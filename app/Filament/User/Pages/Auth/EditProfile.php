<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

use Filament\Pages\Page;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nis')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('NIS')
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->numeric()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('school')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->revealable()
                    ->dehydrated(fn($state): bool => filled($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->same('password')
                    ->minLength(8)
                    ->maxLength(255)
                    ->revealable()
                    ->dehydrated(fn($state): bool => filled($state)),
            ]);
    }
}
