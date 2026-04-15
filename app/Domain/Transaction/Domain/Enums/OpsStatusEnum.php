<?php

namespace App\Domain\Transaction\Domain\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum OpsStatusEnum: string implements HasLabel, HasColor
{
    case UNREVIEWED = 'UNREVIEWED';
    case CONFIRMED = 'CONFIRMED';
    case REJECTED = 'REJECTED';
    case ON_HOLD = 'ON_HOLD';
    case CANCELLED_OPS = 'CANCELLED_OPS';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNREVIEWED => 'Chưa xem xét',
            self::CONFIRMED => 'Đã xác nhận',
            self::REJECTED => 'Từ chối',
            self::ON_HOLD => 'Tạm giữ',
            self::CANCELLED_OPS => 'Hủy vận hành',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::UNREVIEWED => 'gray',
            self::CONFIRMED => 'success',
            self::REJECTED => 'danger',
            self::ON_HOLD => 'warning',
            self::CANCELLED_OPS => 'danger',
        };
    }
}
