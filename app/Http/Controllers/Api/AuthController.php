<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 🔥 OTP
use App\Models\PasswordOtp;
use Carbon\Carbon;

// 🚀 SENDGRID API
use SendGrid\Mail\Mail;

class AuthController extends Controller
{
   public function register(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'accepted_terms' => ['required', 'accepted'],
    ]);

    try {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'accepted_terms_at' => now(),
        ]);

        // 🔥 CRIA O TOKEN (igual ao login)
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,           // ← ADICIONE ISSO
            'accepted_terms' => true,
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'erro_real' => $e->getMessage()
        ], 500);
    }
}

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'accepted_terms' => !is_null($user->accepted_terms_at),
        ]);
    }

    public function acceptTerms(Request $request)
    {
        $user = $request->user();

        $user->accepted_terms_at = now();
        $user->save();

        return response()->json([
            'message' => 'Termos aceitos com sucesso.',
            'accepted_terms' => true,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'accepted_terms' => !is_null($request->user()->accepted_terms_at),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    // ===============================
    // 🔐 ESQUECI SENHA (OTP + SENDGRID)
    // ===============================

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        // 🔒 NÃO revela se usuário existe
        if (!$user) {
            return response()->json([
                'message' => 'Se o email existir, você receberá o código.'
            ]);
        }

        // 🔢 gera OTP
        $otp = rand(100000, 999999);

        // 🧹 remove antigos
        PasswordOtp::where('email', $request->email)->delete();

        // 💾 salva hash
        PasswordOtp::create([
            'email' => $request->email,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // 📧 email bonito
        $html = "
        <div style='font-family:sans-serif;padding:20px'>
            <h2 style='color:#2dd4bf'>MoodUp 💙</h2>
            <p>Use o código abaixo para redefinir sua senha:</p>

            <div style='font-size:32px;font-weight:bold;margin:20px 0'>
                $otp
            </div>

            <p>Esse código expira em 10 minutos.</p>
            <small>Se você não solicitou, ignore este email.</small>
        </div>
        ";

        // 🚀 SENDGRID API
        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Código de recuperação - MoodUp");
        $email->addTo($request->email);
        $email->addContent("text/html", $html);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $sendgrid->send($email);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao enviar email',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Código enviado com sucesso'
        ]);
    }

    // ===============================
    // 🔐 RESET SENHA
    // ===============================

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = PasswordOtp::where('email', $request->email)->first();

        if (!$record) {
            return response()->json(['message' => 'Código inválido'], 400);
        }

        if (Carbon::now()->gt($record->expires_at)) {
            return response()->json(['message' => 'Código expirado'], 400);
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json(['message' => 'Código incorreto'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $record->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso'
        ]);
    }
}