<!-- use Illuminate\Database\Migrations\Migration;
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
};  -->

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Criar o tipo ENUM se ainda não existir
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'resource_type') THEN
                    CREATE TYPE resource_type AS ENUM ('video','musica','livro','exercicio');
                END IF;
            END$$;
        ");

        // Alterar a coluna para usar o ENUM
        DB::statement("
            ALTER TABLE resources
            ALTER COLUMN type TYPE resource_type USING type::text::resource_type;
        ");
    }

    public function down(): void
    {
        // Voltar para VARCHAR(20) se necessário
        DB::statement("
            ALTER TABLE resources
            ALTER COLUMN type TYPE VARCHAR(20);
        ");

        // Opcional: deletar o tipo ENUM
        DB::statement("DROP TYPE IF EXISTS resource_type;");
    }
};