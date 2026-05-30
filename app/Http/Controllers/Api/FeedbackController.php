<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;

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

        // TIRA SE DER ERRADO

        DB::table('activity_logs')->insert([
    'user_id' => $request->user()->id,
    'action' => 'feedback_sent',
    'description' => 'Usuário enviou feedback para o sistema',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
]);

        // ✅ resposta correta
        return response()->json([
            'success' => true,
            'message' => 'Feedback enviado com sucesso',
            'data' => $feedback
        ]);
    }
}
