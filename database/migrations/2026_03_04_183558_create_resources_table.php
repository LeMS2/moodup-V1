<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // video|musica|livro|exercicio
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('author', 120)->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};