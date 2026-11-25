<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // REGISTER


    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'nullable|email|max:150|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:3|max:100',
            'nama_tenants' => 'required|string|max:100',
            'kategori_usaha' => 'nullable|string|max:60',
            'penanggung_jawab' => 'required|string|max:100',
        ]);

        $result = DB::transaction(function () use ($data) {

            // Membuat slug otomatis dari nama tenant
            $baseSlug = Str::slug($data['nama_tenants']);

            if (empty($baseSlug)) {
                $baseSlug = 'tenant';
            }

            $slug = $baseSlug;
            $counter = 1;

            // Memastikan slug tidak duplikat
            while (DB::table('tenants')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Membuat tenant baru
            $tenantId = DB::table('tenants')->insertGetId([
                'nama_tenants' => $data['nama_tenants'],
                'slug' => $slug,
                'kategori_usaha' => $data['kategori_usaha'] ?? null,
                'penanggung_jawab' => $data['penanggung_jawab'],
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Membuat akun Admin Tenant
            $user = new User();

            $user->forceFill([
                'tenant_id' => $tenantId,
                'nama' => $data['nama'],
                'email' => $data['email'] ?? null,
                'username' => $data['username'],
                'password' => Md5Password::make($data['password']),
                'role' => User::ADMIN_TENANT,
                'status' => 'aktif',
            ]);

            $user->save();

            return $user;
        });

        // Membuat token otomatis setelah registrasi
        $token = $result->createToken('kasirku-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil!',
            'user' => $result,
            'token' => $token,
        ], 201);
    }


    // LOGIN
    public function login(Request $request)
    {
        $data = $request->validate([
            'identitas' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $data['identitas'])
            ->orWhere('email', $data['identitas'])
            ->first();

        if (
            !$user ||
            !Md5Password::check($data['password'], $user->password)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah!'
            ], 401);
        }

        if (!$user->isActive() || $user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif atau sedang terkunci!'
            ], 403);
        }

        if (
            $user->tenant_id &&
            !$user->tenant?->isActive()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak aktif!'
            ], 403);
        }

        $user->forceFill([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save();

        $token = $user->createToken('kasirku-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!'
        ]);
    }
}
