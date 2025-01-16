<?php

namespace App\Services\Filament;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getTelegramFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }
    protected function getTelegramFormComponent(): Component
    {
        return TextInput::make('tg_username')
            ->label('Telegram username')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Логин')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }
    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));
        if(request()->cookie('ref') !== null) {
            $user->referral_user = request()->cookie('ref');
            $user->save();
        }

        redirect('/user/login');
        return app(RegistrationResponse::class);
    }


}
