<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Discussões' => [
                'Durante uma discussão, tente falar sobre como você se sentiu diante da situação em vez de apenas apontar erros da outra pessoa. Isso costuma facilitar o diálogo e diminuir os conflitos.',
                'Quando a conversa estiver muito intensa, fazer uma pequena pausa antes de continuar pode ajudar todos os envolvidos a organizarem melhor seus pensamentos.',
                'Antes de responder, tente compreender o que a outra pessoa realmente quis dizer. Muitas discussões aumentam por causa de interpretações equivocadas.',
                'Sempre que possível, procure direcionar a conversa para a busca de soluções. Resolver um problema costuma ser mais produtivo do que tentar encontrar culpados.',
            ],

            'Falta de apoio' => [
                'Quando estiver passando por um momento difícil, tente pensar nas pessoas com quem você se sente segura para conversar. Ter alguém para ouvir você pode fazer diferença em momentos de maior sobrecarga emocional.',
                'Nem sempre as pessoas percebem quando precisamos de ajuda. Explicar de forma clara o que você está sentindo pode facilitar o apoio e a compreensão.',
                'Participar de grupos, atividades ou comunidades pode ajudar a criar novas conexões e ampliar sua rede de apoio ao longo do tempo.',
                'Além de buscar apoio externo, procure reservar momentos para cuidar de si mesma. Pequenas atividades que trazem bem-estar também ajudam a fortalecer o equilíbrio emocional.',
            ],

            'Responsabilidades em casa' => [
                'Quando muitas tarefas domésticas se acumulam, fazer uma lista simples pode ajudar a visualizar melhor o que realmente precisa ser feito naquele momento.',
                'Nem tudo precisa ser resolvido no mesmo dia. Identificar prioridades pode tornar a rotina mais leve e organizada.',
                'Sempre que possível, converse com as pessoas da casa sobre a divisão das responsabilidades. Compartilhar tarefas costuma reduzir a sobrecarga.',
                'Reservar alguns minutos para descanso entre as atividades também faz parte de uma rotina saudável e equilibrada.',
            ],

            'Preocupações com familiares' => [
                'Quando alguém que amamos está enfrentando dificuldades, é comum querer resolver tudo imediatamente. Procure focar naquilo que realmente está ao seu alcance neste momento.',
                'Buscar informações o tempo todo pode aumentar a ansiedade. Estabelecer momentos específicos para acompanhar a situação costuma ajudar a manter mais tranquilidade.',
                'Pequenos gestos de apoio, como ouvir, acompanhar ou ajudar em tarefas simples, muitas vezes fazem mais diferença do que imaginamos.',
                'Cuidar de quem você ama também inclui cuidar de si mesma. Manter sua rotina de descanso, alimentação e bem-estar ajuda você a oferecer apoio de forma mais saudável.',
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