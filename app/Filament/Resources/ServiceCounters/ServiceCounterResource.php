<?php

namespace App\Filament\Resources\ServiceCounters;

use App\Filament\Resources\ServiceCounters\Pages\CreateServiceCounter;
use App\Filament\Resources\ServiceCounters\Pages\EditServiceCounter;
use App\Filament\Resources\ServiceCounters\Pages\ListServiceCounters;
use App\Filament\Resources\ServiceCounters\Schemas\ServiceCounterForm;
use App\Filament\Resources\ServiceCounters\Tables\ServiceCountersTable;
use App\Models\ServiceCounter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceCounterResource extends Resource
{
    protected static ?string $model = ServiceCounter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Master Data & System');
    }

    public static function getModelLabel(): string
    {
        return __('Service Counter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Service Counters');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceCounterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceCountersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceCounters::route('/'),
            'create' => CreateServiceCounter::route('/create'),
            'edit' => EditServiceCounter::route('/{record}/edit'),
        ];
    }
}
