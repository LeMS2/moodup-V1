<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 🔥 OTP
use App\Models\PasswordOtp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

            return response()->json([
                'user' => $user
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
            'accepted_terms_at' => $user->accepted_terms_at,
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
    // 🔐 ESQUECI SENHA (OTP)
    // ===============================

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        // 🔥 gera OTP 6 dígitos
        $otp = rand(100000, 999999);

        // remove OTPs antigos
        PasswordOtp::where('email', $request->email)->delete();

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // 📧 envia email
        Mail::raw("Seu código MoodUp é: $otp\nExpira em 10 minutos.", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Recuperação de senha - MoodUp');
        });

        return response()->json([
            'message' => 'Código enviado para o email'
        ]);
    }

    // ===============================
    // 🔐 RESET SENHA COM OTP
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

        // ⏳ verifica expiração
        if (Carbon::now()->gt($record->expires_at)) {
            return response()->json(['message' => 'Código expirado'], 400);
        }

        // 🔐 verifica OTP
        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json(['message' => 'Código incorreto'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        // 🔥 atualiza senha
        $user->password = Hash::make($request->password);
        $user->save();

        // 🧹 remove OTP
        $record->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso'
        ]);
    }
}