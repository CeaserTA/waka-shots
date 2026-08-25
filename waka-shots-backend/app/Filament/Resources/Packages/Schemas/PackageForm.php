<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('tier_name')
                    ->label('Package / Tier Name')
                    ->placeholder('e.g., Silver, Gold, Platinum')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
            ]);
    }
}
