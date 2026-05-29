<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Problemas físicos' => [
                'Quando o corpo não está bem, é natural que o humor e a disposição também sejam afetados. Reconhecer essa relação pode ajudar a lidar com o momento de forma mais gentil consigo mesma.',
                'Sempre que possível, procure seguir as orientações dos profissionais de saúde que acompanham sua situação. Anotar dúvidas antes das consultas pode ajudar a aproveitar melhor esses momentos.',
                'Em períodos de recuperação, tente concentrar sua atenção nos pequenos avanços do dia a dia. Melhorias graduais muitas vezes passam despercebidas quando estamos focados apenas no resultado final.',
                'Respeitar os limites do seu corpo não significa desistir. Em muitos casos, descansar adequadamente faz parte do processo de recuperação.',
            ],

            'Cansaço mental' => [
                'Quando a mente parece sobrecarregada, pode ser útil identificar quais situações ou responsabilidades estão consumindo mais energia emocional. Entender a origem do desgaste costuma facilitar a busca por soluções.',
                'Quando o cansaço mental aparece, pode ser tentador passar o tempo livre navegando nas redes sociais. No entanto, em alguns momentos isso pode aumentar ainda mais a sensação de desgaste. Pequenas pausas ao longo do dia podem ajudar a recuperar parte da atenção e da capacidade de concentração. Se possível, tente reservar alguns minutos longe das telas para respirar, caminhar, alongar-se ou realizar uma atividade que você goste, como desenhar, ouvir música ou escrever sobre o que está sentindo. Mesmo 10 ou 15 minutos podem ajudar a mente a desacelerar e recuperar energia.',
                'Nem toda tarefa precisa ser resolvida imediatamente. Definir prioridades ajuda a reduzir a sensação de estar tentando lidar com tudo ao mesmo tempo.',
                'Conversar com alguém de confiança sobre aquilo que está gerando preocupação pode ajudar a organizar pensamentos e aliviar parte da carga emocional.',
            ],

            'Preocupação com exames ou consultas' => [
                'É comum sentir ansiedade enquanto aguardamos exames, resultados ou consultas. Em momentos como esse, tente lembrar que a espera costuma ser uma das partes mais difíceis do processo. Enquanto aguarda, procure direcionar sua atenção para atividades que tragam conforto e bem-estar.',
                'Anotar dúvidas e sintomas antes da consulta pode ajudar você a se sentir mais preparada e aproveitar melhor o atendimento.',
                'Buscar informações em fontes confiáveis costuma ser mais útil do que passar longos períodos pesquisando conteúdos alarmantes na internet.',
                'Enquanto aguarda respostas, procure direcionar sua atenção para atividades que estejam sob seu controle neste momento. Isso pode ajudar a reduzir parte da ansiedade relacionada à espera.',
            ],

            'Falta de energia no dia a dia' => [
                'A falta de energia pode ter diferentes causas, como rotina intensa, estresse, sono inadequado ou questões de saúde. Observar quando esse cansaço costuma aparecer pode ajudar a identificar padrões.',
                'Pequenas caminhadas, alongamentos ou momentos de movimento ao longo do dia podem contribuir para aumentar a disposição física e mental.',
                'Alimentação, hidratação e sono influenciam diretamente os níveis de energia. Cuidar desses aspectos pode trazer benefícios graduais ao longo do tempo.',
                'Em vez de exigir produtividade máxima o tempo todo, tente estabelecer metas menores para os dias em que a energia estiver mais baixa. Pequenos avanços também são avanços.',
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