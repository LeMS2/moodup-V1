<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        // ✅ validação correta
        $data = $request->validate([
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        // ✅ cria o feedback vinculado ao usuário logado
        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            ...$data
        ]);

        // ✅ resposta correta
        return response()->json([
            'success' => true,
            'message' => 'Feedback enviado com sucesso',
            'data' => $feedback
        ]);
    }
}
