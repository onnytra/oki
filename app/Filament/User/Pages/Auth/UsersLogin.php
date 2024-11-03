<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Http\Responses\Auth\LoginResponse;
class UsersLogin extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('nis')
            ->label('NIS')
            ->numeric()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'nis' => $data['nis'],
            'password'  => $data['password'],
        ];
    }
    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.nis' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
    }
}
