<?php

namespace Source\Domain\Account\Domain\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum UserStatusEnum: string implements HasLabel, HasColor
{
    case ACTIVE = 'ACTIVE';
    case DISABLED = 'DISABLED';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Hoạt động',
            self::DISABLED => 'Vô hiệu hóa',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::DISABLED => 'danger',
        };
    }
}
