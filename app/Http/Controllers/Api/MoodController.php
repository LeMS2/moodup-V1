<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMoodRequest;
use App\Http\Requests\UpdateMoodRequest;
use App\Models\Mood;
use Illuminate\Http\Request;
use App\Http\Resources\MoodResource;

class MoodController extends Controller
{
    public function index(Request $request)
    {
        $query = Mood::query()
            ->where('user_id', $request->user()->id)
            ->with(['categories', 'triggers']); // 🔥 inclui triggers

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
        $user = $request->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        // ❌ garante que não salva triggers antigo
        unset($data['triggers']);

        $mood = Mood::create($data);

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

        if (!empty($triggerIds)) {
            $mood->triggers()->sync($triggerIds);
        }

        $mood->load(['categories', 'triggers']);

        // =========================
        // 🤖 RECOMENDAÇÃO
        // =========================
        $rec = app(\App\Http\Controllers\Api\ResourceController::class)
            ->recommend(new Request([
                'level' => $mood->level,
                'triggers' => $mood->triggers->pluck('name')->toArray(), // 🔥 corrigido
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
            $mood->load(['categories', 'triggers']) // 🔥 inclui triggers
        );
    }

    public function update(UpdateMoodRequest $request, Mood $mood)
    {
        $this->authorizeMood($request, $mood);

        $data = $request->validated();

        unset($data['triggers']); // ❌ remove campo antigo

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