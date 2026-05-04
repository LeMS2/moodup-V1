<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trigger;
use Illuminate\Http\Request;

class TriggerController extends Controller
{
    /**
     * Display a listing of the triggers.
     */
    public function index()
    {
        $triggers = Trigger::all();
        
        return response()->json([
            'success' => true,
            'data' => $triggers
        ]);
    }
}