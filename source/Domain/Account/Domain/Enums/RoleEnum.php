<?php

namespace Source\Domain\Account\Domain\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum RoleEnum: string implements HasLabel, HasColor
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMIN = 'ADMIN';
    case OPERATOR = 'OPERATOR';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'SUPER_ADMIN',
            self::ADMIN => 'ADMIN',
            self::OPERATOR => 'OPERATOR',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SUPER_ADMIN => 'danger',
            self::ADMIN => 'warning',
            self::OPERATOR => 'info',
        };
    }
}
