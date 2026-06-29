<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SecurityFilter
 *
 * Applied globally in Filters.php. Handles per-request:
 *   1. Rate limiting   — sliding-window counter in CI cache, per IP
 *   2. Authentication  — session logged_in check
 *   3. Role guard      — role must match the route prefix
 */
class SecurityFilter implements FilterInterface
{
    // ── Rate-limit tunables ───────────────────────────────────────────
    private const LOGIN_MAX   = 10;   // attempts per window on auth endpoints
    private const API_MAX     = 120;  // attempts per window on all other routes
    private const WINDOW_SECS = 60;   // sliding window length in seconds

    // ── Auth endpoints: rate-limited but NOT session-guarded ──────────
    private const AUTH_ROUTES = [
        'login/proses',
        'signup/school',
        'signup/awam',
        'login/sekolah',
        'login/awam',
    ];

    // ── Completely public (no rate-limit key needed) ──────────────────
    private const PUBLIC_ROUTES = [
        '',        // home /
        'login',
        'logout',
    ];

    // ── Which session roles may access which URL prefixes ─────────────
    private const ROLE_PREFIX = [
        'super_admin' => ['admin'],
        'admin'       => ['admin'],
        'school'      => ['school'],
        'public'      => ['public'],
    ];

    // ── Role → their home page (for wrong-role redirects) ─────────────
    private const ROLE_HOME = [
        'super_admin' => '/admin/dashboard',
        'admin'       => '/admin/dashboard',
        'school'      => '/school/portal',
        'public'      => '/public/portal',
    ];

    // ─────────────────────────────────────────────────────────────────

    public function before(RequestInterface $request, $arguments = null)
    {
        $uri   = strtolower(trim($request->getUri()->getPath(), '/'));
        $ip    = $request->getIPAddress();
        $cache = \Config\Services::cache();

        $isAuthRoute   = $this->matchesAny($uri, self::AUTH_ROUTES);
        $isPublicRoute = in_array($uri, self::PUBLIC_ROUTES, true);

        // ── 1. Rate limiting (skip completely public pages) ───────────
        if (!$isPublicRoute) {
            $bucketKey = 'rl_' . ($isAuthRoute ? 'auth_' : 'api_') . md5($ip);
            $maxHits   = $isAuthRoute ? self::LOGIN_MAX : self::API_MAX;
            $hits      = (int) ($cache->get($bucketKey) ?? 0);

            if ($hits >= $maxHits) {
                return $this->tooManyRequests($request);
            }
            $cache->save($bucketKey, $hits + 1, self::WINDOW_SECS);
        }

        // ── 2 & 3. Auth + role guard (protected prefixes only) ────────
        $segment = explode('/', $uri)[0] ?? '';
        $protectedPrefixes = array_keys(
            array_merge(...array_values(
                array_map(fn($v) => array_fill_keys($v, true), self::ROLE_PREFIX)
            ))
        );
        // Simpler: hard-coded protected prefixes
        $protectedPrefixes = ['admin', 'school', 'public'];

        if (in_array($segment, $protectedPrefixes, true)) {
            $session = service('session');

            if (!$session->get('logged_in')) {
                return $this->unauthenticated($request);
            }

            $role           = (string) ($session->get('role') ?? '');
            $allowedPrefixes = self::ROLE_PREFIX[$role] ?? [];

            if (!in_array($segment, $allowedPrefixes, true)) {
                return $this->forbidden($request, $session->get('role'));
            }
        }

        return null; // all checks passed — proceed to controller
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function matchesAny(string $uri, array $prefixes): bool
    {
        foreach ($prefixes as $p) {
            if (str_starts_with($uri, strtolower($p))) {
                return true;
            }
        }
        return false;
    }

    private function wantsJson(RequestInterface $request): bool
    {
        return strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest'
            || str_contains($request->getHeaderLine('Accept'), 'application/json');
    }

    private function tooManyRequests(RequestInterface $request)
    {
        $response = service('response')->setStatusCode(429);
        if ($this->wantsJson($request)) {
            return $response->setJSON([
                'success' => false,
                'message' => 'Terlalu banyak percubaan. Sila cuba sebentar lagi.',
            ]);
        }
        return $response->setBody(
            '<h2 style="font-family:sans-serif;text-align:center;margin-top:10vh">'
            . '429 — Terlalu Banyak Permintaan<br>'
            . '<small>Sila tunggu sebentar sebelum mencuba semula.</small></h2>'
        );
    }

    private function unauthenticated(RequestInterface $request)
    {
        $response = service('response')->setStatusCode(401);
        if ($this->wantsJson($request)) {
            return $response->setJSON([
                'success' => false,
                'message' => 'Sesi tamat. Sila log masuk semula.',
            ]);
        }
        return redirect()->to('/login');
    }

    private function forbidden(RequestInterface $request, ?string $role)
    {
        $response = service('response')->setStatusCode(403);
        if ($this->wantsJson($request)) {
            return $response->setJSON([
                'success' => false,
                'message' => 'Akses tidak dibenarkan untuk peranan ini.',
            ]);
        }
        $home = self::ROLE_HOME[$role] ?? '/login';
        return redirect()->to($home);
    }
}