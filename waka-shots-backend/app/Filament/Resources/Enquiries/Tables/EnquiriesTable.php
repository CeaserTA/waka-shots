<?php

namespace App\Filament\Resources\Enquiries\Tables;

use App\Models\Enquiry;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // New enquiries arrive from the public site at any time — without
            // this the list only updated on a manual refresh.
            ->poll('10s')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'new',
                        'primary' => 'contacted',
                        'success' => 'booked',
                        'gray' => 'closed',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable(),
                TextColumn::make('preferred_date')
                    ->label('Event Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'booked' => 'Booked',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('service_id')
                    ->relationship('service', 'name')
                    ->label('Service'),
            ])
            ->recordActions([
                ViewAction::make(),
                // Hand-off links rather than an in-app messaging system —
                // each opens whatever the admin already uses on that device.
                ActionGroup::make([
                    Action::make('email')
                        ->label('Email')
                        ->icon(Heroicon::OutlinedEnvelope)
                        ->url(fn (Enquiry $record): string => 'mailto:' . $record->email . '?subject=' . rawurlencode(self::subjectFor($record)))
                        ->openUrlInNewTab(),
                    Action::make('whatsapp')
                        ->label('WhatsApp')
                        ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                        // wa.me needs digits only, no +, spaces or dashes.
                        ->url(fn (Enquiry $record): string => 'https://wa.me/' . preg_replace('/\D/', '', (string) $record->phone) . '?text=' . rawurlencode(self::greetingFor($record)))
                        ->openUrlInNewTab()
                        ->visible(fn (Enquiry $record): bool => filled($record->phone)),
                    Action::make('call')
                        ->label('Call')
                        ->icon(Heroicon::OutlinedPhone)
                        ->url(fn (Enquiry $record): string => 'tel:' . preg_replace('/[^\d+]/', '', (string) $record->phone))
                        ->visible(fn (Enquiry $record): bool => filled($record->phone)),
                ])
                    ->label('Contact')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function subjectFor(Enquiry $enquiry): string
    {
        $studio = SiteSetting::current()->studio_name ?: 'Waka Shots';

        return $enquiry->service?->name
            ? "Re: your {$enquiry->service->name} enquiry — {$studio}"
            : "Re: your enquiry — {$studio}";
    }

    private static function greetingFor(Enquiry $enquiry): string
    {
        $studio = SiteSetting::current()->studio_name ?: 'Waka Shots';
        $firstName = strtok(trim($enquiry->name), ' ') ?: $enquiry->name;

        return "Hi {$firstName}, this is {$studio} following up on your enquiry.";
    }
}
