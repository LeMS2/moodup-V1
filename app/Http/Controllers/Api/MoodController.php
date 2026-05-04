<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMoodRequest;
use App\Http\Requests\UpdateMoodRequest;
use App\Models\Mood;
use Illuminate\Http\Request;
use App\Http\Resources\MoodResource;
use Illuminate\Support\Facades\Log; // 🔥 IMPORTAR O LOG

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
        // 🔥 LOG 1: Ver todos os dados que chegaram
        Log::info('🔍 DADOS CRUS RECEBIDOS:', $request->all());
        
        $user = $request->user();
        
        // 🔥 LOG 2: Ver usuário autenticado
        Log::info('👤 USUÁRIO:', ['id' => $user->id, 'email' => $user->email]);
        
        $data = $request->validated();
        
        // 🔥 LOG 3: Ver dados depois da validação
        Log::info('✅ DADOS VALIDADOS:', $data);
        
        $data['user_id'] = $user->id;

        // ❌ garante que não salva triggers antigo
        unset($data['triggers']);
        unset($data['trigger_ids']);
        
        // 🔥 LOG 4: Ver dados que serão salvos
        Log::info('💾 DADOS PARA CREATE:', $data);

        $mood = Mood::create($data);
        
        // 🔥 LOG 5: Ver o que foi salvo
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
        }

        // =========================
        // 🔥 TRIGGERS (NOVO)
        // =========================
        $triggerIds = $request->input('trigger_ids', []);
        
        // 🔥 LOG 6: Ver triggers recebidos
        Log::info('🔗 TRIGGER IDs RECEBIDOS:', $triggerIds);

        if (!empty($triggerIds)) {
            $mood->triggers()->sync($triggerIds);
            Log::info('✅ TRIGGERS SINCRONIZADOS:', $triggerIds);
        } else {
            Log::warning('⚠️ NENHUM TRIGGER ID RECEBIDO');
        }

        $mood->load(['categories', 'triggers']);
        
        // 🔥 LOG 7: Ver resultado final
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
        $this->authorizeMood($request, $mood);

        $data = $request->validated();

        unset($data['triggers']);

        $mood->update($data);

        // =========================
        // 📌 CATEGORIAS
        // =========================
        if ($request->has('category_ids')) {

            $categoryIds = $request->input('category_ids', []);

            $validIds = \App\Models\Category::where('user_id', $request->user()->id)
                ->whereIn('id', $categoryIds)
                ->pluck('id')
                ->all();

            $mood->categories()->sync($validIds);
        }

        // =========================
        // 🔥 TRIGGERS
        // =========================
        if ($request->has('trigger_ids')) {

            $triggerIds = $request->input('trigger_ids', []);

            $mood->triggers()->sync($triggerIds);
        }

        return new MoodResource(
            $mood->load(['categories', 'triggers'])
        );
    }

    public function destroy(Request $request, Mood $mood)
    {
        $this->authorizeMood($request, $mood);

        $mood->delete();

        return response()->json([
            'message' => 'Registro removido com sucesso.',
        ]);
    }

    private function authorizeMood(Request $request, Mood $mood): void
    {
        if ($mood->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para acessar este registro.');
        }
    }
}