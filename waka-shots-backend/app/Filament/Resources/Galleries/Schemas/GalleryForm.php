<?php

namespace App\Filament\Resources\Galleries\Schemas;

use App\Support\DriveFolderUrl;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use InvalidArgumentException;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client & Event')
                ->schema([
                    TextInput::make('client_name')->required()->maxLength(255),
                    TextInput::make('event_name')->required()->maxLength(255),
                    DatePicker::make('event_date')->required(),
                ])
                ->columns(3),
            Section::make('Delivery & Access')
                ->description('Where the photos live, and how long the client can reach them.')
                ->schema([
                    TextInput::make('drive_folder_link')
                        ->label('Google Drive Folder Link')
                        ->required()
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($record): ?string => $record?->drive_folder_id ? 'https://drive.google.com/drive/folders/' . $record->drive_folder_id : null)
                        ->rule(function () {
                            return function (string $attribute, mixed $value, \Closure $fail): void {
                                try { DriveFolderUrl::extractId((string) $value); }
                                catch (InvalidArgumentException $exception) { $fail($exception->getMessage()); }
                            };
                        }),
                    DatePicker::make('expires_at')->default(now()->addDays(30)->toDateString()),
                    Toggle::make('is_active')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
