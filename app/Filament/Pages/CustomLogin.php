<?php
namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class CustomLogin extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $this->validate();

        $user = User::where('phone', $this->form->getState()['phone'])->first();

        if (! $user || ! Hash::check($this->form->getState()['password'], $user->password)) {
            $this->addError('phone', __('filament-panels::pages/auth/login.messages.failed'));
            return null;
        }

        auth()->login($user, $this->form->getState()['remember']);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('phone')
                ->label('Phone')
                ->required()
                ->autofocus(),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(),

            Checkbox::make('remember')
                ->label(__('filament-panels::pages/auth/login.form.remember.label')),
        ];
    }
}
