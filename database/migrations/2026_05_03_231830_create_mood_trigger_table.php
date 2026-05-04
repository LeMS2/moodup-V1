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
    Schema::create('mood_trigger', function (Blueprint $table) {
        $table->id();

        $table->foreignId('mood_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('trigger_id')
            ->constrained()
            ->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_trigger');
    }
};
