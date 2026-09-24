<?php

declare(strict_types=1);

use BBSLab\LaravelForceTwoFactor\Facades\ForceTwoFactor;
use BBSLab\LaravelForceTwoFactor\TwoFactorManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

function fakeUser(int $id = 1): Authenticatable
{
    return new class($id) implements Authenticatable
    {
        public function __construct(protected int $id) {}

        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return $this->id;
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getRememberToken(): string
        {
            return '';
        }

        public function setRememberToken($value): void {}

        public function getRememberTokenName(): string
        {
            return '';
        }
    };
}

it('does not bypass when no callback is registered', function () {
    expect(ForceTwoFactor::shouldBypass(Request::create('/'), fakeUser()))->toBeFalse();
});

it('bypasses as soon as a registered callback returns true', function () {
    ForceTwoFactor::bypass(fn (Request $r, Authenticatable $u): bool => true);

    expect(ForceTwoFactor::shouldBypass(Request::create('/'), fakeUser()))->toBeTrue();
});

it('does not bypass when the only callback returns false', function () {
    ForceTwoFactor::bypass(fn (Request $r, Authenticatable $u): bool => false);

    expect(ForceTwoFactor::shouldBypass(Request::create('/'), fakeUser()))->toBeFalse();
});

it('composes several callbacks — any true wins', function () {
    ForceTwoFactor::bypass(fn (Request $r, Authenticatable $u): bool => false);
    ForceTwoFactor::bypass(fn (Request $r, Authenticatable $u): bool => true);

    expect(ForceTwoFactor::shouldBypass(Request::create('/'), fakeUser()))->toBeTrue();
});

it('passes the current request and user to the callback', function () {
    $seen = null;
    ForceTwoFactor::bypass(function (Request $r, Authenticatable $u) use (&$seen): bool {
        $seen = [$r->path(), $u->getAuthIdentifier()];

        return false;
    });

    ForceTwoFactor::shouldBypass(Request::create('/dashboard'), fakeUser(42));

    expect($seen)->toBe(['dashboard', 42]);
});

it('resolves the manager as a shared singleton', function () {
    expect(app(TwoFactorManager::class))->toBe(app(TwoFactorManager::class));
});
