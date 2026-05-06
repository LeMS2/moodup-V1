<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubTrigger;
use App\Models\Trigger;

class SubTriggerController extends Controller
{
    public function index(Request $request)
    {
        $triggerName = $request->query('trigger');

        if (!$triggerName) {
            return response()->json([]);
        }

        $triggerName = strtolower($triggerName);

        $trigger = Trigger::whereRaw('LOWER(name) = ?', [$triggerName])->first();

        if (!$trigger) {
            return response()->json([]);
        }

        $subTriggers = $trigger->subTriggers()->get();

        return response()->json($subTriggers);
    }

    public function show($id)
    {
        $subTrigger = SubTrigger::with([
            'suggestions',
            'resources'
        ])->findOrFail($id);

        return response()->json([
            'sub_trigger' => $subTrigger->name,
            'intro_text' => $subTrigger->intro_text,
            'closing_text' => $subTrigger->closing_text,
            'suggestions' => $subTrigger->suggestions,
            'resources' => $subTrigger->resources
        ]);
    }
}
