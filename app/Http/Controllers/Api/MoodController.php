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
        ->with('categories');

    // filtro por período
    if ($request->filled('start_date')) {
        $query->whereDate('date', '>=', $request->input('start_date'));
    }
    if ($request->filled('end_date')) {
        $query->whereDate('date', '<=', $request->input('end_date'));
    }

    // ✅ filtro por categoria (many-to-many)
if ($request->filled('category_id')) {

    $categoryId = (int) $request->input('category_id');

    // 🔒 garante que a categoria pertence ao usuário
    $exists = \App\Models\Category::where('id', $categoryId)
        ->where('user_id', $request->user()->id)
        ->exists();

    if (!$exists) {
        return response()->json(['message' => 'Categoria inválida.'], 422);
    }

    // 🔎 aplica o filtro
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

    $mood = Mood::create([
        'user_id' => $user->id,
        'date'    => $request->date,
        'level'   => $request->level,
        'note'    => $request->note,
    ]);

     // 🔹 AQUI entra o código do passo 5B
    $categoryIds = $request->input('category_ids', []);

    if (!empty($categoryIds)) {
        $validIds = \App\Models\Category::where('user_id', $user->id)
            ->whereIn('id', $categoryIds)
            ->pluck('id')
            ->all();

        $mood->categories()->sync($validIds);
    }

    $mood->load('categories');
return (new MoodResource($mood))->response()->setStatusCode(201);
}

    public function show(Request $request, Mood $mood)
    {
        $this->authorizeMood($request, $mood);
        return new MoodResource($mood->load('categories'));
    }

    public function update(UpdateMoodRequest $request, Mood $mood)
{
    $this->authorizeMood($request, $mood);

    $mood->update($request->validated());

    // 🔹 AQUI entra o código do passo 5C
    if ($request->has('category_ids')) {

        $categoryIds = $request->input('category_ids', []);

        $validIds = \App\Models\Category::where('user_id', $request->user()->id)
            ->whereIn('id', $categoryIds)
            ->pluck('id')
            ->all();

        $mood->categories()->sync($validIds);
    }

    return new MoodResource($mood->load('categories'));
}

    public function destroy(Request $request, Mood $mood)
    {
        $this->authorizeMood($request, $mood);

        $mood->delete();

        return response()->json(['message' => 'Registro removido com sucesso.']);
    }

    private function authorizeMood(Request $request, Mood $mood): void
    {
        if ($mood->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para acessar este registro.');
        }
    }
}