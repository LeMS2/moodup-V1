<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE resources MODIFY type ENUM('video','musica','livro','exercicio') NOT NULL");
    }

    public function down(): void
    {
        // se quiser voltar ao padrão anterior, ajuste aqui
        DB::statement("ALTER TABLE resources MODIFY type VARCHAR(20) NOT NULL");
    }
};