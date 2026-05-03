<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubTriggerSeeder extends Seeder
{
    public function run()
    {
        DB::table('sub_triggers')->insert([

            // =========================
            // 🎓 ESCOLA
            // =========================
            [
                'trigger' => 'escola',
                'name' => 'Período de provas',
                'intro_text' => 'É compreensível se sentir pressionada em períodos de prova.',
                'closing_text' => 'Estudar com qualidade é mais importante do que estudar por horas.',
            ],
            [
                'trigger' => 'escola',
                'name' => 'Acúmulo de atividades',
                'intro_text' => 'Quando muitas tarefas se acumulam, é natural se sentir sobrecarregada.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'escola',
                'name' => 'Dificuldade em entender o conteúdo',
                'intro_text' => 'Nem sempre aprender é linear — isso faz parte do processo.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'escola',
                'name' => 'Pressão por desempenho',
                'intro_text' => 'Sentir pressão pode ser pesado, principalmente quando envolve expectativas.',
                'closing_text' => null,
            ],

            // =========================
            // 💼 TRABALHO
            // =========================
            [
                'trigger' => 'trabalho',
                'name' => 'Excesso de tarefas',
                'intro_text' => 'Muitas demandas ao mesmo tempo podem gerar estresse.',
                'closing_text' => 'Priorizar tarefas pode te ajudar a recuperar o controle.',
            ],
            [
                'trigger' => 'trabalho',
                'name' => 'Pressão por resultados',
                'intro_text' => 'A cobrança por resultados pode ser desgastante.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'trabalho',
                'name' => 'Relacionamento com colegas',
                'intro_text' => 'Conflitos no ambiente de trabalho impactam muito o emocional.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'trabalho',
                'name' => 'Cansaço extremo',
                'intro_text' => 'O excesso de trabalho pode levar à exaustão física e mental.',
                'closing_text' => 'Descansar também faz parte da produtividade.',
            ],

            // =========================
            // 👨‍👩‍👧 FAMÍLIA
            // =========================
            [
                'trigger' => 'familia',
                'name' => 'Discussões',
                'intro_text' => 'Conflitos familiares podem ser emocionalmente difíceis.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'familia',
                'name' => 'Falta de apoio',
                'intro_text' => 'Sentir falta de apoio pode gerar frustração e tristeza.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'familia',
                'name' => 'Responsabilidades em casa',
                'intro_text' => 'Muitas responsabilidades podem sobrecarregar.',
                'closing_text' => null,
            ],

            // =========================
            // 🚗 TRÂNSITO
            // =========================
            [
                'trigger' => 'transito',
                'name' => 'Engarrafamento',
                'intro_text' => 'Ficar preso no trânsito pode gerar irritação.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'transito',
                'name' => 'Atrasos',
                'intro_text' => 'A sensação de estar atrasado causa ansiedade.',
                'closing_text' => null,
            ],

            // =========================
            // 🧑‍🤝‍🧑 AMIZADES
            // =========================
            [
                'trigger' => 'amizades',
                'name' => 'Conflitos',
                'intro_text' => 'Desentendimentos com amigos podem ser dolorosos.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'amizades',
                'name' => 'Sentimento de exclusão',
                'intro_text' => 'Se sentir deixada de lado pode impactar a autoestima.',
                'closing_text' => null,
            ],

            // =========================
            // 💰 DINHEIRO
            // =========================
            [
                'trigger' => 'dinheiro',
                'name' => 'Preocupação financeira',
                'intro_text' => 'Problemas financeiros geram muita ansiedade.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'dinheiro',
                'name' => 'Dívidas',
                'intro_text' => 'Lidar com dívidas pode ser angustiante.',
                'closing_text' => null,
            ],

            // =========================
            // ❤️ SAÚDE
            // =========================
            [
                'trigger' => 'saude',
                'name' => 'Problemas físicos',
                'intro_text' => 'A saúde física impacta diretamente o emocional.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'saude',
                'name' => 'Cansaço mental',
                'intro_text' => 'O esgotamento mental é real e precisa de atenção.',
                'closing_text' => 'Respeitar seus limites é essencial.',
            ],

            // =========================
            // 😴 SONO
            // =========================
            [
                'trigger' => 'sono',
                'name' => 'Dormiu mal',
                'intro_text' => 'Uma noite mal dormida afeta todo o dia.',
                'closing_text' => null,
            ],
            [
                'trigger' => 'sono',
                'name' => 'Insônia',
                'intro_text' => 'A dificuldade para dormir pode ser muito frustrante.',
                'closing_text' => null,
            ],

        ]);
    }
}