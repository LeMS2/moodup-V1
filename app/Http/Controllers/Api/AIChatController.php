<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // ✅ resposta mock por enquanto (pra testar o front)
        // depois a gente liga num provider de IA (OpenAI, etc)
        $reply = "Entendi. Quer me contar um pouco mais sobre: " . $data['message'];

        return response()->json([
            'reply' => $reply,
        ]);
    }
}