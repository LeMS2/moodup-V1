<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Período de provas' => [
                'Quando a quantidade de conteúdo parece grande demais, manter o foco por longos períodos pode se tornar cansativo. O método Pomodoro consiste em estudar por 25 minutos com atenção total e fazer uma pausa curta de 5 minutos antes de recomeçar.',
                'Em vez de apenas reler o conteúdo, tente responder perguntas sobre o tema ou explicá-lo com suas próprias palavras. Essa estratégia ajuda a fortalecer a memória e identificar quais assuntos ainda precisam de mais atenção.',
                'Criar um cronograma simples pode ajudar a organizar melhor o tempo disponível. Distribuir os conteúdos ao longo dos dias costuma reduzir a ansiedade causada pela sensação de que há muito para estudar.',
                'Durante períodos de prova, tente reconhecer também os avanços realizados durante a preparação. Cada sessão de estudo, exercício resolvido ou conteúdo revisado representa um passo importante no processo de aprendizagem.',
            ],

            'Acúmulo de atividades' => [
                'Quando há muitas atividades para fazer, anotar tudo em uma lista simples pode ajudar a visualizar melhor a situação e identificar por onde começar.',
                'Tarefas grandes costumam parecer mais difíceis do que realmente são. Dividi-las em pequenas etapas pode tornar o processo mais leve e facilitar o progresso ao longo do dia.',
                'Uma estratégia simples é separar as atividades entre urgentes, importantes e aquelas que podem esperar um pouco mais. Isso ajuda a direcionar energia para o que realmente precisa de atenção primeiro.',
                'Quando existem muitas responsabilidades ao mesmo tempo, concentrar-se em concluir uma tarefa antes de iniciar outra pode reduzir a sobrecarga mental e aumentar a sensação de progresso.',
            ],

            'Dificuldade em entender o conteúdo' => [
                'Nem sempre uma explicação funciona para todas as pessoas. Buscar vídeos, exercícios resolvidos, resumos ou exemplos práticos pode ajudar a enxergar o conteúdo por uma nova perspectiva.',
                'A Técnica de Feynman consiste em tentar explicar o conteúdo com palavras simples, como se estivesse ensinando alguém. Isso ajuda a identificar pontos que ainda não foram totalmente compreendidos.',
                'Quando um tema parece muito difícil, vale revisar conceitos básicos relacionados ao assunto. Muitas dificuldades surgem porque algum conhecimento anterior ficou incompleto.',
                'Resolver exercícios, criar exemplos ou aplicar o conteúdo na prática costuma fortalecer a compreensão muito mais do que apenas ler ou assistir aulas repetidamente.',
            ],

            'Pressão por desempenho' => [
                'Em vez de concentrar toda a atenção na nota final, experimente definir metas relacionadas ao processo de aprendizagem, como tempo de estudo, quantidade de exercícios ou tópicos revisados.',
                'Comparar seu desempenho com outras pessoas pode aumentar a ansiedade. Uma alternativa mais útil é comparar seu progresso atual com aquilo que você já conseguiu alcançar anteriormente.',
                'Anotar pequenas conquistas ao longo da semana pode ajudar a perceber avanços que normalmente passam despercebidos durante períodos de cobrança intensa.',
                'O aprendizado também depende de recuperação mental. Reservar momentos de descanso ajuda a manter a concentração e reduz o risco de esgotamento ao longo do tempo.',
            ],

        ];

        foreach ($subTriggers as $name => $suggestions) {

            $subId = DB::table('sub_triggers')
                ->where('name', $name)
                ->value('id');

            if (!$subId) {
                continue;
            }

            DB::table('suggestions')
                ->where('sub_trigger_id', $subId)
                ->delete();

            foreach ($suggestions as $text) {
                DB::table('suggestions')->insert([
                    'sub_trigger_id' => $subId,
                    'text' => $text,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};