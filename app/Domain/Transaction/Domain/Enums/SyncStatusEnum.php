<?php

namespace App\Domain\Transaction\Domain\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum SyncStatusEnum: string implements HasLabel, HasColor
{
    case PENDING = 'PENDING';
    case RECEIVED_SIGNAL = 'RECEIVED_SIGNAL';
    case EXPIRED = 'EXPIRED';
    case FAILED = 'FAILED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Chờ thanh toán',
            self::RECEIVED_SIGNAL => 'Đã nhận tín hiệu',
            self::EXPIRED => 'Hết hạn (auto)',
            self::FAILED => 'Lỗi kỹ thuật',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::RECEIVED_SIGNAL => 'success',
            self::EXPIRED => 'gray',
            self::FAILED => 'danger',
        };
    }
}
