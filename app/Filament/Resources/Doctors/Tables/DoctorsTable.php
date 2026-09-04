<?php

namespace App\Filament\Resources\Doctors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Dr&background=0D8ABC&color=fff'),

                TextColumn::make('name')
                    ->label(__('Doctor Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('specialty')
                    ->label(__('Specialty'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder('Umum'),

                TextColumn::make('license_number')
                    ->label(__('STR No.'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
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
