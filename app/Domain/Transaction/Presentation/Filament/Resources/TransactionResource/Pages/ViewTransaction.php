<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource;
use App\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Chi tiết giao dịch';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm')
                ->label('Xác nhận')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận đã nhận tiền')
                ->modalSubmitActionLabel('Xác nhận')
                ->modalCancelActionLabel('Hủy')
                ->action(function (Transaction $record) {
                    $record->update([
                        'ops_status' => OpsStatusEnum::CONFIRMED,
                        'confirmed_by' => Auth::id(),
                        'confirmed_at' => now(),
                    ]);
                })
                ->visible(fn (Transaction $record) => $record->ops_status !== OpsStatusEnum::CONFIRMED),

            Action::make('reject')
                ->label('Từ chối')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Forms\Components\Textarea::make('ops_note')
                        ->label('Lý do từ chối')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Gửi')
                ->modalCancelActionLabel('Hủy')
                ->action(function (Transaction $record, array $data) {
                    $record->update([
                        'ops_status' => OpsStatusEnum::REJECTED,
                        'ops_note' => trim($record->ops_note . "\nTừ chối: " . $data['ops_note']),
                        'confirmed_by' => Auth::id(),
                        'confirmed_at' => now(),
                    ]);
                })
                ->visible(fn (Transaction $record) => $record->ops_status !== OpsStatusEnum::REJECTED),

            Action::make('hold')
                ->label('Tạm giữ')
                ->color('warning')
                ->icon('heroicon-o-pause-circle')
                ->form([
                    Forms\Components\Textarea::make('ops_note')
                        ->label('Lý do tạm giữ')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Gửi')
                ->modalCancelActionLabel('Hủy')
                ->action(function (Transaction $record, array $data) {
                    $record->update([
                        'ops_status' => OpsStatusEnum::ON_HOLD,
                        'ops_note' => trim($record->ops_note . "\nTạm giữ: " . $data['ops_note']),
                        'confirmed_by' => Auth::id(),
                        'confirmed_at' => now(),
                    ]);
                })
                ->visible(fn (Transaction $record) => !in_array($record->ops_status, [OpsStatusEnum::ON_HOLD, OpsStatusEnum::CONFIRMED])),

            Action::make('cancel_ops')
                ->label('Hủy vận hành')
                ->color('danger')
                ->icon('heroicon-o-archive-box-x-mark')
                ->form([
                    Forms\Components\Textarea::make('ops_note')
                        ->label('Lý do hủy')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Gửi')
                ->modalCancelActionLabel('Hủy')
                ->action(function (Transaction $record, array $data) {
                    $record->update([
                        'ops_status' => OpsStatusEnum::CANCELLED_OPS,
                        'ops_note' => trim($record->ops_note . "\nHủy: " . $data['ops_note']),
                        'confirmed_by' => Auth::id(),
                        'confirmed_at' => now(),
                    ]);
                })
                ->visible(fn (Transaction $record) => !in_array($record->ops_status, [OpsStatusEnum::CANCELLED_OPS, OpsStatusEnum::CONFIRMED])),
        ];
    }
}
