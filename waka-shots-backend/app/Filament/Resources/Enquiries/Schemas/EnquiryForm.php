<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Models\Enquiry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        $readOnlyOnEdit = fn (string $operation): bool => $operation === 'edit';

        return $schema
            ->components([
                Section::make('Status & Follow-up')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'booked' => 'Booked',
                                'closed' => 'Closed',
                            ])
                            ->native(false)
                            ->required()
                            ->default('new'),
                    ]),
                Section::make('Client Contact Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Client Name')
                            ->required()
                            ->disabled($readOnlyOnEdit),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->disabled($readOnlyOnEdit),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->disabled($readOnlyOnEdit),
                    ])
                    ->columns(3),
                Section::make('Event & Booking Details')
                    ->schema([
                        Select::make('service_id')
                            ->relationship('service', 'name')
                            ->label('Requested Service')
                            ->searchable()
                            ->preload()
                            ->disabled($readOnlyOnEdit),
                        Select::make('package_id')
                            ->relationship('package', 'tier_name')
                            ->label('Requested Package')
                            ->searchable()
                            ->preload()
                            ->disabled($readOnlyOnEdit),
                        DatePicker::make('preferred_date')
                            ->label('Preferred Date')
                            ->disabled($readOnlyOnEdit),
                        TextInput::make('location')
                            ->label('Location / Venue')
                            ->disabled($readOnlyOnEdit),
                        TextInput::make('budget')
                            ->label('Budget')
                            ->disabled($readOnlyOnEdit)
                            // No longer collected on the public form — kept
                            // so older enquiries that did capture it still
                            // show it, rather than silently hiding data.
                            ->visible(fn (?Enquiry $record): bool => filled($record?->budget)),
                        Textarea::make('details')
                            ->label('Additional Details / Message')
                            ->columnSpanFull()
                            ->disabled($readOnlyOnEdit),
                    ])
                    ->columns(2),
            ]);
    }
}
