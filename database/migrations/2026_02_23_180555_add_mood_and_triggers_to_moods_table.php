<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            $table->string('mood')->nullable();
            $table->json('triggers')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            $table->dropColumn(['mood', 'triggers']);
        });
    }
};