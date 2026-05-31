<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('resources')
            ->where('title', 'Respiração guiada (5 min)')
            ->update([
                'url' => 'https://www.youtube.com/watch?v=ZuGll8XsrUs',
            ]);

        DB::table('resources')
            ->where('title', 'Meditação curta (3 min)')
            ->update([
                'url' => 'https://www.youtube.com/watch?v=fmBRuuQ0Gs8',
            ]);

        DB::table('resources')
            ->where('title', 'Alongamento relaxante')
            ->update([
                'url' => 'https://www.youtube.com/watch?v=NtXHMBISX_U',
            ]);

        DB::table('resources')
            ->where('title', 'Técnica grounding 5-4-3-2-1')
            ->update([
                'url' => 'https://www.youtube.com/watch?v=pjRMg6KALiw',
            ]);

        DB::table('resources')
            ->where('title', 'Relaxamento muscular')
            ->update([
                'url' => 'https://www.youtube.com/watch?v=LbuxIScY-wU',
            ]);

        DB::table('resources')
            ->where('title', 'Mini pausa respiração')
            ->update([
                'url' => 'https://www.youtube.com/shorts/mPepsJkhIPs',
            ]);

        DB::table('resources')
            ->where('title', 'Journaling 3 coisas do dia')
            ->update([
                'url' => 'https://www.youtube.com/shorts/6UWr3IAcmg4',
            ]);

        DB::table('resources')
            ->where('title', 'Planejamento do dia')
            ->update([
                'url' => 'https://www.youtube.com/shorts/dO0bgNEJ5XY',
            ]);
    }

    public function down(): void
    {
        DB::table('resources')
            ->where('title', 'Respiração guiada (5 min)')
            ->update([
                'url' => 'https://www.youtube.com/results?search_query=respiração+guiada',
            ]);

        DB::table('resources')
            ->where('title', 'Meditação curta (3 min)')
            ->update([
                'url' => 'https://www.youtube.com/results?search_query=meditação+guiada+3+min',
            ]);

        DB::table('resources')
            ->where('title', 'Alongamento relaxante')
            ->update([
                'url' => 'https://www.youtube.com/results?search_query=alongamento+relaxante',
            ]);

        DB::table('resources')
            ->where('title', 'Técnica grounding 5-4-3-2-1')
            ->update([
                'url' => 'https://www.youtube.com/results?search_query=grounding+5-4-3-2-1',
            ]);

        DB::table('resources')
            ->where('title', 'Relaxamento muscular')
            ->update([
                'url' => 'https://www.youtube.com/results?search_query=relaxamento+muscular',
            ]);
    }
};