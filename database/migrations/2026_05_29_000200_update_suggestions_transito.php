<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Engarrafamento' => [
                'Ficar parado no trânsito pode ser frustrante. Ouvir músicas, podcasts ou audiolivros que você gosta pode ajudar a tornar esse momento mais leve e menos cansativo.',
                'Se possível, aproveite o trajeto para aprender algo novo ou acompanhar conteúdos que normalmente não teria tempo para consumir.',
                'Observe se há tensão acumulada nos ombros, pescoço ou mandíbula. Relaxar essas regiões pode ajudar a reduzir parte do estresse do percurso.',
                'Nem sempre é possível controlar o trânsito, mas é possível escolher atividades que tornem esse tempo mais agradável e produtivo.',
            ],

            'Atrasos' => [
                'Quando perceber que vai se atrasar, avisar as pessoas envolvidas costuma reduzir a preocupação e evitar cobranças desnecessárias.',
                'Depois que a situação passar, tente identificar o que contribuiu para o atraso. Entender os motivos pode ajudar a evitar que o problema se repita.',
                'Sempre que possível, deixe uma pequena margem de tempo entre compromissos importantes para lidar melhor com imprevistos.',
                'Depois que o atraso aconteceu, concentrar energia no próximo passo costuma ser mais útil do que ficar revivendo aquilo que não pode mais ser mudado.',
            ],

            'Transporte lotado' => [
                'Trajetos muito cheios podem ser cansativos física e mentalmente. Ter uma playlist, podcast ou conteúdo favorito pode ajudar a tornar o percurso mais agradável.',
                'Se o trajeto costuma gerar muito estresse, algumas pessoas gostam de carregar pequenos objetos antiestresse para ajudar a aliviar a tensão durante o percurso.',
                'Quando houver flexibilidade, experimentar horários alternativos pode ajudar a encontrar períodos com menor movimento.',
                'O tempo de deslocamento também pode ser usado para ouvir algo interessante, aprender algo novo ou simplesmente relaxar um pouco antes do próximo compromisso.',
            ],

            'Direção estressante' => [
                'Algumas atitudes de outros motoristas podem ser irritantes, mas responder a essas situações raramente melhora o trajeto. Priorizar sua segurança costuma ser a melhor escolha.',
                'Manter uma distância segura dos outros veículos pode ajudar a dirigir com mais tranquilidade e reduzir a sensação de pressão.',
                'Criar um ambiente agradável dentro do veículo, com músicas ou conteúdos que você gosta, pode tornar o percurso mais confortável.',
                'Se perceber que está ficando muito irritada ou cansada durante o trajeto, fazer uma breve pausa em um local seguro pode ajudar a recuperar a calma antes de continuar dirigindo.',
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