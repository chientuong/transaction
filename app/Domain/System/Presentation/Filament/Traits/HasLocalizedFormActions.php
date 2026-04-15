<?php

namespace App\Domain\System\Presentation\Filament\Traits;

use Filament\Actions\Action;

trait HasLocalizedFormActions
{
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Tạo mới');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Tạo & Tiếp tục thêm');
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Lưu thay đổi');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Hủy bỏ');
    }
}
