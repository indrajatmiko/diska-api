<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Mail\AccountDeletionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Menangani permintaan penghapusan akun oleh pengguna.
     */
    public function requestDeletion(Request $request)
    {
        // 1. Dapatkan pengguna yang sedang diautentikasi
        $user = $request->user();

        // 2. Validasi input dengan konfirmasi berlapis
        $validated = $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'confirmation_text' => 'required|string',
        ]);

        // 3. Verifikasi Identitas Pengguna
        if ($validated['email'] !== $user->email) {
            return response()->json(['message' => 'Email yang dimasukkan tidak cocok dengan akun Anda.'], 422);
        }
        if ($validated['phone_number'] !== $user->phone_number) {
            return response()->json(['message' => 'Nomor handphone yang dimasukkan tidak cocok dengan akun Anda.'], 422);
        }

        // 4. Verifikasi Teks Konfirmasi
        if (strtolower($validated['confirmation_text']) !== 'hapus') {
            return response()->json(['message' => 'Teks konfirmasi tidak valid. Harap ketik "hapus".'], 422);
        }

        // --- Proses Penghapusan ---
        // Simpan info untuk notifikasi sebelum user dihapus
        $userName = $user->name;
        $userEmail = $user->email;
        $adminEmail = 'kauniyahstoremyid@gmail.com';

        // Hapus semua token akses pengguna
        $user->tokens()->delete();
        
        // Hapus pengguna dari database
        // Semua data yang terhubung dengan onDelete('cascade') akan ikut terhapus (misal: alamat, pesanan)
        $user->delete();

        // Kirim notifikasi email konfirmasi
        try {
            // Kirim ke pengguna yang baru saja dihapus
            Mail::to($userEmail)->send(new AccountDeletionNotification($userName, $userEmail));
            
            // Kirim notifikasi ke admin
            Mail::raw(
                "Pengguna dengan detail berikut telah berhasil menghapus akunnya:\n\nNama: {$userName}\nEmail: {$userEmail}",
                fn ($message) => $message->to($adminEmail)->subject('Notifikasi Penghapusan Akun Pengguna')
            );
        } catch (\Exception $e) {
            // Jangan gagalkan proses jika email gagal terkirim, cukup catat errornya
            report($e);
        }
        
        // Kembalikan respons sukses
        return response()->json(['message' => 'Akun Anda telah berhasil dihapus.']);
    }
}