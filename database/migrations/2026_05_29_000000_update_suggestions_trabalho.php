<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Excesso de tarefas' => [
                'Quando tudo parece urgente, pode ser útil listar as tarefas e identificar quais realmente precisam ser concluídas primeiro. Essa organização ajuda a reduzir a sensação de caos.',
                'Projetos grandes podem gerar bloqueio logo no início. Separá-los em pequenas etapas torna o trabalho mais acessível e facilita o progresso contínuo.',
                'Alternar constantemente entre várias tarefas pode aumentar o cansaço mental. Sempre que possível, conclua uma atividade antes de iniciar a próxima.',
                'Ao final do dia, reserve alguns minutos para observar o que já foi concluído. Essa prática ajuda a visualizar resultados e reduz a sensação de que nada avançou.',
            ],

            'Pressão por resultados' => [
                'Nem todos os resultados estão totalmente sob nosso controle. Identificar aquilo que depende diretamente das suas ações pode ajudar a direcionar melhor sua energia.',
                'Objetivos muito grandes podem parecer distantes. Dividir resultados em pequenas metas permite acompanhar o progresso de forma mais clara e motivadora.',
                'Criar formas simples de medir avanços ao longo do caminho ajuda a perceber evolução mesmo antes do objetivo final ser alcançado.',
                'Em momentos de muita cobrança, vale refletir se as metas estabelecidas continuam realistas diante das circunstâncias atuais. Ajustar expectativas não significa desistir, mas adaptar a estratégia.',
            ],

            'Relacionamento com colegas' => [
                'Pequenos mal-entendidos podem crescer quando não são esclarecidos. Conversar de forma respeitosa logo no início costuma evitar conflitos maiores.',
                'Durante conversas difíceis, tente focar na situação que aconteceu em vez de assumir intenções ou julgamentos sobre a outra pessoa.',
                'Fazer perguntas para entender melhor o ponto de vista do colega pode ajudar a construir soluções e fortalecer a comunicação.',
                'Reconhecer atitudes positivas e boas contribuições dos colegas também ajuda a criar um ambiente de trabalho mais colaborativo.',
            ],

            'Cansaço extremo' => [
                'Observe quais atividades estão consumindo mais energia ao longo da semana. Identificar as principais fontes de desgaste é o primeiro passo para buscar mudanças.',
                'Pequenas pausas entre tarefas exigentes ajudam o cérebro a recuperar parte da atenção e da energia mental, mesmo em dias corridos.',
                'Sempre que possível, alterne atividades que exigem muito esforço mental com tarefas mais simples para evitar sobrecarga contínua.',
                'O descanso não é uma recompensa por produzir. Ele é uma necessidade que ajuda seu corpo e sua mente a funcionarem de forma saudável.',
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