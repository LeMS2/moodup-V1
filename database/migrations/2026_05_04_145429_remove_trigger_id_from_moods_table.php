<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('moods', 'trigger_id')) {
            Schema::table('moods', function (Blueprint $table) {
                $table->dropColumn('trigger_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('moods', function (Blueprint $table) {
            $table->foreignId('trigger_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};