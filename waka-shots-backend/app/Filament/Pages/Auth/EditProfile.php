<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserRole;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getRoleFormComponent(),
                    ])
                    ->columns(2),
                Section::make('Password')
                    ->description('Leave blank to keep your current password.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->label('Role')
            ->options(collect(UserRole::cases())->mapWithKeys(
                fn (UserRole $role): array => [$role->value => $role->label()],
            ))
            ->disabled()
            ->dehydrated(false)
            ->helperText('Contact another admin to change your role.');
    }
}
