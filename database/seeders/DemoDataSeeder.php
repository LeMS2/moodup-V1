<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $this->command->info('Iniciando geração dos dados de demonstração...');

// Busca usuários demo já existentes
$createdUserIds = DB::table('users')
    ->where('email', 'like', 'demo%@moodup.com')
    ->pluck('id')
    ->toArray();

// Se ainda não existirem, cria os 50 usuários
if (empty($createdUserIds)) {
$nomes = [
    'Ana', 'Beatriz', 'Camila', 'Carla', 'Daniela', 'Eduarda',
    'Fernanda', 'Gabriela', 'Isabela', 'Juliana', 'Larissa',
    'Letícia', 'Luana', 'Mariana', 'Natália', 'Patrícia',
    'Rafaela', 'Sabrina', 'Vanessa', 'Vitória',
    'Bruno', 'Carlos', 'Diego', 'Eduardo', 'Felipe',
    'Gabriel', 'Gustavo', 'Henrique', 'Igor', 'João',
    'José', 'Leonardo', 'Lucas', 'Marcelo', 'Matheus',
    'Paulo', 'Pedro', 'Rafael', 'Ricardo', 'Thiago'
];

$sobrenomes = [
    'Silva', 'Santos', 'Oliveira', 'Souza', 'Pereira',
    'Lima', 'Ferreira', 'Costa', 'Rodrigues', 'Almeida',
    'Nunes', 'Barbosa', 'Cardoso', 'Rocha', 'Dias'
];

$sexos = ['feminino', 'masculino', 'outro'];

$faixas = [
    '14-17',
    '18-24',
    '25-34',
    '35-44',
    '45-54',
    '55-64',
    '65+'
];

$estados = [
    'SP', 'RJ', 'MG', 'PR', 'SC', 'RS',
    'BA', 'PE', 'CE', 'GO', 'ES', 'DF'
];

for ($i = 1; $i <= 50; $i++) {

    $nomeCompleto =
        $nomes[array_rand($nomes)] . ' ' .
        $sobrenomes[array_rand($sobrenomes)];

    $userId = DB::table('users')->insertGetId([
        'name' => $nomeCompleto,
        'email' => sprintf('demo%02d@moodup.com', $i),
        'password' => Hash::make('12345678'),

        'sexo' => $sexos[array_rand($sexos)],
        'faixa_etaria' => $faixas[array_rand($faixas)],
        'estado' => $estados[array_rand($estados)],

        'accepted_terms_at' => now()->subDays(rand(30, 90)),

        'created_at' => now()->subDays(rand(30, 90)),
        'updated_at' => now(),
    ]);

   $createdUserIds[] = $userId;
}

$this->command->info(
    count($createdUserIds) . ' usuários de demonstração criados.'
);

} // <- fecha o if (empty($createdUserIds))

$titulos = [
    1 => ['Dia muito difícil', 'Tudo deu errado', 'Não consegui lidar bem hoje'],
    2 => ['Dia complicado', 'Me senti para baixo', 'Pouca disposição hoje'],
    3 => ['Um dia normal', 'Tudo seguiu como esperado', 'Sem grandes acontecimentos'],
    4 => ['Dia produtivo', 'Me senti bem', 'As coisas deram certo'],
    5 => ['Dia excelente', 'Estou muito feliz', 'Foi um dos melhores dias'],
];

$notas = [
    1 => [
        'Hoje me senti muito sobrecarregado e tive dificuldade para lidar com os acontecimentos.',
        'Nada pareceu dar certo e fiquei bastante desanimado durante o dia.',
        'Passei por várias situações estressantes e terminei o dia muito cansado.'
    ],
    2 => [
        'Algumas coisas não aconteceram como eu esperava, mas consegui seguir em frente.',
        'Foi um dia cansativo e com algumas preocupações.',
        'Não foi um dia ruim, mas também não consegui me sentir muito bem.'
    ],
    3 => [
        'Foi um dia tranquilo, sem acontecimentos muito marcantes.',
        'Minha rotina ocorreu normalmente e consegui cumprir minhas tarefas.',
        'Não tive grandes emoções hoje, apenas um dia comum.'
    ],
    4 => [
        'Consegui resolver minhas pendências e fiquei satisfeito com o meu dia.',
        'Hoje me senti mais motivado e produtivo.',
        'Passei bons momentos com pessoas importantes e isso melhorou meu humor.'
    ],
    5 => [
        'Hoje foi um dia maravilhoso e me senti muito feliz.',
        'Recebi boas notícias e aproveitei bastante o meu dia.',
        'Tudo pareceu fluir bem e terminei o dia muito satisfeito.'
    ]
];

$mapeamentoMood = [
    1 => 'muito_triste',
    2 => 'triste',
    3 => 'neutro',
    4 => 'bem',
    5 => 'muito_bem'
];

foreach ($createdUserIds as $userId) {

    $data = new \DateTime('2026-05-01');

    while ($data <= new \DateTime('2026-05-31')) {

        $level = rand(1, 5);

        $moodId = DB::table('moods')->insertGetId([
            'user_id' => $userId,
            'date' => $data->format('Y-m-d'),
            'level' => $level,
            'score' => $level,
            'mood' => $mapeamentoMood[$level],
            'title' => $titulos[$level][array_rand($titulos[$level])],
            'note' => $notas[$level][array_rand($notas[$level])],
            'created_at' => $data->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(7, 22), rand(0, 59)),
            'updated_at' => $data->format('Y-m-d') . ' ' . sprintf('%02d:%02d:00', rand(7, 22), rand(0, 59)),
        ]);

        DB::table('mood_trigger')->insert([
            'mood_id' => $moodId,
            'trigger_id' => rand(1, 8),
        ]);

        $data->modify('+1 day');
    }
}

$this->command->info('Moods de demonstração criados com sucesso!');

$this->command->info('Seeder finalizado com sucesso!');
    }
}