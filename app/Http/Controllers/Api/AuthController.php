<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ActivityLoggerService;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Kita buat password dummy karena login kita tidak menggunakannya
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make(Str::random(10)),
            'is_admin' => false,
        ]);

        // Buat token untuk user yang baru daftar
        $token = $user->createToken('auth_token')->plainTextToken;
        $activityLogger->log('user_registered', $user);
        
        return response()->json([
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cari user berdasarkan nomor handphone
        $user = User::where('phone_number', $request->phone_number)->first();

        // Jika user tidak ditemukan
        if (!$user) {
            return response()->json(['message' => 'Nomor handphone tidak terdaftar.'], 401);
        }
        
        // Hapus token lama jika ada, dan buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}