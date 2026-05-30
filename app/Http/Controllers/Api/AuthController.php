<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
            'sexo' => ['required', 'string', 'max:30'],
            'faixa_etaria' => ['required', 'string', 'max:30'],
            'estado' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accepted_terms' => ['required', 'accepted'],
        ]);

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'sexo' => $data['sexo'],
                'faixa_etaria' => $data['faixa_etaria'],
                'estado' => $data['estado'],
                'password' => Hash::make($data['password']),
                'accepted_terms_at' => now(),
            ]);

            // TIRA SE DER ERRADO

            DB::table('activity_logs')->insert([
    'user_id' => $user->id,
    'action' => 'register',
    'description' => 'Novo usuário cadastrado',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
]);

            // 🔥 CRIA O TOKEN (igual ao login)
            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
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

if ($user->desativado_em) {
    return response()->json([
        'message' => 'Sua conta está desativada. Para reativá-la, redefina sua senha.',
        'requires_password_reset' => true,
    ], 403);
}

$user->tokens()->delete();

$token = $user->createToken('mobile')->plainTextToken;

// TIRA SE NÃO DER CERTO:

    DB::table('activity_logs')->insert([
    'user_id' => $user->id,
    'action' => 'login',
    'description' => 'Usuário realizou login',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
]);

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
        
// TIRA SE NÃO DER CERTO
        DB::table('activity_logs')->insert([
    'user_id' => $user->id,
    'action' => 'accept_terms',
    'description' => 'Usuário aceitou os termos de uso',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
]);

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

    // VOLTA CÓDIGO ANTERIOR SE NÃO DER CERTO

    public function logout(Request $request)
{
    DB::table('activity_logs')->insert([
        'user_id' => $request->user()->id,
        'action' => 'logout',
        'description' => 'Usuário realizou logout',
        'ip_address' => $request->ip(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout realizado com sucesso.',
    ]);
}


    public function deactivateAccount(Request $request)
{
    $user = $request->user();

    $user->desativado_em = now();
    $user->save();

    // segurança: derruba todas as sessões/tokens
    $user->tokens()->delete();

    return response()->json([
        'message' => 'Conta desativada com sucesso.',
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

        // tira se não der certo 
        DB::table('activity_logs')->insert([
    'user_id' => $user->id,
    'action' => 'forgot_password',
    'description' => 'Solicitou recuperação de senha',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
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
    // 🔐 RESET SENHA (COM VALIDAÇÃO DE SENHA IGUAL)
    // ===============================

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        // Busca o registro do OTP
        $record = PasswordOtp::where('email', $request->email)->first();

        if (!$record) {
            return response()->json([
                'message' => 'Código inválido. Solicite um novo código.'
            ], 400);
        }

        // Verifica se o código expirou
        if (Carbon::now()->gt($record->expires_at)) {
            return response()->json([
                'message' => 'Código expirado. Solicite um novo código.'
            ], 400);
        }

        // Verifica se o código está correto
        if (!Hash::check($request->otp, $record->otp)) {
            return response()->json([
                'message' => 'Código incorreto. Verifique e tente novamente.'
            ], 400);
        }

        // Busca o usuário
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        // 🔥 VALIDAÇÃO CRÍTICA: Verificar se a nova senha é igual à antiga
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'A nova senha não pode ser igual à senha atual. Escolha uma senha diferente.'
            ], 400);
        }

        // Atualiza a senha
        // $user->password = Hash::make($request->password);
        // atualizando abauxo $user->save();

        // Atualiza a senha
$user->password = Hash::make($request->password);

// Se a conta estava desativada, reativa automaticamente
if ($user->desativado_em) {
    $user->desativado_em = null;
}

$user->save();

// TIRA SE NAO DER CERTO
        DB::table('activity_logs')->insert([
    'user_id' => $user->id,
    'action' => 'reset_password',
    'description' => 'Senha redefinida com sucesso',
    'ip_address' => $request->ip(),
    'created_at' => now(),
    'updated_at' => now(),
]);

        // 🔒 Segurança extra: Invalidar todos os tokens do usuário
        // Isso força o usuário a fazer login novamente em todos os dispositivos
        $user->tokens()->delete();

        // Remove o OTP usado
        $record->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso! Faça login com sua nova senha.'
        ]);
    }
}