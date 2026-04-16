<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\AuditLogResource\Pages;

use App\Domain\Transaction\Presentation\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ExportAction;
use App\Domain\Transaction\Presentation\Filament\Exporters\AuditLogExporter;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(AuditLogExporter::class)
                ->label('Xuất Excel')
                ->modalHeading('Xuất Audit Log'),
        ];
    }
}
