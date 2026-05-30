<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Dormiu mal' => [
                'Uma noite ruim de sono pode afetar o humor, a concentração e a disposição ao longo do dia. Se possível, tente ajustar suas expectativas e não se cobrar o mesmo rendimento de um dia em que descansou bem.',
                'Quando o sono não foi suficiente, pequenas pausas durante o dia podem ajudar a reduzir a sensação de cansaço e recuperar parte da atenção. Se possível, aproveite esses momentos para respirar, alongar-se ou descansar alguns minutos longe das telas.',
                'A exposição à luz natural pela manhã pode ajudar o organismo a regular melhor o ciclo do sono nos dias seguintes. Sempre que possível, passe alguns minutos em ambientes iluminados naturalmente após acordar.',
                'Se possível, procure retomar sua rotina normal de horários para dormir e acordar. Pequenos ajustes costumam ser mais eficazes do que tentar compensar totalmente uma noite mal dormida.',
            ],

            'Insônia' => [
                'Quando não conseguimos dormir, é comum que a preocupação com o horário aumente ainda mais a dificuldade para pegar no sono. Tentar não focar constantemente no relógio pode ajudar a reduzir parte dessa ansiedade.',
                'Criar uma rotina tranquila antes de dormir pode ajudar o corpo a entender que é hora de descansar. Algumas pessoas se beneficiam ao reduzir o uso de telas, diminuir a intensidade das luzes do ambiente, ler algo leve, ouvir músicas calmas ou tomar uma bebida quente sem cafeína, como um chá. Pequenos hábitos repetidos ao longo do tempo ajudam a sinalizar ao organismo que o momento de dormir está se aproximando.',
                'Caso não consiga dormir após muito tempo na cama, levantar por alguns minutos para realizar uma atividade tranquila pode ser mais confortável do que permanecer tentando forçar o sono. Ler algumas páginas de um livro, ouvir sons relaxantes ou praticar exercícios leves de respiração são alternativas que podem ajudar a mente a desacelerar.',
                'Dificuldades frequentes para dormir merecem atenção. Caso a insônia esteja presente por longos períodos ou esteja afetando sua qualidade de vida, procurar orientação profissional pode ser uma boa opção.',
            ],

            'Sono não reparador' => [
                'Mesmo dormindo várias horas, algumas pessoas acordam com a sensação de que o descanso não foi suficiente. Observar como você se sente ao longo do dia pode ajudar a identificar padrões relacionados ao sono.',
                'Horários muito diferentes para dormir e acordar podem dificultar o funcionamento adequado do relógio biológico. Sempre que possível, tente manter uma rotina relativamente consistente, inclusive nos fins de semana.',
                'Fatores como estresse, preocupações constantes ou desconfortos físicos também podem influenciar a qualidade do sono. Identificar possíveis causas pode ajudar na busca por soluções.',
                'Registrar horários de sono, despertares durante a noite e nível de disposição ao acordar pode ajudar a compreender melhor seus hábitos e fornecer informações úteis caso seja necessário buscar orientação profissional.',
            ],

            'Dificuldade para manter uma rotina de sono' => [
                'O corpo costuma responder melhor quando existe uma certa regularidade nos horários de dormir e acordar. Pequenas mudanças graduais costumam ser mais fáceis de manter do que alterações muito bruscas.',
                'Criar uma rotina antes de dormir pode ajudar a sinalizar para o cérebro que o momento de descanso está se aproximando. Ler, ouvir músicas tranquilas ou realizar atividades relaxantes são algumas possibilidades.',
                'Algumas pessoas acham útil criar um "alarme para dormir", programando um lembrete cerca de uma hora antes do horário desejado para começar a desacelerar as atividades do dia.',
                'Construir uma rotina de sono é um processo gradual. Mesmo que alguns dias saiam do planejado, retomar os hábitos no dia seguinte costuma ser mais importante do que buscar perfeição.',
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