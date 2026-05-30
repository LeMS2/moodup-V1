<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMoodRequest;
use App\Http\Requests\UpdateMoodRequest;
use App\Models\Mood;
use Illuminate\Http\Request;
use App\Http\Resources\MoodResource;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;

class MoodController extends Controller
{
    public function index(Request $request)
    {
        $query = Mood::query()
            ->where('user_id', $request->user()->id)
            ->with(['categories', 'triggers']);

        // filtro por período
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->input('end_date'));
        }

        // filtro por categoria
        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            $exists = \App\Models\Category::where('id', $categoryId)
                ->where('user_id', $request->user()->id)
                ->exists();

            if (!$exists) {
                return response()->json(['message' => 'Categoria inválida.'], 422);
            }

            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        return MoodResource::collection(
            $query->orderByDesc('date')->paginate(10)
        );
    }

    public function store(StoreMoodRequest $request)
    {
        Log::info('🔍 DADOS CRUS RECEBIDOS:', $request->all());
        
        $user = $request->user();
        
        Log::info('👤 USUÁRIO:', ['id' => $user->id, 'email' => $user->email]);
        
        $data = $request->validated();
        
        Log::info('✅ DADOS VALIDADOS:', $data);
        
        $data['user_id'] = $user->id;

        // Remove campos que não devem ser salvos diretamente
        unset($data['triggers']);
        unset($data['trigger_ids']);
        unset($data['category_ids']);
        
        Log::info('💾 DADOS PARA CREATE:', $data);

        $mood = Mood::create($data);

        // TIRA SE DER ERRADO

        ActivityLog::create([
    'user_id' => $user->id,
    'action' => 'CREATE_MOOD',
    'description' => 'Criou um registro de humor: ' . $mood->title,
    'ip_address' => $request->ip(),
]);
        
        Log::info('🎉 MOOD CRIADO:', $mood->toArray());

        // =========================
        // 📌 CATEGORIAS
        // =========================
        $categoryIds = $request->input('category_ids', []);

        if (!empty($categoryIds)) {
            $validIds = \App\Models\Category::where('user_id', $user->id)
                ->whereIn('id', $categoryIds)
                ->pluck('id')
                ->all();

            $mood->categories()->sync($validIds);
            Log::info('✅ CATEGORIAS SINCRONIZADAS:', $validIds);
        }

        // =========================
        // 🔥 TRIGGERS
        // =========================
        $triggerIds = $request->input('trigger_ids', []);
        
        Log::info('🔗 TRIGGER IDs RECEBIDOS:', $triggerIds);

        if (!empty($triggerIds)) {
            // Verifica se os triggers existem
            $validTriggerIds = \App\Models\Trigger::whereIn('id', $triggerIds)
                ->pluck('id')
                ->all();
                
            $mood->triggers()->sync($validTriggerIds);
            Log::info('✅ TRIGGERS SINCRONIZADOS:', $validTriggerIds);
        } else {
            Log::warning('⚠️ NENHUM TRIGGER ID RECEBIDO');
        }

        $mood->load(['categories', 'triggers']);
        
        Log::info('📋 MOOD FINAL COM TRIGGERS:', $mood->toArray());

        // =========================
        // 🤖 RECOMENDAÇÃO
        // =========================
        $rec = app(\App\Http\Controllers\Api\ResourceController::class)
            ->recommend(new Request([
                'level' => $mood->level,
                'triggers' => $mood->triggers->pluck('name')->toArray(),
            ]))->getData(true)['recommendation'] ?? null;

        return response()->json([
            'mood' => new MoodResource($mood),
            'recommendation' => $rec,
        ], 201);
    }

    public function show(Request $request, Mood $mood)
    {
        $this->authorizeMood($request, $mood);

        return new MoodResource(
            $mood->load(['categories', 'triggers'])
        );
    }

    public function update(UpdateMoodRequest $request, Mood $mood)
    {
        Log::info('✏️ ATUALIZANDO MOOD:', ['id' => $mood->id]);
        Log::info('📥 DADOS RECEBIDOS:', $request->all());
        
        $this->authorizeMood($request, $mood);

        $data = $request->validated();
        
        Log::info('✅ DADOS VALIDADOS PARA UPDATE:', $data);

        // Remove campos que não devem ser atualizados diretamente
        unset($data['triggers']);
        unset($data['trigger_ids']);
        unset($data['category_ids']);
        unset($data['user_id']); // Impede mudança de usuário

        $mood->update($data);

        // TIRA SE DER ERRADO

        ActivityLog::create([
    'user_id' => $request->user()->id,
    'action' => 'UPDATE_MOOD',
    'description' => 'Editou o humor ID ' . $mood->id,
    'ip_address' => $request->ip(),
]);
        
        Log::info('💾 MOOD ATUALIZADO:', $mood->fresh()->toArray());

        // =========================
        // 📌 CATEGORIAS
        // =========================
        if ($request->has('category_ids')) {
            $categoryIds = $request->input('category_ids', []);

            if (!empty($categoryIds)) {
                $validIds = \App\Models\Category::where('user_id', $request->user()->id)
                    ->whereIn('id', $categoryIds)
                    ->pluck('id')
                    ->all();

                $mood->categories()->sync($validIds);
                Log::info('✅ CATEGORIAS ATUALIZADAS:', $validIds);
            } else {
                $mood->categories()->sync([]);
                Log::info('🗑️ CATEGORIAS REMOVIDAS');
            }
        }

        // =========================
        // 🔥 TRIGGERS
        // =========================
        if ($request->has('trigger_ids')) {
            $triggerIds = $request->input('trigger_ids', []);

            if (!empty($triggerIds)) {
                $validTriggerIds = \App\Models\Trigger::whereIn('id', $triggerIds)
                    ->pluck('id')
                    ->all();
                    
                $mood->triggers()->sync($validTriggerIds);
                Log::info('✅ TRIGGERS ATUALIZADOS:', $validTriggerIds);
            } else {
                $mood->triggers()->sync([]);
                Log::info('🗑️ TRIGGERS REMOVIDOS');
            }
        }

        $mood->load(['categories', 'triggers']);
        
        Log::info('📋 MOOD FINAL APÓS UPDATE:', $mood->toArray());

        return new MoodResource($mood);
    }

    public function destroy(Request $request, Mood $mood)
    {
        Log::info('🗑️ DELETANDO MOOD:', ['id' => $mood->id, 'user_id' => $mood->user_id]);
        
        $this->authorizeMood($request, $mood);

        // Remove relacionamentos primeiro (opcional, dependendo das constraints do banco)
        $mood->categories()->sync([]);
        $mood->triggers()->sync([]);
        
        $mood->delete();

        // TIRA SE DER ERRADO
        
        ActivityLog::create([
    'user_id' => $request->user()->id,
    'action' => 'DELETE_MOOD',
    'description' => 'Removeu o humor ID ' . $mood->id,
    'ip_address' => $request->ip(),
]);

        return response()->json([
            'message' => 'Registro removido com sucesso.',
        ], 200);
    }

    /**
     * Get summary of moods for dashboard
     */
    public function summary(Request $request)
    {
        $userId = $request->user()->id;
        
        $query = Mood::where('user_id', $userId);
        
        // Filtro por período
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->input('start_date'));
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->input('end_date'));
        }
        
        $moods = $query->get();
        
        return response()->json([
            'total' => $moods->count(),
            'average_level' => $moods->avg('level'),
            'by_level' => [
                1 => $moods->where('level', 1)->count(),
                2 => $moods->where('level', 2)->count(),
                3 => $moods->where('level', 3)->count(),
                4 => $moods->where('level', 4)->count(),
                5 => $moods->where('level', 5)->count(),
            ],
            'latest' => MoodResource::collection($moods->take(5)),
        ]);
    }

    /**
     * Get weekly summary
     */
    public function weeklySummary(Request $request)
    {
        $userId = $request->user()->id;
        
        $endDate = $request->input('end_date', now());
        $startDate = $request->input('start_date', now()->subDays(6));
        
        $moods = Mood::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
        
        $series = [];
        $currentDate = clone $startDate;
        
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $currentDate->toDateString();
            $dayMoods = $moods->where('date', $dateStr);
            
            $series[] = [
                'date' => $dateStr,
                'avg_level' => $dayMoods->count() ? round($dayMoods->avg('level'), 2) : null,
                'count' => $dayMoods->count(),
            ];
            
            $currentDate->addDay();
        }
        
        return response()->json([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'count' => $moods->count(),
            'average_level' => $moods->count() ? round($moods->avg('level'), 2) : null,
            'series' => $series,
        ]);
    }

    private function authorizeMood(Request $request, Mood $mood): void
    {
        if ($mood->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para acessar este registro.');
        }
    }
}