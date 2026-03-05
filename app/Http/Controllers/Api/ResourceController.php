<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Mood;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $q = Resource::query()->where('is_active', true);

        if ($request->filled('type')) {
            $q->where('type', $request->string('type'));
        }

        if ($request->filled('search')) {
            $s = (string) $request->input('search');
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('author', 'like', "%{$s}%");
            });
        }

        return $q->orderBy('type')->orderBy('title')->paginate(20);
    }

    /**
     * Recomendação baseada no nível + triggers enviados pelo front (ex: após criar mood).
     */
    public function recommend(Request $request)
    {
        $level = (int) $request->input('level', 3);
        $triggers = (array) $request->input('triggers', []);

        $preferredTypes = ($level <= 2)
            ? ['exercicio', 'video', 'musica']
            : ['musica', 'video', 'livro', 'exercicio'];

        $wantedTags = [];

        if ($level <= 2) {
            $wantedTags = array_merge($wantedTags, ['ansiedade', 'calma', 'respiracao', 'relaxamento']);
        }

        $map = [
            'Escola/Faculdade' => ['estudo', 'foco', 'calma'],
            'Trabalho' => ['stress', 'foco', 'calma'],
            'Família' => ['autoconhecimento', 'calma'],
            'Amizades' => ['autoconhecimento', 'calma'],
            'Trânsito' => ['calma', 'respiracao'],
            'Dinheiro' => ['stress', 'calma'],
            'Saúde' => ['bem-estar', 'calma'],
            'Sono' => ['sono', 'relaxamento'],
        ];

        foreach ($triggers as $t) {
            if (isset($map[$t])) {
                $wantedTags = array_merge($wantedTags, $map[$t]);
            }
        }

        $wantedTags = array_values(array_unique($wantedTags));

        $q = Resource::query()
            ->where('is_active', true)
            ->whereIn('type', $preferredTypes);

        if (!empty($wantedTags)) {
            $q->where(function ($qq) use ($wantedTags) {
                foreach ($wantedTags as $tag) {
                    $qq->orWhereJsonContains('tags', $tag);
                }
            });
        }

        $resource = $q->inRandomOrder()->first();

        if (!$resource) {
            $resource = Resource::query()
                ->where('is_active', true)
                ->inRandomOrder()
                ->first();
        }

        return response()->json([
            'recommendation' => $resource,
            'debug' => [
                'mode' => 'by_level',
                'level' => $level,
                'triggers' => $triggers,
                'wantedTags' => $wantedTags,
                'preferredTypes' => $preferredTypes,
            ],
        ]);
    }

    /**
     * NOVO: recomendação baseada no histórico dos últimos 7 dias (TCC forte).
     * - calcula média semanal
     * - detecta "dias ruins"
     * - usa triggers mais frequentes como contexto
     */
    public function recommendByHistory(Request $request)
    {
        $userId = $request->user()->id;

        $start = Carbon::now()->subDays(6)->startOfDay()->toDateString();
        $end = Carbon::now()->endOfDay()->toDateString();

        $moods = Mood::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get(['level', 'triggers', 'date']);

        $levels = $moods->pluck('level')->filter()->values();
        $avg = $levels->count() ? round($levels->avg(), 2) : 3.0;

        // conta quantos registros com level <= 2 na semana
        $lowCount = $moods->filter(fn ($m) => (int)$m->level <= 2)->count();

        // pega triggers mais frequentes (top 3)
        $triggerCounts = [];
        foreach ($moods as $m) {
            $t = $m->triggers;

            if (is_string($t)) $t = json_decode($t, true);
            if (!is_array($t)) $t = [];

            foreach ($t as $item) {
                $triggerCounts[$item] = ($triggerCounts[$item] ?? 0) + 1;
            }
        }
        arsort($triggerCounts);
        $topTriggers = array_slice(array_keys($triggerCounts), 0, 3);

        // risco simplificado baseado em média + lows
        $riskFromAvg = (5 - $avg) / 4 * 60;        // 0..60
        $riskFromLow = min($lowCount * 8, 40);     // 0..40
        $risk = (int) round(min(100, $riskFromAvg + $riskFromLow));

        $riskLevel = $risk >= 70 ? 'alto' : ($risk >= 40 ? 'medio' : 'baixo');

        // define tipos e tags pelo cenário
        $preferredTypes = ['musica', 'video', 'livro', 'exercicio'];
        $wantedTags = [];

        if ($riskLevel === 'alto') {
            $preferredTypes = ['exercicio', 'video', 'musica'];
            $wantedTags = ['ansiedade', 'respiracao', 'calma', 'relaxamento', 'sono'];
        } elseif ($riskLevel === 'medio') {
            $preferredTypes = ['musica', 'exercicio', 'video'];
            $wantedTags = ['calma', 'foco', 'rotina', 'bem-estar'];
        } else {
            $preferredTypes = ['livro', 'musica', 'exercicio', 'video'];
            $wantedTags = ['habitos', 'rotina', 'motivacao', 'bem-estar'];
        }

        // reforça tags com base nos triggers mais frequentes
        $map = [
            'Escola/Faculdade' => ['estudo', 'foco', 'calma'],
            'Trabalho' => ['stress', 'foco', 'calma'],
            'Família' => ['autoconhecimento', 'calma'],
            'Amizades' => ['autoconhecimento', 'calma'],
            'Trânsito' => ['calma', 'respiracao'],
            'Dinheiro' => ['stress', 'calma'],
            'Saúde' => ['bem-estar', 'calma'],
            'Sono' => ['sono', 'relaxamento'],
        ];

        foreach ($topTriggers as $t) {
            if (isset($map[$t])) {
                $wantedTags = array_merge($wantedTags, $map[$t]);
            }
        }

        $wantedTags = array_values(array_unique($wantedTags));

        // busca por tags + tipo
        $q = Resource::query()
            ->where('is_active', true)
            ->whereIn('type', $preferredTypes);

        if (!empty($wantedTags)) {
            $q->where(function ($qq) use ($wantedTags) {
                foreach ($wantedTags as $tag) {
                    $qq->orWhereJsonContains('tags', $tag);
                }
            });
        }

        $resource = $q->inRandomOrder()->first();

        // fallback final
        if (!$resource) {
            $resource = Resource::query()
                ->where('is_active', true)
                ->inRandomOrder()
                ->first();
        }

        return response()->json([
            'recommendation' => $resource,
            'meta' => [
                'mode' => 'by_history',
                'range' => ['start' => $start, 'end' => $end],
                'avg_level_week' => $avg,
                'low_count' => $lowCount,
                'risk_score' => $risk,
                'risk_level' => $riskLevel,
                'top_triggers' => $topTriggers,
                'preferredTypes' => $preferredTypes,
                'wantedTags' => $wantedTags,
            ],
        ]);
    }
}