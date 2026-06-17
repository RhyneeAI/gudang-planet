<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class TelescopeAuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah punya token di session, cek validitasnya
        if (session('telescope_token')) {
            $token = session('telescope_token');
            $accessToken = PersonalAccessToken::findToken($token);
            
            // Jika token valid dan user masih ada, redirect ke telescope
            if ($accessToken && $accessToken->tokenable) {
                $tokenUser = $accessToken->tokenable;
                
                // Hanya SuperAdmin yang bisa akses Telescope
                if ($tokenUser->role->value === Role::SUPERADMIN->value) {
                    return redirect('/telescope');
                }
            }
            
            // Token tidak valid atau bukan SuperAdmin, clear session
            session()->forget(['telescope_token', 'telescope_user']);
        }

        return view('telescope-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['phone' => 'Credential not valid.']);
        }

        if ($user->role->value !== Role::SUPERADMIN->value) {
            return back()->withErrors(['phone' => 'Unathorized.']);
        }

        // Buat token baru untuk Telescope session
        $token = $user->createToken('telescope-token')->plainTextToken;

        session([
            'telescope_token' => $token,
            'telescope_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role->value,
            ],
        ]);

        return redirect('/telescope');
    }

    public function logout(Request $request)
    {
        // Hapus token dari database
        if ($token = session('telescope_token')) {
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                $accessToken->delete();
            }
        }

        // Clear session
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('telescope.login');
    }
}
