<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubTrigger;
use App\Models\Trigger;

class SubTriggerController extends Controller
{
    // 🔥 NOVO MÉTODO
    public function index(Request $request)
    {
        $triggerName = $request->query('trigger');

        if (!$triggerName) {
            return response()->json([]);
        }

        // 🔥 normaliza (pra evitar erro com acento/maiúscula)
        $triggerName = strtolower($triggerName);

        // tenta encontrar o trigger pelo nome
        $trigger = Trigger::whereRaw('LOWER(name) = ?', [$triggerName])->first();

        if (!$trigger) {
            return response()->json([]);
        }

        // pega os subgatilhos relacionados
        $subTriggers = $trigger->subTriggers()->get();

        return response()->json($subTriggers);
    }

    // 🔥 JÁ EXISTIA (mantém)
    public function show($id)
    {
        $subTrigger = SubTrigger::with([
            'suggestions',
            'resources'
        ])->findOrFail($id);

        return response()->json([
            'sub_trigger' => $subTrigger->name,
            'suggestions' => $subTrigger->suggestions,
            'resources' => $subTrigger->resources
        ]);
    }
}
}
