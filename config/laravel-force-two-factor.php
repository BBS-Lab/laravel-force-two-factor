<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enforce two-factor authentication
    |--------------------------------------------------------------------------
    |
    | Master switch shared by every adapter (nova-force-two-factor,
    | filament-force-two-factor). When disabled, no adapter forces 2FA
    | enrolment. Set FORCE_TWO_FACTOR_ENABLED=false to turn enforcement off,
    | e.g. in local development.
    |
    */

    'enabled' => (bool) env('FORCE_TWO_FACTOR_ENABLED', true),

];
