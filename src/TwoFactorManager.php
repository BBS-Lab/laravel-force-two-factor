<?php

declare(strict_types=1);

namespace BBSLab\LaravelForceTwoFactor;

use BBSLab\LaravelForceTwoFactor\Facades\ForceTwoFactor;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * Framework-agnostic registry for forced two-factor authentication, resolved as
 * a container singleton and reached through the {@see ForceTwoFactor} facade.
 * Bypass callbacks live here (never in the config file) so they survive
 * `config:cache`, which cannot serialise a Closure. Both adapters
 * (nova-force-two-factor, filament-force-two-factor) read this same registry, so
 * a single registered reason to skip 2FA applies to whichever panel enforces it.
 */
class TwoFactorManager
{
    /**
     * Callbacks that exempt a user from forced 2FA enrolment. The request is
     * bypassed as soon as any of them returns true, so several independent
     * reasons to skip enrolment compose without clobbering.
     *
     * @var array<int, Closure(Request, Authenticatable): bool>
     */
    protected array $bypassCallbacks = [];

    /**
     * Register a callback that exempts a request/user from forced 2FA enrolment —
     * for instance an SSO user whose second factor is handled by the identity
     * provider, or a user who still owes a forced password rotation. The callback
     * receives the current request and the authenticated, not-yet-enrolled user,
     * and returns true to skip the enrolment redirect.
     *
     * @param  Closure(Request, Authenticatable): bool  $callback
     */
    public function bypass(Closure $callback): void
    {
        $this->bypassCallbacks[] = $callback;
    }

    /**
     * Whether any registered callback exempts this request/user from enrolment.
     */
    public function shouldBypass(Request $request, Authenticatable $user): bool
    {
        foreach ($this->bypassCallbacks as $callback) {
            if ($callback($request, $user)) {
                return true;
            }
        }

        return false;
    }
}
