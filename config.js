import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const envPath = path.join(__dirname, '.env');
if (fs.existsSync(envPath)) {
  dotenv.config({ path: envPath, override: false });
}

export function famoEnv(key, defaultValue = '') {
  const value = process.env[key];
  return value === undefined || value === null || value === '' ? defaultValue : String(value);
}

export function famoAppMode() {
  return famoEnv('APP_MODE', famoEnv('APP_ENV', 'production')).trim().toLowerCase();
}

export function famoIsDev() {
  return famoAppMode() === 'development';
}

export function famoAsset(assetPath, fallback) {
  const defaultBase = famoIsDev() ? '' : 'https://assets.famoacademy.ir';
  const base = famoEnv('ASSET_URL', defaultBase).replace(/\/+$/, '');
  return base === '' ? fallback : `${base}/${String(assetPath).replace(/^\/+/, '')}`;
}

export function famoAssetBase() {
  return famoEnv('ASSET_URL', 'https://assets.famoacademy.ir').replace(/\/+$/, '');
}

export function famoApiUrl() {
  const base = famoEnv('API_URL', 'https://api.famoacademy.ir').replace(/\/+$/, '');
  return /\/api\/v1$/.test(base) ? base : `${base}/api/v1`;
}

export function famoInternalApiUrl() {
  const base = famoEnv('INTERNAL_API_URL', famoApiUrl()).replace(/\/+$/, '');
  return /\/api\/v1$/.test(base) ? base : `${base}/api/v1`;
}

export function famoPublicUrl() {
  return famoEnv('PUBLIC_URL', '').replace(/\/+$/, '');
}

export function famoAdminUrl() {
  return famoEnv('ADMIN_URL', '').replace(/\/+$/, '');
}

export function famoDashboardUrl() {
  return famoEnv('DASHBOARD_URL', '').replace(/\/+$/, '');
}

export function famoLoginUrl() {
  return famoEnv('LOGIN_URL', '').replace(/\/+$/, '');
}

function safeJsonForScript(obj) {
  return JSON.stringify(obj)
    .replace(/</g, '\\u003c')
    .replace(/>/g, '\\u003e')
    .replace(/&/g, '\\u0026')
    .replace(/'/g, '\\u0027');
}

export function famoConfigScript() {
  const config = {
    assetUrl: famoAssetBase(),
    apiUrl: famoApiUrl(),
  };

  const optionalUrls = {
    publicUrl: famoPublicUrl(),
    adminUrl: famoAdminUrl(),
    dashboardUrl: famoDashboardUrl(),
    loginUrl: famoLoginUrl(),
  };

  for (const [key, url] of Object.entries(optionalUrls)) {
    if (url !== '') {
      config[key] = url;
    }
  }

  const json = safeJsonForScript(config);
  return `<script>window.APP_CONFIG = Object.assign(window.APP_CONFIG || {}, ${json}); window.FAMO_ASSET = function (path) { var base = (window.APP_CONFIG && window.APP_CONFIG.assetUrl) || ""; return base.replace(/\\/$/, "") + "/" + String(path).replace(/^\\//, ""); };</script>`;
}
