<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- Translatable fields ---
                TextInput::make('title')
                    ->label(__('Announcement Title'))
                    ->required()
                    ->maxLength(255),

                Textarea::make('summary')
                    ->label(__('Summary'))
                    ->rows(2)
                    ->maxLength(500),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList', 'blockquote',
                        'h2', 'h3', 'link',
                    ])
                    ->columnSpanFull(),

                Hidden::make('slug'),

                Toggle::make('is_active')
                    ->label(__('Active Status'))
                    ->default(true),

                DateTimePicker::make('published_at')
                    ->label(__('Publish Date'))
                    ->seconds(false)
                    ->nullable(),

                DateTimePicker::make('expired_at')
                    ->label(__('Expiry Date'))
                    ->seconds(false)
                    ->nullable(),

                FileUpload::make('image')
                    ->label(__('Featured Image'))
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('announcements')
                    ->nullable(),
            ]);
    }
}
