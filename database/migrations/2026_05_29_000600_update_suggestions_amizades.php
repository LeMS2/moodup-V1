<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Conflitos' => [
                'Durante conflitos, é comum responder no impulso ou focar apenas na própria perspectiva. Antes de reagir, tente ouvir atentamente o que a outra pessoa está tentando comunicar. Em alguns casos, compreender melhor o ponto de vista do outro pode ajudar a reduzir mal-entendidos.',
                'Quando as emoções estão muito intensas, uma pausa antes de continuar a conversa pode ajudar a evitar respostas impulsivas. Retomar o diálogo com mais calma costuma facilitar a comunicação.',
                'Ao conversar sobre um problema, tente focar em como determinada situação fez você se sentir, em vez de apenas apontar erros ou culpas. Isso pode tornar a conversa mais produtiva e menos defensiva.',
                'Nem todo conflito precisa terminar com uma solução imediata. Em alguns casos, compreender melhor a situação e manter o diálogo aberto já representa um avanço importante.',
            ],

            'Sentimento de exclusão' => [
                'Sentir-se excluída pode ser doloroso e gerar dúvidas sobre o próprio valor. Tente lembrar que nem sempre a ausência de convites, mensagens ou interações significa rejeição intencional.',
                'Quando nos sentimos excluídos, é comum interpretar situações de forma mais negativa. Procurar outras explicações possíveis para o que aconteceu pode ajudar a reduzir conclusões precipitadas.',
                'Participar de grupos, atividades ou comunidades relacionadas aos seus interesses pode aumentar as oportunidades de criar conexões significativas com outras pessoas.',
                'Em vez de avaliar apenas a quantidade de amizades, tente valorizar também a qualidade das relações que já fazem parte da sua vida.',
            ],

            'Distanciamento de amigos' => [
                'As amizades costumam passar por períodos de maior e menor proximidade. Nem sempre o afastamento significa que o vínculo deixou de existir.',
                'Se sentir falta de alguém, considere dar o primeiro passo. Uma mensagem simples perguntando como a pessoa está pode ser suficiente para retomar o contato.',
                'Mudanças na rotina, trabalho, estudos ou responsabilidades podem influenciar a frequência das conversas. Tentar compreender essas mudanças pode ajudar a evitar interpretações negativas.',
                'Mesmo quando a frequência diminui, pequenos momentos de contato e interesse genuíno podem ajudar a fortalecer amizades ao longo do tempo.',
            ],

            'Dificuldade para fazer amizades' => [
                'Construir novas amizades costuma levar tempo. Relacionamentos significativos geralmente surgem a partir de interações frequentes e experiências compartilhadas.',
                'Participar de atividades, cursos, grupos ou comunidades relacionadas aos seus interesses pode facilitar encontros com pessoas que compartilham gostos semelhantes.',
                'Muitas amizades começam com interações simples do dia a dia. Um elogio sincero, uma pergunta sobre um interesse em comum ou um comentário sobre algo que chamou sua atenção podem ser suficientes para iniciar uma conversa. Pequenos passos costumam ser mais confortáveis do que tentar criar uma conexão profunda imediatamente.',
                'Em vez de buscar agradar todas as pessoas, tente focar em ambientes onde você possa agir de forma mais autêntica. Relações construídas com base na autenticidade costumam ser mais saudáveis e duradouras.',
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