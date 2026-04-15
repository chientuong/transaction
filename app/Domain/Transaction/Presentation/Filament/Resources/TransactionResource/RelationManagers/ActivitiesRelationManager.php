<?php

namespace App\Domain\Transaction\Presentation\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Collection;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activitiesAsSubject';

    protected static ?string $title = 'Lịch sử thay đổi (Audit Log)';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->label('Hành động')
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    }),
                TextColumn::make('causer.name')
                    ->label('Người thực hiện')
                    ->getStateUsing(function ($record) {
                        if ($record->causer) {
                            return $record->causer->name;
                        }

                        // Heuristic for API and System
                        if ($record->event === 'created') {
                            return 'API';
                        }

                        return 'SYSTEM';
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'API' => 'info',
                        'SYSTEM' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('attribute_changes')
                    ->label('Chi tiết thay đổi')
                    ->wrap()
                    ->getStateUsing(function ($record) {
                        $data = $record->attribute_changes instanceof Collection
                            ? $record->attribute_changes->toArray()
                            : (array) $record->attribute_changes;

                        $attributes = $data['attributes'] ?? [];
                        $old = $data['old'] ?? [];

                        if (empty($attributes)) {
                            return '-';
                        }

                        $changes = [];
                        foreach ($attributes as $key => $value) {
                            $oldValue = array_key_exists($key, $old) ? ($old[$key] ?? 'NULL') : 'N/A';
                            $newValue = $value ?? 'NULL';
                            $changes[] = "<strong>{$key}</strong>: {$oldValue} → {$newValue}";
                        }

                        return implode('<br>', $changes);
                    })
                    ->html(),
                TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
