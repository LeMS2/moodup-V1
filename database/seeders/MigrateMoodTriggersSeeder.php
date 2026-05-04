<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateMoodTriggersSeeder extends Seeder
{
    public function run()
    {
        DB::table('moods')->get()->each(function ($mood) {

            if (empty($mood->triggers)) return;

            $triggers = json_decode($mood->triggers, true);

            if (!is_array($triggers)) return;

            foreach ($triggers as $triggerName) {

                $trigger = DB::table('triggers')
                    ->whereRaw('LOWER(name) = ?', [strtolower(trim($triggerName))])
                    ->first();

                if ($trigger) {
                    DB::table('mood_trigger')->updateOrInsert(
                        [
                            'mood_id' => $mood->id,
                            'trigger_id' => $trigger->id
                        ],
                        [] // evita erro no postgres
                    );
                }
            }
        });
    }
}