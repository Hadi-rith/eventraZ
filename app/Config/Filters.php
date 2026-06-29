<?php

namespace Config;

use App\Filters\SecurityFilter;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Aliases for filters.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'security'      => SecurityFilter::class,   // ← our filter
    ];

    /**
     * Filters that always run on every request.
     */
    public array $globals = [
        'before' => [
            'security',                // rate-limit + auth + role guard
            // 'honeypot',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Filters applied to specific HTTP methods.
     */
    public array $methods = [];

    /**
     * Filters applied to specific routes.
     * Not needed — SecurityFilter covers all routes globally.
     */
    public array $filters = [];
}