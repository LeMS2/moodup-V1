<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\PasswordOtp;
use Carbon\Carbon;
use SendGrid\Mail\Mail;

class AuthController extends Controller
{
    // 🔥 MÉTODOS AUXILIARES PARA PADRONIZAÇÃO
    private function successResponse($message, $data = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private function errorResponse($message, $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    // ===============================
    // 📝 REGISTER (PADRONIZADO)
    // ===============================
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

            $token = $user->createToken('mobile')->plainTextToken;

            return $this->successResponse('Cadastro realizado com sucesso', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'accepted_terms' => true,
            ], 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao realizar cadastro', $e->getMessage(), 500);
        }
    }

    // ===============================
    // 🔐 LOGIN (PADRONIZADO)
    // ===============================
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Credenciais inválidas', null, 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse('Login realizado com sucesso', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'accepted_terms' => !is_null($user->accepted_terms_at),
        ]);
    }

    // ===============================
    // ✅ ACEITAR TERMOS (PADRONIZADO)
    // ===============================
    public function acceptTerms(Request $request)
    {
        $user = $request->user();

        $user->accepted_terms_at = now();
        $user->save();

        return $this->successResponse('Termos aceitos com sucesso', [
            'accepted_terms' => true,
            'accepted_at' => $user->accepted_terms_at,
        ]);
    }

    // ===============================
    // 👤 MEUS DADOS (PADRONIZADO)
    // ===============================
    public function me(Request $request)
    {
        $user = $request->user();
        
        return $this->successResponse('Dados do usuário', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'accepted_terms' => !is_null($user->accepted_terms_at),
        ]);
    }

    // ===============================
    // 🚪 LOGOUT (PADRONIZADO)
    // ===============================
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logout realizado com sucesso');
    }

    // ===============================
    // 📧 ESQUECI SENHA (PADRONIZADO)
    // ===============================
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Segurança: não revela se usuário existe
        if (!$user) {
            return $this->successResponse('Se o email existir, você receberá o código');
        }

        $otp = rand(100000, 999999);
        PasswordOtp::where('email', $request->email)->delete();

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => Hash::make($otp),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Envio de email com SendGrid
        $html = "
        <div style='font-family:sans-serif;padding:20px'>
            <h2 style='color:#2dd4bf'>MoodUp 💙</h2>
            <p>Use o código abaixo para redefinir sua senha:</p>
            <div style='font-size:32px;font-weight:bold;margin:20px 0'>$otp</div>
            <p>Esse código expira em 10 minutos.</p>
            <small>Se você não solicitou, ignore este email.</small>
        </div>";

        $email = new Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Código de recuperação - MoodUp");
        $email->addTo($request->email);
        $email->addContent("text/html", $html);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));

        try {
            $sendgrid->send($email);
            return $this->successResponse('Código enviado para seu email');
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao enviar email', $e->getMessage(), 500);
        }
    }

    // ===============================
    // 🔐 RESET SENHA (PADRONIZADO)
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
            return $this->errorResponse('Código inválido', null, 400);
        }

        if (Carbon::now()->gt($record->expires_at)) {
            return $this->errorResponse('Código expirado', null, 400);
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return $this->errorResponse('Código incorreto', null, 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('Usuário não encontrado', null, 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $record->delete();

        return $this->successResponse('Senha redefinida com sucesso');
    }
}