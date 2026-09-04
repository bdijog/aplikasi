<?php

namespace App\Filament\Resources\Patients\Tables;

use App\Enums\Gender;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('medical_record_number')
                    ->label(__('RM No.'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label(__('Patient Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('national_id')
                    ->label(__('NIK'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),

                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge(),

                TextColumn::make('date_of_birth')
                    ->label(__('Date of Birth (short)'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('blood_type')
                    ->label(__('Blood Type (short)'))
                    ->badge()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class),

                SelectFilter::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ]),
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
