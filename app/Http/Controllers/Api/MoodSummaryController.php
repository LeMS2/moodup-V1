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

    /**
     * NOVO: insights dos últimos 7 dias (gráfico + risco + alertas)
     * NÃO mexe no weekly/monthly existentes.
     */
    public function weeklyInsights(Request $request)
    {
        $userId = $request->user()->id;

        $start = now()->subDays(6)->startOfDay();
        $end   = now()->endOfDay();

        $moods = Mood::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get(['date', 'level', 'triggers']);

        // série com 7 dias fixos (pro gráfico)
        $series = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $dayMoods = $moods->where('date', $day);

            $series[] = [
                'date' => $day,
                'avg_level' => $dayMoods->count() ? round($dayMoods->avg('level'), 2) : null,
                'count' => $dayMoods->count(),
            ];
        }

        $levels = $moods->pluck('level')->filter()->values();
        $avg = $levels->count() ? $levels->avg() : null;

        $lowDays = collect($series)->filter(
            fn ($d) => $d['avg_level'] !== null && $d['avg_level'] <= 2
        )->count();

        // tendência: últimos 3 vs primeiros 3 (simples e defendível no TCC)
        $first3 = collect($series)->take(3)->pluck('avg_level')->filter();
        $last3  = collect($series)->slice(4, 3)->pluck('avg_level')->filter();

        $trend = null;
        if ($first3->count() && $last3->count()) {
            $trend = round($last3->avg() - $first3->avg(), 2); // >0 melhora, <0 piora
        }

        // score de risco 0..100
        $risk = null;
        if ($avg !== null) {
            $riskFromAvg = (5 - $avg) / 4 * 60;     // 0..60
            $riskFromLow = min($lowDays * 10, 30);  // 0..30
            $riskFromTrend = 0;

            if ($trend !== null && $trend < 0) {
                $riskFromTrend = min(abs($trend) * 10, 10); // 0..10
            }

            $risk = (int) round(min(100, $riskFromAvg + $riskFromLow + $riskFromTrend));
        }

        $riskLevel = 'desconhecido';
        if ($risk !== null) {
            $riskLevel = $risk >= 70 ? 'alto' : ($risk >= 40 ? 'medio' : 'baixo');
        }

        // top triggers
        $triggerCounts = [];
        foreach ($moods as $m) {
            $t = $m->triggers;

            // triggers pode vir array (cast) ou string JSON
            if (is_string($t)) $t = json_decode($t, true);
            if (!is_array($t)) $t = [];

            foreach ($t as $item) {
                $triggerCounts[$item] = ($triggerCounts[$item] ?? 0) + 1;
            }
        }
        arsort($triggerCounts);

        $topTriggers = [];
        foreach (array_slice(array_keys($triggerCounts), 0, 5) as $k) {
            $topTriggers[] = ['trigger' => $k, 'count' => $triggerCounts[$k]];
        }

        // alertas (o front decide como exibir)
        $alerts = [];
        if ($riskLevel === 'alto') {
            $alerts[] = [
                'type' => 'critical',
                'title' => 'Semana pesada detectada',
                'message' => 'Seu score de risco está alto. Se precisar, considere buscar apoio (CVV/psicóloga) e use um exercício rápido agora.',
            ];
        } elseif ($riskLevel === 'medio') {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Atenção à sua rotina',
                'message' => 'Você teve alguns dias difíceis. Que tal uma prática leve hoje?',
            ];
        }

        return response()->json([
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'series' => $series,
            'avg_level_week' => $avg !== null ? round($avg, 2) : null,
            'low_days' => $lowDays,
            'trend' => $trend,
            'risk_score' => $risk,
            'risk_level' => $riskLevel,
            'top_triggers' => $topTriggers,
            'alerts' => $alerts,
        ]);
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

        $best = null;
        $worst = null;
        $topBest = [];
        $topWorst = [];

        if ($count > 0) {
            // ✅ Corrigido: sort por level e desempate por data (sem “duplo sortBy” que quebra)
            $bestSorted = $moods->sort(function ($a, $b) {
                if ($a->level === $b->level) return strcmp((string)$b->date, (string)$a->date); // mais recente primeiro
                return $b->level <=> $a->level; // level maior primeiro
            })->values();

            $worstSorted = $moods->sort(function ($a, $b) {
                if ($a->level === $b->level) return strcmp((string)$b->date, (string)$a->date); // mais recente primeiro
                return $a->level <=> $b->level; // level menor primeiro
            })->values();

            $bestMood = $bestSorted->first();
            $best = [
                'date' => $bestMood->date,
                'level' => $bestMood->level,
                'note' => $bestMood->note,
            ];

            if ($count === 1) {
                $worst = null;
                $topWorst = [];
            } else {
                $worstMood = $worstSorted->first();
                $worst = [
                    'date' => $worstMood->date,
                    'level' => $worstMood->level,
                    'note' => $worstMood->note,
                ];

                $topWorst = $worstSorted->take(3)->map(function ($m) {
                    return ['date' => $m->date, 'level' => $m->level, 'note' => $m->note];
                })->values()->all();
            }

            $topBest = $bestSorted->take(3)->map(function ($m) {
                return ['date' => $m->date, 'level' => $m->level, 'note' => $m->note];
            })->values()->all();
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