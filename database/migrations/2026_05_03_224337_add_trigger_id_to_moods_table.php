<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('moods', function (Blueprint $table) {
        $table->foreignId('trigger_id')
              ->nullable()
              ->constrained()
              ->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            //
        });
    }
};
