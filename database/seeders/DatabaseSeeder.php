<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

        public function run(): void
{
    $this->call([
        TriggerSeeder::class,
        SubTriggerSeeder::class,
        ResourceSeeder::class,
        SubTriggerResourceSeeder::class,
        UserSeeder::class,
        MoodSeeder::class,
        SuggestionSeeder::class,
        
    ]);
} 
}
