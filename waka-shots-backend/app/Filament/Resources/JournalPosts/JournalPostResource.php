<?php

namespace App\Filament\Resources\JournalPosts;

use App\Filament\Resources\JournalPosts\Pages\CreateJournalPost;
use App\Filament\Resources\JournalPosts\Pages\EditJournalPost;
use App\Filament\Resources\JournalPosts\Pages\ListJournalPosts;
use App\Filament\Resources\JournalPosts\Schemas\JournalPostForm;
use App\Filament\Resources\JournalPosts\Tables\JournalPostsTable;
use App\Models\JournalPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JournalPostResource extends Resource
{
    protected static ?string $model = JournalPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Journal Posts';

    public static function form(Schema $schema): Schema
    {
        return JournalPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JournalPostsTable::configure($table);
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
            'index' => ListJournalPosts::route('/'),
            'create' => CreateJournalPost::route('/create'),
            'edit' => EditJournalPost::route('/{record}/edit'),
        ];
    }
}
