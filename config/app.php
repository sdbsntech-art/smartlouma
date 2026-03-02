<?php

return [
    'name'  => env('APP_NAME', 'SMART-LOUMA'),
    'env'   => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url'   => env('APP_URL', 'http://localhost:8000'),

    'timezone' => env('APP_TIMEZONE', 'Africa/Dakar'),
    'locale'   => env('APP_LOCALE', 'fr'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'fr'),
    'faker_locale'    => 'fr_FR',

    'cipher' => 'AES-256-CBC',
    'key'    => env('APP_KEY'),
    'previous_keys' => [],

    'maintenance' => ['driver' => 'file'],

    // ── Config spécifique SMART-LOUMA ──────────────────────────
    'admin_email'    => env('ADMIN_EMAIL', 'seydoubakhayokho1@gmail.com'),
    'admin_name'     => env('ADMIN_NAME', 'Seydou Bakhay Okho'),
    'admin_password' => env('ADMIN_PASSWORD', 'louma'),

    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Laravel\Sanctum\SanctumServiceProvider::class,
    ],

    'aliases' => Illuminate\Support\Facades\Facade::defaultAliases()->merge([
        'Str' => Illuminate\Support\Str::class,
    ])->toArray(),
];
