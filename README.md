# Force Two-Factor (core)

[![Tests](https://github.com/BBS-Lab/laravel-force-two-factor/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/BBS-Lab/laravel-force-two-factor/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/BBS-Lab/laravel-force-two-factor/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/BBS-Lab/laravel-force-two-factor/actions/workflows/phpstan.yml)

Admin-panel-agnostic core for **forcing two-factor authentication** in any Laravel app. It ships the shared **bypass registry** that lets several independent reasons to skip forced 2FA compose cleanly, and that the adapters enforce:

- [`bbs-lab/nova-force-two-factor`](https://github.com/BBS-Lab/nova-force-two-factor) — forces Nova's Fortify 2FA.
- [`bbs-lab/filament-force-two-factor`](https://github.com/BBS-Lab/filament-force-two-factor) — plugs into Filament's native multi-factor gate.

You usually install an **adapter**, which pulls this package in automatically. Install it directly only when you build your own enforcement middleware.

```bash
composer require bbs-lab/laravel-force-two-factor
```

## Why a shared registry?

A panel can only wire **one** "force 2FA" gate, but several packages have a legitimate reason to let a user skip it — e.g. SSO users (their MFA is handled by the identity provider) and users who still owe a forced password rotation (they must change their password first). Each reason registers a callback here; the gate bypasses as soon as **any** returns `true`. Nova and Filament read the same registry, so a reason registered once applies to whichever panel enforces 2FA.

## Registering a bypass

Callbacks live in code (never in the config file — a `Closure` cannot be `config:cache`d), so register them in a service provider's `boot()`:

```php
use BBSLab\LaravelForceTwoFactor\Facades\ForceTwoFactor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

ForceTwoFactor::bypass(function (Request $request, Authenticatable $user): bool {
    return $request->hasSession()
        && $request->session()->get('okta_authenticated') === true;
});
```

The sibling packages register their own bypass automatically when this package is present:

- **`bbs-lab/laravel-okta`** — skips forced 2FA for users authenticated via Okta (`okta_authenticated`).
- **`bbs-lab/laravel-password-rotation`** — skips forced 2FA while a user still owes a forced password rotation, so the rotation happens first.

## Configuration

```php
// config/laravel-force-two-factor.php
return [
    'enabled' => (bool) env('FORCE_TWO_FACTOR_ENABLED', true),
];
```

`enabled` is the master switch shared by every adapter. Publish it with:

```bash
php artisan vendor:publish --tag=laravel-force-two-factor-config
```

## Testing

```bash
composer test
```

## License

MIT. See [LICENSE.md](LICENSE.md).
