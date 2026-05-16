<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sexo');
            $table->string('faixa_etaria');
            $table->string('estado');
           /* AJUSTAR PARA ESSE CASO NÃO DÊ BOA 
           $table->string('sexo')->after('email');
           $table->string('estado')->after('sexo'); 
            */
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['sexo', 'faixa_etaria', 'estado']);        });
    }
};