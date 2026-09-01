<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Models\Package;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                Textarea::make('features_text')
                    ->label('What is included')
                    ->helperText('One feature per line. Add, edit, or delete lines and save — the included list is replaced with exactly what you enter, in that order.')
                    ->placeholder("Full day coverage\n2 photographers\nOnline gallery\nPrint release")
                    ->rows(6)
                    ->columnSpanFull()
                    ->afterStateHydrated(function (Textarea $component, ?Package $record): void {
                        if ($record?->exists) {
                            $component->state($record->packageFeatures()->pluck('feature_text')->implode("\n"));
                        }
                    }),
            ]);
    }

    public static function syncFeatures(Package $record, ?string $featuresText): void
    {
        $features = collect(preg_split('/\r\n|\r|\n/', (string) $featuresText))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        $record->packageFeatures()->delete();

        if ($features->isEmpty()) {
            return;
        }

        $record->packageFeatures()->createMany(
            $features->map(fn (string $text) => ['feature_text' => $text])->all()
        );
    }
}
