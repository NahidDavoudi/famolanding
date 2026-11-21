<?php
declare(strict_types=1);

function famo_load_env(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envPath = __DIR__ . '/.env';
    if (!is_file($envPath) || !is_readable($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }
        if (str_starts_with($trimmed, 'export ')) {
            $trimmed = ltrim(substr($trimmed, 7));
        }
        $eqPos = strpos($trimmed, '=');
        if ($eqPos === false) {
            continue;
        }
        $key = trim(substr($trimmed, 0, $eqPos));
        $val = trim(substr($trimmed, $eqPos + 1));
        if ($key === '') {
            continue;
        }
        if (strlen($val) >= 2) {
            $first = $val[0];
            $last = $val[strlen($val) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $val = substr($val, 1, -1);
            }
        }
        $existing = getenv($key);
        if ($existing === false && !array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
            putenv($key . '=' . $val);
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

famo_load_env();

function famo_env(string $key, string $default = ''): string
{
    $val = getenv($key);
    if ($val === false) {
        $val = $_ENV[$key] ?? ($_SERVER[$key] ?? '');
    }
    $str = (string) $val;
    return $str === '' ? $default : $str;
}

function famo_app_mode(): string
{
    return strtolower(trim(famo_env('APP_MODE', famo_env('APP_ENV', 'production'))));
}

function famo_is_dev(): bool
{
    return famo_app_mode() === 'development';
}

function famo_asset_version(): string
{
    return trim(famo_env('ASSET_VERSION', ''));
}

function famo_append_version(string $url, string $version): string
{
    if ($version === '') {
        return $url;
    }
    $sep = str_contains($url, '?') ? '&' : '?';
    return $url . $sep . 'v=' . rawurlencode($version);
}

function famo_asset(string $assetPath, string $fallback): string
{
    $defaultBase = famo_is_dev() ? '' : 'https://assets.famoacademy.ir';
    $base = rtrim(famo_env('ASSET_URL', $defaultBase), '/');
    $url = $base === '' ? $fallback : $base . '/' . ltrim($assetPath, '/');
    return famo_append_version($url, famo_asset_version());
}

function famo_local_asset(string $relativePath, string $base = ''): string
{
    $cleanPath = ltrim($relativePath, '/');
    $url = $base . $cleanPath;
    $fsPath = __DIR__ . '/' . $cleanPath;
    if (is_file($fsPath)) {
        $mtime = filemtime($fsPath);
        if ($mtime !== false) {
            return famo_append_version($url, (string) $mtime);
        }
    }
    return $url;
}

function famo_asset_base(): string
{
    return rtrim(famo_env('ASSET_URL', 'https://assets.famoacademy.ir'), '/');
}

function famo_api_url(): string
{
    $base = rtrim(famo_env('API_URL', 'https://api.famoacademy.ir'), '/');
    return preg_match('#/api/v1$#', $base) === 1 ? $base : $base . '/api/v1';
}

function famo_internal_api_url(): string
{
    $base = rtrim(famo_env('INTERNAL_API_URL', famo_api_url()), '/');
    return preg_match('#/api/v1$#', $base) === 1 ? $base : $base . '/api/v1';
}

function famo_public_url(): string
{
    return rtrim(famo_env('PUBLIC_URL', ''), '/');
}

function famo_admin_url(): string
{
    return rtrim(famo_env('ADMIN_URL', ''), '/');
}

function famo_dashboard_url(): string
{
    return rtrim(famo_env('DASHBOARD_URL', ''), '/');
}

function famo_login_url(): string
{
    return rtrim(famo_env('LOGIN_URL', ''), '/');
}

function famo_config_script(): string
{
    $config = [
        'assetUrl' => famo_asset_base(),
        'apiUrl'   => famo_api_url(),
    ];

    $optionalUrls = [
        'publicUrl'    => famo_public_url(),
        'adminUrl'     => famo_admin_url(),
        'dashboardUrl' => famo_dashboard_url(),
        'loginUrl'     => famo_login_url(),
    ];

    foreach ($optionalUrls as $key => $url) {
        if ($url !== '') {
            $config[$key] = $url;
        }
    }

    $json = json_encode(
        $config,
        JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    if ($json === false) {
        $json = '{}';
    }

    return '<script>window.APP_CONFIG = Object.assign(window.APP_CONFIG || {}, '
        . $json
        . '); window.FAMO_ASSET = function (path) { var base = (window.APP_CONFIG && window.APP_CONFIG.assetUrl) || ""; return base.replace(/\/$/, "") + "/" + String(path).replace(/^\//, ""); };</script>';
}
