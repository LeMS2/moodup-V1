<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sub_triggers')->insert([

            // 👨‍👩‍👧 FAMÍLIA
            [
                'trigger' => 'familia',
                'name' => 'Preocupações com familiares',
                'intro_text' => 'Quando alguém importante para nós está passando por dificuldades, é natural sentir preocupação.',
                'closing_text' => 'Lembre-se de cuidar de você também enquanto apoia quem ama.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 🚗 TRÂNSITO
            [
                'trigger' => 'transito',
                'name' => 'Transporte lotado',
                'intro_text' => 'Ambientes cheios e desconfortáveis podem tornar o trajeto mais cansativo.',
                'closing_text' => 'Tente focar em pequenas pausas mentais durante o percurso.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'trigger' => 'transito',
                'name' => 'Direção estressante',
                'intro_text' => 'Situações difíceis no trânsito podem gerar irritação e desgaste emocional.',
                'closing_text' => 'Respire fundo e lembre-se que sua segurança vem primeiro.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 🧑‍🤝‍🧑 AMIZADES
            [
                'trigger' => 'amizades',
                'name' => 'Distanciamento de amigos',
                'intro_text' => 'Mudanças nas amizades podem gerar tristeza e sentimentos de afastamento.',
                'closing_text' => 'Nem toda amizade termina; algumas apenas passam por fases diferentes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'trigger' => 'amizades',
                'name' => 'Dificuldade para fazer amizades',
                'intro_text' => 'Criar novas conexões nem sempre é fácil, e tudo bem levar tempo.',
                'closing_text' => 'Relacionamentos verdadeiros costumam ser construídos aos poucos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 💰 DINHEIRO
            [
                'trigger' => 'dinheiro',
                'name' => 'Despesas inesperadas',
                'intro_text' => 'Imprevistos financeiros podem trazer preocupação e insegurança.',
                'closing_text' => 'Um passo de cada vez costuma ser mais eficaz do que tentar resolver tudo de uma vez.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'trigger' => 'dinheiro',
                'name' => 'Dificuldade para economizar',
                'intro_text' => 'Guardar dinheiro pode ser desafiador, principalmente em períodos difíceis.',
                'closing_text' => 'Pequenas mudanças de hábito podem gerar resultados ao longo do tempo.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ❤️ SAÚDE
            [
                'trigger' => 'saude',
                'name' => 'Preocupação com exames ou consultas',
                'intro_text' => 'A espera por resultados ou consultas pode gerar ansiedade.',
                'closing_text' => 'Procure focar no que está ao seu alcance enquanto aguarda respostas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'trigger' => 'saude',
                'name' => 'Falta de energia no dia a dia',
                'intro_text' => 'Quando a energia parece insuficiente, até tarefas simples podem parecer difíceis.',
                'closing_text' => 'Respeitar seus limites também faz parte do autocuidado.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 😴 SONO
            [
                'trigger' => 'sono',
                'name' => 'Sono não reparador',
                'intro_text' => 'Mesmo após dormir, você pode sentir que não descansou o suficiente.',
                'closing_text' => 'O descanso é tão importante quanto a quantidade de horas dormidas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'trigger' => 'sono',
                'name' => 'Dificuldade para manter uma rotina de sono',
                'intro_text' => 'Horários irregulares podem dificultar o descanso e a recuperação do corpo.',
                'closing_text' => 'Pequenas mudanças na rotina podem ajudar seu organismo a se adaptar.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }

    public function down(): void
    {
        DB::table('sub_triggers')
            ->whereIn('name', [
                'Preocupações com familiares',
                'Transporte lotado',
                'Direção estressante',
                'Distanciamento de amigos',
                'Dificuldade para fazer amizades',
                'Despesas inesperadas',
                'Dificuldade para economizar',
                'Preocupação com exames ou consultas',
                'Falta de energia no dia a dia',
                'Sono não reparador',
                'Dificuldade para manter uma rotina de sono',
            ])
            ->delete();
    }
};