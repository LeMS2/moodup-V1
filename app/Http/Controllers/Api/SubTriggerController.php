<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubTriggerController extends Controller
{
    public function show($id)
{
    $subTrigger = \App\Models\SubTrigger::with([
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
