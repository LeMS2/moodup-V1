<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('sexo')->nullable();
        $table->string('faixa_etaria')->nullable();
        $table->string('estado')->nullable();
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['sexo', 'faixa_etaria', 'estado']);        });
    }
};