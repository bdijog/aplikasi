<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label(__('Doctor'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge(),

                TextColumn::make('day_of_week')
                    ->label(__('Practice Day'))
                    ->formatStateUsing(fn ($state) => [
                        0 => 'Minggu',
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                    ][$state] ?? '-')
                    ->sortable(),

                TextColumn::make('specific_date')
                    ->label(__('Special Date'))
                    ->date('d M Y')
                    ->placeholder('-'),

                TextColumn::make('start_time')
                    ->label(__('Start Time'))
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label(__('End Time'))
                    ->time('H:i'),

                TextColumn::make('max_patients')
                    ->label(__('Quota'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(ScheduleStatus::class),

                SelectFilter::make('type')
                    ->label(__('Schedule Type'))
                    ->options(ScheduleType::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
