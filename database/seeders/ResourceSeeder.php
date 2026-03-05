<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            // -------- VIDEOS --------
            [
                'type' => 'video',
                'title' => 'Respiração guiada (5 min)',
                'description' => 'Exercício rápido para acalmar o corpo.',
                'url' => 'https://www.youtube.com/results?search_query=respiração+guiada',
                'author' => 'Curadoria MoodUp',
                'duration_minutes' => 5,
                'tags' => ['ansiedade','calma'],
                'is_active' => true,
            ],
            [
                'type' => 'video',
                'title' => 'Meditação curta (3 min)',
                'description' => 'Meditação simples para relaxar.',
                'url' => 'https://www.youtube.com/results?search_query=meditação+guiada+3+min',
                'author' => 'Curadoria MoodUp',
                'duration_minutes' => 3,
                'tags' => ['meditação','relaxamento'],
                'is_active' => true,
            ],
            [
                'type' => 'video',
                'title' => 'Alongamento relaxante',
                'description' => 'Alongamento leve para reduzir tensão.',
                'url' => 'https://www.youtube.com/results?search_query=alongamento+relaxante',
                'author' => 'Curadoria MoodUp',
                'duration_minutes' => 7,
                'tags' => ['corpo','relaxamento'],
                'is_active' => true,
            ],
            [
                'type' => 'video',
                'title' => 'Técnica grounding 5-4-3-2-1',
                'description' => 'Técnica rápida para reduzir ansiedade.',
                'url' => 'https://www.youtube.com/results?search_query=grounding+5-4-3-2-1',
                'author' => 'Curadoria MoodUp',
                'duration_minutes' => 5,
                'tags' => ['ansiedade'],
                'is_active' => true,
            ],
            [
                'type' => 'video',
                'title' => 'Relaxamento muscular',
                'description' => 'Exercício para relaxar o corpo.',
                'url' => 'https://www.youtube.com/results?search_query=relaxamento+muscular',
                'author' => 'Curadoria MoodUp',
                'duration_minutes' => 8,
                'tags' => ['stress'],
                'is_active' => true,
            ],

            // -------- MUSICAS --------
            [
                'type' => 'musica',
                'title' => 'Playlist foco e calma',
                'description' => 'Playlist relaxante.',
                'url' => 'https://open.spotify.com/search/focus%20calm',
                'author' => 'Curadoria MoodUp',
                'tags' => ['foco'],
                'is_active' => true,
            ],
            [
                'type' => 'musica',
                'title' => 'Lo-fi para estudar',
                'description' => 'Playlist para concentração.',
                'url' => 'https://open.spotify.com/search/lofi%20study',
                'author' => 'Curadoria MoodUp',
                'tags' => ['estudo'],
                'is_active' => true,
            ],
            [
                'type' => 'musica',
                'title' => 'Sons da natureza',
                'description' => 'Sons relaxantes.',
                'url' => 'https://open.spotify.com/search/nature%20sounds',
                'author' => 'Curadoria MoodUp',
                'tags' => ['natureza'],
                'is_active' => true,
            ],
            [
                'type' => 'musica',
                'title' => 'Música relaxante',
                'description' => 'Músicas para relaxar.',
                'url' => 'https://open.spotify.com/search/relaxing%20music',
                'author' => 'Curadoria MoodUp',
                'tags' => ['relaxamento'],
                'is_active' => true,
            ],
            [
                'type' => 'musica',
                'title' => 'Música para dormir',
                'description' => 'Trilhas calmas para sono.',
                'url' => 'https://open.spotify.com/search/sleep%20music',
                'author' => 'Curadoria MoodUp',
                'tags' => ['sono'],
                'is_active' => true,
            ],

            // -------- LIVROS --------
            [
                'type' => 'livro',
                'title' => 'Hábitos Atômicos',
                'description' => 'Criando bons hábitos.',
                'url' => 'https://www.google.com/search?q=hábitos+atômicos',
                'author' => 'James Clear',
                'tags' => ['hábitos'],
                'is_active' => true,
            ],
            [
                'type' => 'livro',
                'title' => 'Mindset',
                'description' => 'Mentalidade de crescimento.',
                'url' => 'https://www.google.com/search?q=livro+mindset',
                'author' => 'Carol Dweck',
                'tags' => ['mentalidade'],
                'is_active' => true,
            ],
            [
                'type' => 'livro',
                'title' => 'O Poder do Agora',
                'description' => 'Consciência e presença.',
                'url' => 'https://www.google.com/search?q=o+poder+do+agora',
                'author' => 'Eckhart Tolle',
                'tags' => ['presença'],
                'is_active' => true,
            ],
            [
                'type' => 'livro',
                'title' => 'Essencialismo',
                'description' => 'Foco no que importa.',
                'url' => 'https://www.google.com/search?q=livro+essencialismo',
                'author' => 'Greg McKeown',
                'tags' => ['foco'],
                'is_active' => true,
            ],
            [
                'type' => 'livro',
                'title' => 'A Coragem de Ser Imperfeito',
                'description' => 'Autocompaixão e vulnerabilidade.',
                'url' => 'https://www.google.com/search?q=a+coragem+de+ser+imperfeito',
                'author' => 'Brené Brown',
                'tags' => ['autoestima'],
                'is_active' => true,
            ],

            // -------- EXERCICIOS --------
            [
                'type' => 'exercicio',
                'title' => 'Journaling 3 coisas do dia',
                'description' => 'Escreva três coisas importantes do dia.',
                'author' => 'MoodUp',
                'duration_minutes' => 7,
                'tags' => ['reflexão'],
                'is_active' => true,
            ],
            [
                'type' => 'exercicio',
                'title' => 'Técnica 5-4-3-2-1',
                'description' => 'Observe o ambiente.',
                'author' => 'MoodUp',
                'duration_minutes' => 4,
                'tags' => ['ansiedade'],
                'is_active' => true,
            ],
            [
                'type' => 'exercicio',
                'title' => 'Mini pausa respiração',
                'description' => 'Respire fundo por dois minutos.',
                'author' => 'MoodUp',
                'duration_minutes' => 2,
                'tags' => ['calma'],
                'is_active' => true,
            ],
            [
                'type' => 'exercicio',
                'title' => 'Lista de gratidão',
                'description' => 'Anote três coisas positivas.',
                'author' => 'MoodUp',
                'duration_minutes' => 5,
                'tags' => ['gratidão'],
                'is_active' => true,
            ],
            [
                'type' => 'exercicio',
                'title' => 'Planejamento do dia',
                'description' => 'Defina três prioridades.',
                'author' => 'MoodUp',
                'duration_minutes' => 5,
                'tags' => ['organização'],
                'is_active' => true,
            ],

        ];

        foreach ($items as $item) {
            Resource::updateOrCreate(
                ['type' => $item['type'], 'title' => $item['title']],
                $item
            );
        }
    }
}