<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor
{
    case UNPAID = 'unpaid';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed'; // Tambahkan status 'Selesai'
    case CANCELLED = 'cancelled'; // Tambahkan status 'Batal'

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => 'Belum Dibayar',
            self::PROCESSING => 'Diproses',
            self::SHIPPED => 'Dikirim',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNPAID, self::CANCELLED => 'danger',
            self::PROCESSING => 'warning',
            self::SHIPPED => 'info',
            self::COMPLETED => 'success',
        };
    }
}