<?php

declare(strict_types=1);

namespace BBSLab\LaravelForceTwoFactor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelForceTwoFactorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        // spatie derives its "short name" by stripping the "laravel-" prefix,
        // which would register the config under "force-two-factor". The public
        // contract is the full "laravel-force-two-factor" name the adapters read
        // from, so pin the config file name explicitly.
        $package
            ->name('laravel-force-two-factor')
            ->hasConfigFile('laravel-force-two-factor');
    }

    public function packageRegistered(): void
    {
        // Shared registry singleton: the facade and every adapter middleware talk
        // to the same instance, so bypass callbacks registered in a host — or in a
        // sibling package's boot() (laravel-okta, laravel-password-rotation) —
        // reach whichever panel enforces 2FA.
        $this->app->singleton(TwoFactorManager::class);
    }
}
