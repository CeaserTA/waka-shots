<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Models\Testimonial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('client_name')
                    ->label('Client')
                    ->content(fn (Testimonial $record): string => $record->gallery?->client_name ?? 'Unknown client'),
                Placeholder::make('event_name')
                    ->label('Event')
                    ->content(fn (Testimonial $record): string => $record->gallery?->event_name ?? 'Unknown event'),
                Placeholder::make('quote')
                    ->label('Quote')
                    ->content(fn (Testimonial $record): string => $record->quote),
                Placeholder::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int|string|null $state): string => str_repeat('★', (int) $state) . str_repeat('☆', 5 - (int) $state)),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Featured')
                    ->helperText('Only approved testimonials are shown publicly.'),
                FileUpload::make('photo_path')
                    ->label('Photo')
                    ->disk('r2')
                    ->directory('testimonials')
                    ->image()
                    ->imageEditor(),
            ]);
    }
}
