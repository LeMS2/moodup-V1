<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubTrigger;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;

class SubTriggerResourceSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 (opcional) limpa antes de popular
        DB::table('sub_trigger_resource')->truncate();

        // 🔹 função helper (closure, sem warning)
        $attachResources = function ($subTriggerName, array $resourceTitles) {

            $sub = SubTrigger::where('name', $subTriggerName)->first();

            if (!$sub) {
                return;
            }

            $resources = Resource::whereIn('title', $resourceTitles)
                ->pluck('id')
                ->toArray();

            if (!empty($resources)) {
                $sub->resources()->syncWithoutDetaching($resources);
            }
        };

        // =========================
        // 💼 TRABALHO
        // =========================
        $attachResources('Excesso de tarefas', [
            'Respiração guiada (5 min)',
            'Mini pausa respiração',
            'Playlist foco e calma',
        ]);

        $attachResources('Cansaço extremo', [
            'Relaxamento muscular',
            'Música para dormir',
        ]);

        // =========================
        // 🎓 ESCOLA
        // =========================
        $attachResources('Período de provas', [
            'Lo-fi para estudar',
            'Planejamento do dia',
        ]);

        $attachResources('Acúmulo de atividades', [
            'Journaling 3 coisas do dia',
            'Playlist foco e calma',
        ]);

        // =========================
        // 👨‍👩‍👧 FAMÍLIA
        // =========================
        $attachResources('Discussões', [
            'Respiração guiada (5 min)',
            'Técnica grounding 5-4-3-2-1',
        ]);

        // =========================
        // 😴 SONO
        // =========================
        $attachResources('Insônia', [
            'Música para dormir',
            'Sons da natureza',
        ]);

        // =========================
        // 🧠 SAÚDE
        // =========================
        $attachResources('Cansaço mental', [
            'Meditação curta (3 min)',
            'Relaxamento muscular',
        ]);
    }
}