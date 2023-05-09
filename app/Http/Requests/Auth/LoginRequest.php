<?php

namespace App\Http\Requests\Auth;

use App\Models\{CompromisedUser, LoginHistory, User};
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\{Auth, RateLimiter, Session};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::firstWhere('email', $this->get('email'));

        if (!Auth::attempt($this->only(['email', 'password']), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user) {
                $this->createLoginHistoryRecord($user->id, true);
                $this->increaseUserSuspicionLevel($user->id);
            }

            throw ValidationException::withMessages([
                // TODO: translations
                'email' => trans('auth.failed'),
            ]);
        }

        // Check if user suspended
        if (config('enums.user.statuses')[$user->status] === 'suspended') {
            throw ValidationException::withMessages([
                'email' => trans('login.suspendedErr')
            ]);
        }

        // Provide authorization
        if (!$token = auth()->attempt($this->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => trans('login.fatalErr')
            ]);
        }

        $this->createLoginHistoryRecord($user->id);

        // Get sign-ins by IP
        $ips = LoginHistory::select('ip')->where('user_id', $user->id)->groupBy('ip')->get()->count();

        // Get sign-ins by user agent
        $devices = LoginHistory::select('user_agent')
            ->where('user_id', $user->id)
            ->groupBy('user_agent')
            ->get()
            ->count();

        // Check the authorization was compromised
        if (($ips > 2 || $devices > 2) && !CompromisedUser::where('user_id', $user->id)->count()) {
            $this->increaseUserSuspicionLevel($user->id);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }

    /**
     * Create Login history entity
     *
     * @param int $id
     * @param bool $fail
     * @return LoginHistory
     */
    protected function createLoginHistoryRecord(int $id, bool $fail = false): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $id,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'failed' => $fail
        ]);
    }

    /**
     * Increase the level of suspicion of the user
     *
     * @param int $id
     * @return void
     */
    protected function increaseUserSuspicionLevel(int $id): void
    {
        $compromised = CompromisedUser::firstWhere(['user_id' => $id]);
        if ($compromised) {
            $compromised->when($compromised->attempt < 65535, fn($q) => $q->increment('attempt'));
        } else {
            CompromisedUser::create(['user_id' => $id, 'attempt' => 1]);
        }
    }
}
