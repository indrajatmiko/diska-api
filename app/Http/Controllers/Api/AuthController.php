<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Str;
use App\Mail\UserRegistrationOtpNotification;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request, ActivityLoggerService $activityLogger)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users',
        ]);

        // 1. Generate OTP 6 digit dan waktu kedaluwarsa
        $otpCode = random_int(100000, 999999);
        $otpExpiresAt = now()->addMinutes(30);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make(Str::random(10)),
            'is_admin' => false,
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        // 2. Kirim email ke administrator
        Mail::to('kauniyahstoremyid@gmail.com')->send(new UserRegistrationOtpNotification($user, (string)$otpCode));

        $activityLogger->log('user_registered', $user);
        
        // 3. JANGAN kembalikan token. Beri pesan bahwa verifikasi diperlukan.
        return response()->json([
            'message' => 'Registrasi berhasil. Silakan masukkan kode verifikasi.',
            'user' => ['phone_number' => $user->phone_number] // Kirim no HP untuk halaman verifikasi
        ], 201);
    }

    /**
     * Handle OTP verification and login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
            'otp_code' => 'required|string|digits:6',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        // Cek jika user sudah terverifikasi
        if ($user->email_verified_at) {
            return response()->json(['message' => 'Akun ini sudah terverifikasi.'], 422);
        }

        // Cek jika OTP salah atau sudah kedaluwarsa
        if ($user->otp_code !== $request->otp_code || now()->isAfter($user->otp_expires_at)) {
            return response()->json(['message' => 'Kode verifikasi tidak valid atau telah kedaluwarsa.'], 422);
        }

        // Jika berhasil, verifikasi user dan hapus OTP
        $user->email_verified_at = now(); // Kita gunakan email_verified_at sebagai penanda
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Buat token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Verifikasi berhasil. Selamat datang!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
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