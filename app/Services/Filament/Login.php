<?php

namespace App\Services\Filament;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use DominionSolutions\FilamentCaptcha\Forms\Components\Captcha;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseAuth
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
        return TextInput::make('username')
            ->label('Логин')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password'  => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $exception) {
            $this->handleLoginError($exception);
            return null;
        }
    }

    protected function handleLoginError(ValidationException $exception): void
    {
        Notification::make()
            ->title('Ошибка аутентификации')
            ->body('Неверный логин или пароль')
            ->danger()
            ->send();
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}