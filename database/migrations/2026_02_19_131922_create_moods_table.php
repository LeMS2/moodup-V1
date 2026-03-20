<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('moods', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->date('date');                 // data do registro
        $table->unsignedTinyInteger('level'); // 1 a 5 (ou 1 a 10, você decide)
        $table->text('note')->nullable();     // observação opcional

        $table->timestamps();

        // evita 2 registros no mesmo dia por usuário
        $table->unique(['user_id', 'date']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moods');
    }
};
