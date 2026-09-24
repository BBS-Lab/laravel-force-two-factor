# Changelog

All notable changes to `bbs-lab/laravel-force-two-factor` will be documented in this file.

## v1.0.0 - 2026-09-24

Admin-panel-agnostic core for forced two-factor authentication, extracted from `bbs-lab/nova-force-two-factor` so Nova and Filament share one bypass registry.

### ✨ Features

- `TwoFactorManager`: a container-singleton registry of bypass callbacks (`fn(Request, Authenticatable): bool`). `shouldBypass()` returns true as soon as any callback does, so several independent reasons to skip forced 2FA compose without clobbering.
- `ForceTwoFactor` facade (`bypass()`, `shouldBypass()`) — the single API both adapters read from.
- `enabled` master switch (`config/laravel-force-two-factor.php`, env `FORCE_TWO_FACTOR_ENABLED`) shared by every adapter.

### 📦 Requirements

- PHP 8.2+, Laravel 11/12/13.
