<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Service Name')
                    ->placeholder('e.g., Weddings, Commercial, Portraits')
                    ->required()
                    ->maxLength(255),
                Toggle::make('has_packages')
                    ->label('Offers Packages')
                    ->default(true),
            ]);
    }
}
