<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * Logika ini akan dipanggil oleh $this->authorize('view', $order)
     */
    public function view(User $user, Order $order): bool
    {
        // Kembalikan 'true' HANYA JIKA ID pengguna yang diautentikasi
        // sama dengan ID pengguna yang terkait dengan pesanan.
        return $user->id === $order->user_id;
    }

    /**
     * (Opsional) Anda bisa menambahkan method lain di sini untuk masa depan,
     * misalnya, untuk membatalkan pesanan.
     *
     * public function cancel(User $user, Order $order): bool
     * {
     *     return $user->id === $order->user_id && $order->status === 'unpaid';
     * }
     */
}