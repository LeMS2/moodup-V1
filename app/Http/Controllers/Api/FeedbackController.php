<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    
public function store(Request $request)
    {
        $data = $request->validade([
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            ...$data
        ]);

        return response()->json([
            'sucess' => true,
            'message' => 'Feedback enviado com sucesso',
            'data' => $feedback
        ]);
    }
}
