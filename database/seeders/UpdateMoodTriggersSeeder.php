<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMoodTriggersSeeder extends Seeder
{
    public function run()
    {
        DB::table('moods')->get()->each(function ($mood) {
            $trigger = DB::table('triggers')
                ->where('name', $mood->triggers)
                ->first();

            if ($trigger) {
                DB::table('moods')
                    ->where('id', $mood->id)
                    ->update(['trigger_id' => $trigger->id]);
            }
        });
    }
}