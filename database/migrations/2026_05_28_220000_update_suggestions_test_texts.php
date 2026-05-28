<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Executa a migration
     */
    public function up(): void
    {
        DB::table('suggestions')
            ->where('text', 'Use ciclos de estudo (25min + pausa)')
            ->update([
                'text' => 'Experimente o método Pomodoro: estude por 25 minutos e faça uma pausa curta de 5 minutos. Isso pode ajudar a manter o foco sem sobrecarregar tanto a mente.'
            ]);

        DB::table('suggestions')
            ->where('text', 'Priorize o mais importante')
            ->update([
                'text' => 'Nem tudo precisa ser resolvido ao mesmo tempo. Tente escolher o que é mais importante agora e comece por um passo pequeno.'
            ]);
    }

    /**
     * Desfaz a migration
     */
    public function down(): void
    {
        DB::table('suggestions')
            ->where('text', 'Experimente o método Pomodoro: estude por 25 minutos e faça uma pausa curta de 5 minutos. Isso pode ajudar a manter o foco sem sobrecarregar tanto a mente.')
            ->update([
                'text' => 'Use ciclos de estudo (25min + pausa)'
            ]);

        DB::table('suggestions')
            ->where('text', 'Nem tudo precisa ser resolvido ao mesmo tempo. Tente escolher o que é mais importante agora e comece por um passo pequeno.')
            ->update([
                'text' => 'Priorize o mais importante'
            ]);
    }
};