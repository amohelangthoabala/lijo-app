<?php
 
namespace App\Http\Livewire\Auth;
 
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use Filament\Http\Livewire\Auth\Login as OldLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
 
class Login extends OldLogin
{
    public $phone_number = '';
 
 
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('filament::login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }
 
        $data = $this->form->getState();
 
        if (! Filament::auth()->attempt([
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'phone_number' => __('filament::login.messages.failed'),
            ]);
        }
 
        session()->regenerate();
 
        return app(LoginResponse::class);
    }
 
 
 
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('phone_number')
                ->label(__('Phone number'))
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label(__('filament::login.fields.password.label'))
                ->password()
                ->required(),
            Checkbox::make('remember')
                ->label(__('filament::login.fields.remember.label')),
        ];
    }
 
}