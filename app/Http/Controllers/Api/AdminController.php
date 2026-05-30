<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getActivityLogs(Request $request)
    {
        $logs = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('activity_logs.created_at')
            ->paginate(50);
        
        $stats = [
            'total' => DB::table('activity_logs')->count(),
            'last_7_days' => DB::table('activity_logs')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'by_action' => DB::table('activity_logs')
                ->select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->get(),
        ];
        
        return response()->json(['logs' => $logs, 'stats' => $stats]);
    }

    public function getUsers(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
        
        return response()->json(['users' => $users, 'stats' => $stats]);
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:user,admin']);
        
        $user = User::findOrFail($id);
        
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Você não pode alterar sua própria role'], 403);
        }
        
        $user->role = $request->role;
        $user->save();
        
        DB::table('activity_logs')->insert([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_USER_ROLE',
            'description' => "Alterou role do usuário {$user->name} para {$request->role}",
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
        
        return response()->json(['message' => 'Role atualizada com sucesso', 'user' => $user]);
    }
}