<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mood;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MoodSummaryController extends Controller
{
    /**
     * 🔥 RESUMO SEMANAL (últimos 7 dias baseado no último registro)
     */
    public function weekly(Request $request)
    {
        $userId = $request->user()->id;

        $lastDate = Mood::where('user_id', $userId)->max('date');

        if (!$lastDate) {
            return response()->json([
                'period' => null,
                'count' => 0,
                'average_level' => null,
                'series' => [],
            ]);
        }

        $end = Carbon::parse($lastDate)->endOfDay();
        $start = $end->copy()->subDays(6)->startOfDay();

        return response()->json(
            $this->buildSummaryWithSeries($request, $start, $end)
        );
    }

    /**
     * 🔥 RESUMO MENSAL (baseado no último registro)
     */
    public function monthly(Request $request)
    {
        $userId = $request->user()->id;

        $lastDate = Mood::where('user_id', $userId)->max('date');

        if (!$lastDate) {
            return response()->json([
                'period' => null,
                'count' => 0,
                'average_level' => null,
                'series' => [],
            ]);
        }

        $end = Carbon::parse($lastDate)->endOfDay();
        $start = $end->copy()->subDays(29)->startOfDay(); // 30 dias

        return response()->json(
            $this->buildSummaryWithSeries($request, $start, $end)
        );
    }

    /**
     * 🔥 INSIGHTS (já corrigido)
     */
    public function weeklyInsights(Request $request)
    {
        $userId = $request->user()->id;

        $moods = Mood::query()
            ->where('user_id', $userId)
            ->orderBy('date')
            ->get(['date', 'level', 'triggers']);

        if ($moods->isEmpty()) {
            return response()->json([
                'series' => [],
                'avg_level_week' => null,
                'low_days' => 0,
                'trend' => null,
                'risk_score' => null,
                'risk_level' => 'desconhecido',
                'top_triggers' => [],
                'alerts' => [],
            ]);
        }

        $lastDate = Carbon::parse($moods->max('date'));
        $start = $lastDate->copy()->subDays(6);

        $filtered = $moods->whereBetween('date', [
            $start->toDateString(),
            $lastDate->toDateString()
        ]);

        $series = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $dayMoods = $filtered->where('date', $day);

            $series[] = [
                'date' => $day,
                'avg_level' => $dayMoods->count()
                    ? round($dayMoods->avg('level'), 2)
                    : null,
                'count' => $dayMoods->count(),
            ];
        }

        $levels = $filtered->pluck('level')->filter();
        $avg = $levels->count() ? $levels->avg() : null;

        $lowDays = collect($series)->filter(
            fn ($d) => $d['avg_level'] !== null && $d['avg_level'] <= 2
        )->count();

        $first3 = collect($series)->take(3)->pluck('avg_level')->filter();
        $last3  = collect($series)->slice(4, 3)->pluck('avg_level')->filter();

        $trend = null;
        if ($first3->count() && $last3->count()) {
            $trend = round($last3->avg() - $first3->avg(), 2);
        }

        $risk = null;

        if ($avg !== null) {
            $riskFromAvg = (5 - $avg) / 4 * 60;
            $riskFromLow = min($lowDays * 10, 30);
            $riskFromTrend = ($trend !== null && $trend < 0)
                ? min(abs($trend) * 10, 10)
                : 0;

            $risk = (int) round(min(100, $riskFromAvg + $riskFromLow + $riskFromTrend));
        }

        $riskLevel = 'desconhecido';

        if ($risk !== null) {
            $riskLevel = $risk >= 70
                ? 'alto'
                : ($risk >= 40 ? 'medio' : 'baixo');
        }

        $triggerCounts = [];

        foreach ($filtered as $m) {
            $t = is_string($m->triggers)
                ? json_decode($m->triggers, true)
                : $m->triggers;

            if (!is_array($t)) continue;

            foreach ($t as $item) {
                $triggerCounts[$item] = ($triggerCounts[$item] ?? 0) + 1;
            }
        }

        arsort($triggerCounts);

        $topTriggers = collect($triggerCounts)
            ->take(5)
            ->map(fn ($count, $trigger) => [
                'trigger' => $trigger,
                'count' => $count
            ])
            ->values();

        $alerts = [];

        if ($riskLevel === 'alto') {
            $alerts[] = [
                'type' => 'critical',
                'title' => 'Semana pesada detectada',
                'message' => 'Seu nível emocional está baixo. Busque apoio.',
            ];
        }

        return response()->json([
            'range' => [
                'start' => $start->toDateString(),
                'end' => $lastDate->toDateString(),
            ],
            'series' => $series,
            'avg_level_week' => $avg ? round($avg, 2) : null,
            'low_days' => $lowDays,
            'trend' => $trend,
            'risk_score' => $risk,
            'risk_level' => $riskLevel,
            'top_triggers' => $topTriggers,
            'alerts' => $alerts,
        ]);
    }

    /**
     * 🔥 RESUMO COM SÉRIE (USADO PELO GRÁFICO)
     */
    private function buildSummaryWithSeries(Request $request, Carbon $start, Carbon $end): array
    {
        $userId = $request->user()->id;

        $moods = Mood::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString()
            ])
            ->orderBy('date')
            ->get(['date', 'level']);

        $series = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $dayMoods = $moods->where('date', $day);

            $series[] = [
                'date' => $day,
                'avg_level' => $dayMoods->count()
                    ? round($dayMoods->avg('level'), 2)
                    : 0,
                'count' => $dayMoods->count(),
            ];
        }

        $count = $moods->count();

        return [
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'count' => $count,
            'average_level' => $count > 0
                ? round($moods->avg('level'), 2)
                : null,
            'series' => $series,
        ];
    }
}