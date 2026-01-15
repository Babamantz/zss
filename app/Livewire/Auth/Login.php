<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // FIXED: Attempt login without tenant scope
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Credentials don\'t match our records',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // ADDED: Set tenant context after successful login
        $user = Auth::user();

        if ($user->tenant) {
            session(['tenant_name' => $user->tenant->name,'module'=>"general"]);
            app()->instance('tenant', $user->tenant);
            app()->instance('tenant.id', $user->tenant_id);

            Log::info('Tenant set after login', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant->name
            ]);
        } else {
            // User has no tenant - handle this case
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is not associated with any organization.',
            ]);
        }

        $this->redirectIntended(default: route('index', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }


    #[Layout('components.layouts.master-auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
