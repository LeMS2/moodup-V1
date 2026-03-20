<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'author')) {
                $table->string('author', 120)->nullable()->after('url');
            }
            if (!Schema::hasColumn('resources', 'duration_minutes')) {
                $table->unsignedSmallInteger('duration_minutes')->nullable()->after('author');
            }
            if (!Schema::hasColumn('resources', 'tags')) {
                $table->json('tags')->nullable()->after('duration_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'tags')) $table->dropColumn('tags');
            if (Schema::hasColumn('resources', 'duration_minutes')) $table->dropColumn('duration_minutes');
            if (Schema::hasColumn('resources', 'author')) $table->dropColumn('author');
        });
    }
};