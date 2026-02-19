<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Mood;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MoodSummaryController extends Controller
{
    public function weekly(Request $request)
    {
        // semana atual (seg-dom) por padrão
        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfWeek();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfWeek();

        return response()->json($this->buildSummary($request, $start, $end));
    }

    public function monthly(Request $request)
    {
        // mês atual por padrão
        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfMonth();

        return response()->json($this->buildSummary($request, $start, $end));
    }

    private function buildSummary(Request $request, Carbon $start, Carbon $end): array
    {
        $userId = $request->user()->id;

        // filtros
        $categoryId = null;
        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            $exists = Category::where('id', $categoryId)
                ->where('user_id', $userId)
                ->exists();

            if (!$exists) {
                abort(422, 'Categoria inválida.');
            }
        }

        // query dos moods (com filtro opcional por categoria)
        $moods = Mood::query()
            ->where('user_id', $userId)
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('categories', function ($qq) use ($categoryId) {
                    $qq->where('categories.id', $categoryId);
                });
            })
            ->orderBy('date')
            ->get(['id', 'date', 'level', 'note']);

        $count = $moods->count();

        $average = $count > 0
            ? round($moods->avg('level'), 2)
            : null;

        // distribuição por level (1..5)
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[(string) $i] = $moods->where('level', $i)->count();
        }

        // melhor/pior dia e tops
        $best = null;
        $worst = null;
        $topBest = [];
        $topWorst = [];

        if ($count > 0) {
            // Empate resolvido por data mais recente:
            // (ordenamos por date desc primeiro e depois por level)
            $bestSorted = $moods->sortByDesc('date')->sortByDesc('level')->values();
            $worstSorted = $moods->sortByDesc('date')->sortBy('level')->values();

            $bestMood = $bestSorted->first();
            $best = [
                'date' => $bestMood->date,
                'level' => $bestMood->level,
                'note' => $bestMood->note,
            ];

            // Upgrade 1: se só tem 1 registro, worst_day = null
            if ($count === 1) {
                $worst = null;
            } else {
                $worstMood = $worstSorted->first();
                $worst = [
                    'date' => $worstMood->date,
                    'level' => $worstMood->level,
                    'note' => $worstMood->note,
                ];
            }

            // Upgrade 2: Top 3 melhores
            $topBest = $bestSorted->take(3)->map(function ($m) {
                return [
                    'date' => $m->date,
                    'level' => $m->level,
                    'note' => $m->note,
                ];
            })->values()->all();

            // Upgrade extra: se count=1, top_worst_days vazio pra não confundir
            if ($count === 1) {
                $topWorst = [];
            } else {
                $topWorst = $worstSorted->take(3)->map(function ($m) {
                    return [
                        'date' => $m->date,
                        'level' => $m->level,
                        'note' => $m->note,
                    ];
                })->values()->all();
            }
        }

        return [
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'filters' => [
                'category_id' => $categoryId,
            ],
            'count' => $count,
            'average_level' => $average,
            'distribution' => $distribution,
            'best_day' => $best,
            'worst_day' => $worst,
            'top_best_days' => $topBest,
            'top_worst_days' => $topWorst,
        ];
    }
}