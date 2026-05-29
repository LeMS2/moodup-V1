<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subTriggers = [

            'Preocupação financeira' => [
                'Quando as contas começam a gerar preocupação, pode ser difícil enxergar a situação com clareza. Anotar receitas, despesas e compromissos financeiros ajuda a visualizar o cenário atual e identificar quais áreas precisam de mais atenção.',
                'Uma estratégia simples é dividir os gastos entre essenciais e não essenciais. Isso pode ajudar a identificar oportunidades de ajuste sem abrir mão de tudo que traz qualidade de vida.',
                'Nem sempre é necessário fazer grandes mudanças. Pequenos cortes ou adaptações em gastos recorrentes podem gerar resultados significativos ao longo do tempo.',
                'Ter um plano financeiro simples para as próximas semanas ou meses pode ajudar a diminuir a sensação de incerteza e aumentar a percepção de controle sobre a situação.',
            ],

            'Dívidas' => [
                'Pode ser desconfortável olhar para as dívidas, mas reunir todas as informações em um único lugar ajuda a entender melhor a situação e facilita a tomada de decisões.',
                'Quando existem várias dívidas ao mesmo tempo, pode ser útil identificar quais possuem juros mais altos ou maior impacto financeiro para definir prioridades.',
                'Muitas instituições oferecem renegociação, parcelamentos ou condições especiais. Buscar informações sobre essas possibilidades pode ajudar a aliviar parte da pressão financeira.',
                'Quitar uma dívida costuma ser um processo gradual. Reconhecer cada etapa concluída pode ajudar a manter a motivação durante esse caminho.',
            ],

            'Despesas inesperadas' => [
                'Quando surge um gasto inesperado, é comum sentir preocupação ou agir por impulso. Reservar alguns minutos para avaliar a situação pode ajudar a encontrar alternativas mais adequadas.',
                'Nem toda despesa inesperada precisa ser resolvida da mesma forma. Identificar o que é urgente e o que pode esperar ajuda a organizar melhor os recursos disponíveis.',
                'Dependendo da situação, pode haver opções de parcelamento, negociação ou apoio que reduzam o impacto financeiro imediato.',
                'Sempre que possível, tente identificar se existe alguma forma de se preparar melhor para situações semelhantes no futuro, mesmo que seja com pequenas reservas financeiras.',
            ],

            'Dificuldade para economizar' => [
                'Durante uma semana, tente anotar todos os gastos, até os menores. Muitas vezes descobrimos para onde o dinheiro está indo apenas quando visualizamos tudo em um único lugar.',
                'Algumas pessoas utilizam a regra 50-30-20, dividindo a renda entre necessidades, lazer e economia. Adaptar uma versão simples dessa estratégia pode ajudar na organização financeira.',
                'Criar uma transferência automática para uma reserva financeira pode ajudar a desenvolver o hábito de economizar sem precisar tomar essa decisão todos os meses. O valor não precisa ser alto: começar com R$ 20, R$ 30 ou R$ 50 já pode ser suficiente para criar consistência.',
                'Quando o orçamento continua apertado mesmo após a organização dos gastos, pode ser útil pensar em formas de gerar uma renda complementar. Nem sempre isso significa assumir um segundo emprego ou trabalhar todos os dias da semana. Algumas pessoas conseguem uma renda extra vendendo alimentos, oferecendo serviços, realizando trabalhos temporários ou utilizando habilidades que já possuem. Em alguns casos, dedicar apenas algumas horas do fim de semana a uma atividade compatível com sua rotina já pode ajudar a aliviar a pressão financeira e aumentar a sensação de controle sobre as próprias finanças.',
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