<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            if (!Schema::hasColumn('moods', 'title')) {
                $table->string('title')->nullable();
            }

            if (!Schema::hasColumn('moods', 'score')) {
                $table->unsignedInteger('score')->nullable(); // ajustado
            }

            if (!Schema::hasColumn('moods', 'mood')) {
                $table->string('mood')->nullable();
            }

            if (!Schema::hasColumn('moods', 'triggers')) {
                $table->json('triggers')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            $table->dropColumn(['title', 'score', 'mood', 'triggers']);
        });
    }
};