<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuggestionSeeder extends Seeder
{
    public function run()
    {
        // helper pra pegar ID
        function sub($name) {
            return DB::table('sub_triggers')->where('name', $name)->value('id');
        }

        DB::table('suggestions')->insert([

            // 🎓 PERÍODO DE PROVAS
            [
                'sub_trigger_id' => sub('Período de provas'),
                'text' => 'teste menores',
            ],
            [
                'sub_trigger_id' => sub('Período de provas'),
                'text' => 'Use ciclos de estudo (25min + pausa)',
            ],
            [
                'sub_trigger_id' => sub('Período de provas'),
                'text' => 'Revise ao invés de começar do zero',
            ],
            [
                'sub_trigger_id' => sub('Período de provas'),
                'text' => 'Priorize o mais importante',
            ],

            // 🎓 ACÚMULO DE ATIVIDADES
            [
                'sub_trigger_id' => sub('Acúmulo de atividades'),
                'text' => 'Liste tudo que precisa fazer',
            ],
            [
                'sub_trigger_id' => sub('Acúmulo de atividades'),
                'text' => 'Organize por prioridade',
            ],
            [
                'sub_trigger_id' => sub('Acúmulo de atividades'),
                'text' => 'Faça uma coisa de cada vez',
            ],

            // 🎓 DIFICULDADE
            [
                'sub_trigger_id' => sub('Dificuldade em entender o conteúdo'),
                'text' => 'Procure vídeos ou explicações alternativas',
            ],
            [
                'sub_trigger_id' => sub('Dificuldade em entender o conteúdo'),
                'text' => 'Peça ajuda a colegas ou professores',
            ],
            [
                'sub_trigger_id' => sub('Dificuldade em entender o conteúdo'),
                'text' => 'Revise o básico antes de avançar',
            ],

            // 🎓 PRESSÃO
            [
                'sub_trigger_id' => sub('Pressão por desempenho'),
                'text' => 'Evite se comparar com outras pessoas',
            ],
            [
                'sub_trigger_id' => sub('Pressão por desempenho'),
                'text' => 'Faça o melhor possível dentro do seu limite',
            ],

            // 💼 TRABALHO
            [
                'sub_trigger_id' => sub('Excesso de tarefas'),
                'text' => 'Quebre tarefas grandes em pequenas',
            ],
            [
                'sub_trigger_id' => sub('Excesso de tarefas'),
                'text' => 'Evite multitarefa',
            ],

            [
                'sub_trigger_id' => sub('Pressão por resultados'),
                'text' => 'Foque no processo, não só no resultado',
            ],

            [
                'sub_trigger_id' => sub('Relacionamento com colegas'),
                'text' => 'Tente uma comunicação clara e calma',
            ],

            [
                'sub_trigger_id' => sub('Cansaço extremo'),
                'text' => 'Respeite seu tempo de descanso',
            ],

            // 👨‍👩‍👧 FAMÍLIA
            [
                'sub_trigger_id' => sub('Discussões'),
                'text' => 'Evite discutir no momento de raiva',
            ],
            [
                'sub_trigger_id' => sub('Discussões'),
                'text' => 'Dê um tempo antes de responder',
            ],

            [
                'sub_trigger_id' => sub('Falta de apoio'),
                'text' => 'Busque apoio em amigos ou pessoas confiáveis',
            ],

            // 🚗 TRÂNSITO
            [
                'sub_trigger_id' => sub('Engarrafamento'),
                'text' => 'Use esse tempo para ouvir algo relaxante',
            ],
            [
                'sub_trigger_id' => sub('Atrasos'),
                'text' => 'Avise antecipadamente para reduzir ansiedade',
            ],

            // 🧑‍🤝‍🧑 AMIZADES
            [
                'sub_trigger_id' => sub('Conflitos'),
                'text' => 'Tente ouvir antes de reagir',
            ],
            [
                'sub_trigger_id' => sub('Sentimento de exclusão'),
                'text' => 'Nem sempre é pessoal, tente conversar',
            ],

            // 💰 DINHEIRO
            [
                'sub_trigger_id' => sub('Preocupação financeira'),
                'text' => 'Organize seus gastos',
            ],
            [
                'sub_trigger_id' => sub('Dívidas'),
                'text' => 'Crie um plano simples de pagamento',
            ],

            // ❤️ SAÚDE
            [
                'sub_trigger_id' => sub('Problemas físicos'),
                'text' => 'Procure orientação médica quando necessário',
            ],
            [
                'sub_trigger_id' => sub('Cansaço mental'),
                'text' => 'Faça pausas ao longo do dia',
            ],

            // 😴 SONO
            [
                'sub_trigger_id' => sub('Dormiu mal'),
                'text' => 'Evite telas antes de dormir',
            ],
            [
                'sub_trigger_id' => sub('Insônia'),
                'text' => 'Crie uma rotina de sono',
            ],

        ]);
    }
}
