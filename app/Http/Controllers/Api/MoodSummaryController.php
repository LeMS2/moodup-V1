<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mood;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 🔥 IMPORTANTE: Adicionar esta linha no topo

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
     * 🔥 INSIGHTS (CORRIGIDO - com relacionamento many-to-many)
     */
    public function weeklyInsights(Request $request)
    {
        $userId = $request->user()->id;

        // 🔥 CORREÇÃO: Não selecionar 'triggers' diretamente, usar with() para o relacionamento
        $moods = Mood::query()
            ->where('user_id', $userId)
            ->with('triggers')  // Carrega os triggers via relacionamento many-to-many
            ->orderBy('date')
            ->get(['date', 'level', 'id']);  // Só campos que existem na tabela moods

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

        // 🔥 CORREÇÃO: Coletar triggers do relacionamento many-to-many
        $triggerCounts = [];

        foreach ($filtered as $mood) {
            // Pega os triggers do relacionamento many-to-many via tabela pivô mood_trigger
            foreach ($mood->triggers as $trigger) {
                $triggerName = $trigger->name;
                $triggerCounts[$triggerName] = ($triggerCounts[$triggerName] ?? 0) + 1;
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

    // ============================================================
    // 📊 NOVOS MÉTODOS PARA ESTATÍSTICAS E RELATÓRIOS
    // ============================================================

    /**
     * 📊 Estatísticas de triggers mais usados
     */
    public function topTriggers(Request $request)
    {
        $userId = $request->user()->id;
        $limit = $request->input('limit', 5);
        $days = $request->input('days', 30); // últimos 30 dias por padrão
        
        $startDate = now()->subDays($days);
        
        $topTriggers = DB::table('mood_trigger')
            ->join('moods', 'mood_trigger.mood_id', '=', 'moods.id')
            ->join('triggers', 'mood_trigger.trigger_id', '=', 'triggers.id')
            ->where('moods.user_id', $userId)
            ->where('moods.date', '>=', $startDate)
            ->select('triggers.id', 'triggers.name', DB::raw('count(*) as total'))
            ->groupBy('triggers.id', 'triggers.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'triggers' => $topTriggers,
            'period_days' => $days,
            'total_entries' => Mood::where('user_id', $userId)
                ->where('date', '>=', $startDate)
                ->count()
        ]);
    }

    /**
     * 📚 Recursos mais acessados
     */
    public function topResources(Request $request)
    {
        $userId = $request->user()->id;
        $limit = $request->input('limit', 5);
        $days = $request->input('days', 30);
        
        $startDate = now()->subDays($days);
        
        // Assumindo que você tem uma tabela 'resource_views' ou similar
        $topResources = DB::table('resource_views')
            ->join('resources', 'resource_views.resource_id', '=', 'resources.id')
            ->where('resource_views.user_id', $userId)
            ->where('resource_views.created_at', '>=', $startDate)
            ->select('resources.id', 'resources.title', DB::raw('count(*) as views'))
            ->groupBy('resources.id', 'resources.title')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
        
        // Se não tiver tabela de views, retorna recomendação baseada nos triggers
        if ($topResources->isEmpty()) {
            // Pega os triggers mais comuns e sugere recursos relacionados
            $topTriggers = DB::table('mood_trigger')
                ->join('moods', 'mood_trigger.mood_id', '=', 'moods.id')
                ->join('triggers', 'mood_trigger.trigger_id', '=', 'triggers.id')
                ->where('moods.user_id', $userId)
                ->where('moods.date', '>=', $startDate)
                ->select('triggers.name', DB::raw('count(*) as total'))
                ->groupBy('triggers.name')
                ->orderByDesc('total')
                ->limit(3)
                ->get();
            
            return response()->json([
                'resources' => [],
                'suggestions' => $topTriggers,
                'message' => 'Com base nos seus gatilhos mais frequentes, recomendamos explorar recursos relacionados.'
            ]);
        }
        
        return response()->json([
            'resources' => $topResources,
            'period_days' => $days
        ]);
    }

    /**
     * 📈 Resumo completo de estatísticas
     */
    public function statsOverview(Request $request)
    {
        $userId = $request->user()->id;
        $days = $request->input('days', 30);
        $startDate = now()->subDays($days);
        
        // Média geral
        $avgLevel = Mood::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->avg('level');
        
        // Distribuição por nível
        $levelDistribution = Mood::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();
        
        // Dias com registro
        $daysWithEntries = Mood::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->distinct('date')
            ->count('date');
        
        // Melhor e pior dia (média mais alta e mais baixa)
        $bestDay = Mood::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->select('date', DB::raw('avg(level) as avg_level'))
            ->groupBy('date')
            ->orderByDesc('avg_level')
            ->first();
        
        $worstDay = Mood::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->select('date', DB::raw('avg(level) as avg_level'))
            ->groupBy('date')
            ->orderBy('avg_level')
            ->first();
        
        return response()->json([
            'period_days' => $days,
            'total_entries' => Mood::where('user_id', $userId)->where('date', '>=', $startDate)->count(),
            'days_with_entries' => $daysWithEntries,
            'average_level' => round($avgLevel, 2),
            'level_distribution' => $levelDistribution,
            'best_day' => $bestDay,
            'worst_day' => $worstDay,
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