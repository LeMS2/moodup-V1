<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mood;
use App\Models\User;
use App\Models\Trigger;
use Carbon\Carbon;

class MoodSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $dates = collect(range(0, 10))->map(fn($i) => Carbon::now()->subDays($i));

        foreach ($dates as $date) {

            $mood = Mood::create([
                'user_id' => $user->id,
                'title' => 'Dia comum',
                'date' => $date,
                'level' => rand(2, 5),
                'score' => rand(2, 5),
                'note' => fake()->sentence(),
                'mood' => ['muito_triste','triste','neutro','bem','muito_bem'][rand(0,4)],
            ]);

            // 🔥 anexar triggers aleatórias
            $triggerIds = \App\Models\Trigger::inRandomOrder()
                ->take(rand(1, 3))
                ->pluck('id');

            $mood->triggers()->sync($triggerIds);
        }
    }
}