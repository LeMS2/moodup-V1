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
    Schema::create('sub_triggers', function (Blueprint $table) {
        $table->id();
        $table->string('trigger'); // ex: escola, trabalho
        $table->string('name'); // ex: Período de provas
        $table->text('intro_text')->nullable(); // mensagem inicial
        $table->text('closing_text')->nullable(); // fechamento
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_triggers');
    }
};
