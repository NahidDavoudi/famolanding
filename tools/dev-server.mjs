import fs from 'node:fs';
import http from 'node:http';
import https from 'node:https';
import net from 'node:net';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');

function loadEnv() {
  const envPath = path.join(ROOT_DIR, '.env');
  if (!fs.existsSync(envPath)) return;
  const lines = fs.readFileSync(envPath, 'utf8').split(/\r?\n/);
  for (const line of lines) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;
    const clean = trimmed.startsWith('export ') ? trimmed.slice(7).trim() : trimmed;
    const eq = clean.indexOf('=');
    if (eq === -1) continue;
    const key = clean.slice(0, eq).trim();
    let val = clean.slice(eq + 1).trim();
    if (
      val.length >= 2 &&
      ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'")))
    ) {
      val = val.slice(1, -1);
    }
    if (key && process.env[key] === undefined) {
      process.env[key] = val;
    }
  }
}

loadEnv();

const PORT = Number(process.env.PORT) || 3000;
const HOST = '0.0.0.0';

function famoEnv(key, fallback = '') {
  const val = process.env[key];
  return val === undefined || val === null || val === '' ? fallback : String(val);
}

function famoAssetBase() {
  return famoEnv('ASSET_URL', 'https://assets.famoacademy.ir').replace(/\/+$/, '');
}

function famoAsset(assetPath) {
  return `${famoAssetBase()}/${String(assetPath).replace(/^\/+/, '')}`;
}

function famoApiUrl() {
  const base = famoEnv('API_URL', 'https://api.famoacademy.ir').replace(/\/+$/, '');
  return /\/api\/v1$/.test(base) ? base : `${base}/api/v1`;
}

function famoInternalApiUrl() {
  const base = famoEnv('INTERNAL_API_URL', famoApiUrl()).replace(/\/+$/, '');
  return /\/api\/v1$/.test(base) ? base : `${base}/api/v1`;
}

function famoConfigScript() {
  const config = {
    assetUrl: famoAssetBase(),
    apiUrl: famoApiUrl(),
  };
  for (const [k, envKey] of [
    ['publicUrl', 'PUBLIC_URL'],
    ['adminUrl', 'ADMIN_URL'],
    ['dashboardUrl', 'DASHBOARD_URL'],
    ['loginUrl', 'LOGIN_URL'],
  ]) {
    const v = famoEnv(envKey, '').replace(/\/+$/, '');
    if (v) config[k] = v;
  }
  const json = JSON.stringify(config)
    .replace(/</g, '\\u003c')
    .replace(/>/g, '\\u003e')
    .replace(/&/g, '\\u0026')
    .replace(/'/g, '\\u0027');
  return `<script>window.APP_CONFIG = Object.assign(window.APP_CONFIG || {}, ${json}); window.FAMO_ASSET = function (path) { var base = (window.APP_CONFIG && window.APP_CONFIG.assetUrl) || ""; return base.replace(/\\/$/, "") + "/" + String(path).replace(/^\\//, ""); };</script>`;
}

const MIME_TYPES = {
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.mjs': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.ico': 'image/x-icon',
  '.xml': 'application/xml; charset=utf-8',
  '.txt': 'text/plain; charset=utf-8',
  '.html': 'text/html; charset=utf-8',
  '.woff2': 'font/woff2',
};

function renderPhpPreviewFile(relPath, base, vars = {}) {
  const fullPath = path.join(ROOT_DIR, relPath);
  let content = fs.readFileSync(fullPath, 'utf8');

  // Strip leading PHP preamble
  content = content.replace(/^<\?php[\s\S]*?\?>\s*/m, '');

  // Inline PHP includes from layouts/
  content = content.replace(/<\?php\s+include\s+__DIR__\s*\.\s*'([^']+)';\s*\?>/g, (_m, incRel) => {
    const incPath = path.resolve(path.dirname(fullPath), incRel.replace(/^\//, ''));
    if (!fs.existsSync(incPath)) return '';
    let inc = fs.readFileSync(incPath, 'utf8');
    inc = inc.replace(/^<\?php[\s\S]*?\?>\s*/m, '');
    return inc;
  });

  const loginUrl = famoEnv('LOGIN_URL', '').replace(/\/+$/, '') + '/';

  // Evaluate standard echo expressions used across the PHP pages for preview
  return content
    .replace(/<\?=\s*famo_config_script\(\)\s*\?>/g, famoConfigScript())
    .replace(/<\?=\s*htmlspecialchars\(\$pageTitle[^)]+\)\s*\?>/g, vars.pageTitle || 'آموزشگاه فامو')
    .replace(/<\?=\s*htmlspecialchars\(\$metaDescription[^)]+\)\s*\?>/g, vars.metaDescription || '')
    .replace(/<\?=\s*htmlspecialchars\(\$base,\s*ENT_QUOTES,\s*'UTF-8'\)\s*\?>/g, base)
    .replace(/<\?=\s*\$baseEsc\s*\?>/g, base)
    .replace(/<\?=\s*\$loginUrlEsc\s*\?>/g, loginUrl)
    .replace(/<\?=\s*htmlspecialchars\(famo_login_url\(\)\s*\.\s*'\/'[^)]+\)\s*\?>/g, loginUrl)
    .replace(/<\?=\s*json_encode\(famo_public_url\(\)[^)]+\)\s*\?>/g, JSON.stringify(famoEnv('PUBLIC_URL', '').replace(/\/+$/, '')))
    .replace(/<\?=\s*htmlspecialchars\(famo_asset\('([^']+)'[^)]+\),\s*ENT_QUOTES,\s*'UTF-8'\)\s*\?>/g, (_m, p) => famoAsset(p));
}

function escapeHtml(val) {
  return String(val ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function faDigits(str) {
  const map = { 0: '۰', 1: '۱', 2: '۲', 3: '۳', 4: '۴', 5: '۵', 6: '۶', 7: '۷', 8: '۸', 9: '۹' };
  return String(str ?? '').replace(/[0-9]/g, (d) => map[d]);
}

function gregorianToJalaliStr(ymd) {
  const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(ymd ?? ''));
  if (!m) return String(ymd ?? '');
  const gy = Number.parseInt(m[1], 10);
  const gm = Number.parseInt(m[2], 10);
  const gd = Number.parseInt(m[3], 10);
  if (gm < 1 || gm > 12 || gd < 1 || gd > 31) return String(ymd ?? '');
  const gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
  const gy2 = gm > 2 ? gy + 1 : gy;
  let days =
    355666 +
    365 * gy +
    Math.trunc((gy2 + 3) / 4) -
    Math.trunc((gy2 + 99) / 100) +
    Math.trunc((gy2 + 399) / 400) +
    gd +
    gdm[gm - 1];
  let jy = -1595 + 33 * Math.trunc(days / 12053);
  days %= 12053;
  jy += 4 * Math.trunc(days / 1461);
  days %= 1461;
  if (days > 365) {
    jy += Math.trunc((days - 1) / 365);
    days = (days - 1) % 365;
  }
  const jm = days < 186 ? 1 + Math.trunc(days / 31) : 7 + Math.trunc((days - 186) / 30);
  const jd = days < 186 ? 1 + (days % 31) : 1 + ((days - 186) % 30);
  return `${String(jy).padStart(4, '0')}/${String(jm).padStart(2, '0')}/${String(jd).padStart(2, '0')}`;
}

function fetchUpstream(apiUrl, clientIp) {
  return new Promise((resolve) => {
    let parsed;
    try {
      parsed = new URL(apiUrl);
    } catch {
      resolve({ status: 0, body: '', retryAfter: null, isRedirect: false });
      return;
    }
    const transport = parsed.protocol === 'https:' ? https : http;
    const headers = { Accept: 'application/json' };
    if (clientIp) headers['X-Forwarded-For'] = clientIp;

    let done = false;
    let connectTimer = null;
    let totalTimer = null;
    const finish = (out) => {
      if (done) return;
      done = true;
      if (connectTimer) clearTimeout(connectTimer);
      if (totalTimer) clearTimeout(totalTimer);
      resolve(out);
    };

    const req = transport.request(parsed, { method: 'GET', headers }, (upRes) => {
      const code = Number(upRes.statusCode || 0);
      if (code >= 300 && code < 400) {
        upRes.resume();
        finish({ status: code, body: '', retryAfter: null, isRedirect: true });
        return;
      }
      const chunks = [];
      upRes.on('data', (c) => chunks.push(c));
      upRes.on('end', () => {
        const ra = upRes.headers['retry-after'];
        finish({
          status: code,
          body: Buffer.concat(chunks).toString('utf8'),
          retryAfter: typeof ra === 'string' && /^\d+$/.test(ra.trim()) ? ra.trim() : null,
          isRedirect: false,
        });
      });
      upRes.on('error', () => finish({ status: 0, body: '', retryAfter: null, isRedirect: false }));
    });
    totalTimer = setTimeout(() => req.destroy(new Error('TIMEOUT')), 4000);
    req.on('socket', (sock) => {
      if (sock.connecting) {
        connectTimer = setTimeout(() => req.destroy(new Error('CONNECT_TIMEOUT')), 2000);
        sock.once('connect', () => {
          if (connectTimer) clearTimeout(connectTimer);
        });
      }
    });
    req.on('error', () => finish({ status: 0, body: '', retryAfter: null, isRedirect: false }));
    req.end();
  });
}

function renderExamPreviewHtml(state, data) {
  const rPhp = fs.readFileSync(path.join(ROOT_DIR, 'r.php'), 'utf8');
  const styleMatch = rPhp.match(/<style>[\s\S]*?<\/style>/);
  const inlineStyle = styleMatch ? styleMatch[0] : '';

  let bodyInner = '';
  if (state === 'not_found') {
    bodyInner = `<div class="exam-card exam-state-card"><h1 class="exam-state-title">لینک کارنامه در دسترس نیست</h1><p class="exam-state-desc">این لینک نامعتبر است، منقضی شده یا توسط پشتیبان غیرفعال گردیده است. در صورت نیاز، برای دریافت لینک جدید با واحد پشتیبانی آموزشگاه فامو تماس بگیرید.</p></div>`;
  } else if (state === 'rate_limited') {
    bodyInner = `<div class="exam-card exam-state-card"><h1 class="exam-state-title">تعداد درخواستها بیش از حد مجاز است</h1><p class="exam-state-desc">لطفاً حدود یک دقیقه صبر کرده و دوباره صفحه را بارگذاری کنید.</p></div>`;
  } else if (state === 'unavailable') {
    bodyInner = `<div class="exam-card exam-state-card"><h1 class="exam-state-title">سرویس موقتاً در دسترس نیست</h1><p class="exam-state-desc">دریافت اطلاعات کارنامه با اختلال موقت مواجه شد. لطفاً دقایقی دیگر دوباره تلاش کنید.</p></div>`;
  } else if (data) {
    const badges = [
      data.grade !== null ? `<span class="exam-badge">پایه ${escapeHtml(faDigits(data.grade))}</span>` : '',
      data.field ? `<span class="exam-badge">رشته ${escapeHtml(data.field)}</span>` : '',
      `<span class="exam-badge">تاریخ آزمون: ${escapeHtml(faDigits(gregorianToJalaliStr(data.exam_date)))}</span>`,
    ].join('');
    const cards = data.subjects
      .map(
        (s) =>
          `<div class="exam-card exam-subject-card"><div class="exam-subject-top"><div><div class="exam-subject-title">${escapeHtml(s.subject)}</div>${s.chapter ? `<div class="exam-subject-chapter">${escapeHtml(s.chapter)}</div>` : ''}</div><span class="exam-subject-pct" dir="ltr">${escapeHtml(faDigits(s.percentage.toFixed(1)))}%</span></div><div class="exam-stats-grid"><div class="exam-stat-cell exam-stat-cell--total"><div>کل</div><div class="exam-stat-num">${escapeHtml(faDigits(s.total_q))}</div></div><div class="exam-stat-cell exam-stat-cell--correct"><div>صحیح</div><div class="exam-stat-num">${escapeHtml(faDigits(s.correct))}</div></div><div class="exam-stat-cell exam-stat-cell--wrong"><div>غلط</div><div class="exam-stat-num">${escapeHtml(faDigits(s.wrong))}</div></div><div class="exam-stat-cell exam-stat-cell--skipped"><div>نزده</div><div class="exam-stat-num">${escapeHtml(faDigits(s.skipped))}</div></div></div></div>`,
      )
      .join('');
    const rows = data.subjects
      .map(
        (s) =>
          `<tr><td><div class="exam-col-subject">${escapeHtml(s.subject)}</div>${s.chapter ? `<div class="exam-subject-chapter">${escapeHtml(s.chapter)}</div>` : ''}</td><td class="exam-text-center">${escapeHtml(faDigits(s.total_q))}</td><td class="exam-text-center exam-col-correct">${escapeHtml(faDigits(s.correct))}</td><td class="exam-text-center exam-col-wrong">${escapeHtml(faDigits(s.wrong))}</td><td class="exam-text-center exam-col-skipped">${escapeHtml(faDigits(s.skipped))}</td><td class="exam-text-center exam-col-pct" dir="ltr">${escapeHtml(faDigits(s.percentage.toFixed(1)))}%</td></tr>`,
      )
      .join('');
    const commentsHtml =
      data.comments.length > 0
        ? `<section class="exam-card exam-comments"><h2 class="exam-comments-heading">تحلیل و توضیحات دبیر و پشتیبان</h2><div class="exam-comments-list">${data.comments
            .map(
              (c) =>
                `<article class="exam-comment-item"><div class="exam-comment-meta"><span class="exam-comment-author">${escapeHtml(c.author_name)}</span><span class="exam-comment-role">${escapeHtml(c.author_role)}</span></div><p class="exam-comment-body">${escapeHtml(c.body).replace(/\r\n|\r|\n/g, '<br>')}</p></article>`,
            )
            .join('')}</div></section>`
        : '';

    bodyInner = `<section class="exam-card exam-summary"><div class="exam-summary-inner"><div><h1 class="exam-student-name">${escapeHtml(data.student_name)}</h1><div class="exam-badges">${badges}</div></div><div class="exam-avg-box"><div class="exam-avg-label">میانگین درصد کل</div><div class="exam-avg-value" dir="ltr">${escapeHtml(faDigits(data.avg_percentage.toFixed(1)))}%</div></div></div></section><section class="exam-mobile-list">${cards}</section><section class="exam-card exam-table-wrap"><table class="exam-table"><thead><tr><th>درس / مبحث</th><th class="exam-text-center">تعداد سوال</th><th class="exam-text-center">صحیح</th><th class="exam-text-center">غلط</th><th class="exam-text-center">نزده</th><th class="exam-text-center">درصد</th></tr></thead><tbody>${rows}</tbody></table></section>${commentsHtml}`;
  }

  return `<!DOCTYPE html><html lang="fa" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="robots" content="noindex, nofollow"><meta name="referrer" content="no-referrer"><meta name="color-scheme" content="light"><title>کارنامه آزمون | آموزشگاه فامو</title><link rel="stylesheet" href="${escapeHtml(famoAsset('css/tokens.css'))}"><link rel="stylesheet" href="${escapeHtml(famoAsset('css/fonts.css'))}"><link rel="icon" type="image/png" sizes="32x32" href="${escapeHtml(famoAsset('images/favicon-32.png'))}">${inlineStyle}</head><body><header class="exam-header"><div class="exam-header-inner"><div class="exam-brand"><img src="${escapeHtml(famoAsset('images/logo.png'))}" alt="آموزشگاه فامو" width="40" height="40" class="exam-logo"><div><div class="exam-brand-title">آموزشگاه فامو</div><div class="exam-brand-sub">گزارش اختصاصی نتیجه آزمون</div></div></div>${data ? '<button type="button" onclick="window.print()" class="no-print exam-print-btn">چاپ کارنامه</button>' : ''}</div></header><main class="exam-main">${bodyInner}</main><footer class="exam-footer">آموزشگاه فامو — مشاوره تخصصی کنکور و تیزهوشان</footer></body></html>`;
}

const server = http.createServer(async (req, res) => {
  const reqUrl = new URL(req.url || '/', `http://${req.headers.host || '127.0.0.1'}`);
  const pathname = reqUrl.pathname;

  if (pathname.startsWith('/assets/') || pathname === '/robots.txt' || pathname === '/sitemap.xml') {
    const safeRel = path.normalize(pathname).replace(/^(\.\.(\/|\\|$))+/, '');
    const filePath = path.join(ROOT_DIR, safeRel);
    if (filePath.startsWith(ROOT_DIR) && fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      const ext = path.extname(filePath).toLowerCase();
      res.writeHead(200, { 'Content-Type': MIME_TYPES[ext] || 'application/octet-stream' });
      fs.createReadStream(filePath).pipe(res);
      return;
    }
    res.writeHead(404);
    res.end('Not Found');
    return;
  }

  if (pathname === '/' || pathname === '/index.php') {
    const html = renderPhpPreviewFile('index.php', '', {
      pageTitle: 'آموزشگاه فامو | مشاوره کنکور و تیزهوشان بابل',
      metaDescription: 'آموزشگاه فامو بابل - مشاوره تخصصی کنکور و تیزهوشان',
    });
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(html);
    return;
  }

  if (pathname === '/pages/course.php' || pathname === '/courses') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/course.php', '../'));
    return;
  }
  if (pathname === '/pages/team.php' || pathname === '/team') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/team.php', '../'));
    return;
  }
  if (pathname === '/pages/admissions.php' || pathname === '/admissions') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/admissions.php', '../'));
    return;
  }
  if (pathname === '/pages/testimonials.php' || pathname === '/testimonials') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/testimonials.php', '../'));
    return;
  }
  if (pathname === '/pages/blog.php' || pathname === '/blog') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/blog.php', '../'));
    return;
  }
  if (pathname === '/pages/post.php' || pathname === '/post') {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('pages/post.php', '../'));
    return;
  }
  if (pathname === '/sitemap-blog.php') {
    res.writeHead(200, { 'Content-Type': 'application/xml; charset=utf-8' });
    res.end('<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>\n');
    return;
  }

  if (pathname === '/r' || pathname.startsWith('/r/') || pathname === '/r.php') {
    res.setHeader('Content-Type', 'text/html; charset=UTF-8');
    res.setHeader('Referrer-Policy', 'no-referrer');
    res.setHeader('X-Robots-Tag', 'noindex, nofollow');
    res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    res.setHeader('X-Content-Type-Options', 'nosniff');

    const token =
      pathname === '/r.php'
        ? String(reqUrl.searchParams.get('token') || '').trim()
        : pathname.startsWith('/r/')
          ? pathname.slice(3).replace(/\/$/, '').trim()
          : '';

    if (!/^[A-Za-z0-9_-]{32,64}$/.test(token)) {
      res.writeHead(404);
      res.end(renderExamPreviewHtml('not_found', null));
      return;
    }

    let ip = String(req.socket?.remoteAddress || '').trim();
    if (ip.startsWith('::ffff:') && net.isIPv4(ip.slice(7))) ip = ip.slice(7);
    const up = await fetchUpstream(
      `${famoInternalApiUrl()}/public/exam-results/${encodeURIComponent(token)}`,
      net.isIP(ip) ? ip : '',
    );

    if (up.isRedirect || up.status === 0 || up.status >= 500) {
      res.setHeader('Retry-After', up.retryAfter || '30');
      res.writeHead(503);
      res.end(renderExamPreviewHtml('unavailable', null));
      return;
    }
    if (up.status === 404) {
      res.writeHead(404);
      res.end(renderExamPreviewHtml('not_found', null));
      return;
    }
    if (up.status === 429) {
      res.setHeader('Retry-After', up.retryAfter || '60');
      res.writeHead(429);
      res.end(renderExamPreviewHtml('rate_limited', null));
      return;
    }
    if (up.status !== 200) {
      res.setHeader('Retry-After', '30');
      res.writeHead(503);
      res.end(renderExamPreviewHtml('unavailable', null));
      return;
    }

    try {
      const parsed = JSON.parse(up.body);
      if (!parsed?.success || !parsed?.data) throw new Error('BAD_JSON');
      const d = parsed.data;
      const st = d.student && typeof d.student === 'object' ? d.student : {};
      const roleLabels = { teacher: 'دبیر', supporter: 'پشتیبان', admin: 'مدیریت آموزشی' };
      const result = {
        student_name: String(d.student_name ?? st.name ?? 'دانش‌آموز'),
        grade: st.grade !== undefined && st.grade !== null && st.grade !== '' ? Number.parseInt(st.grade, 10) : null,
        field: st.field ? String(st.field) : null,
        exam_date: String(d.exam_date ?? ''),
        avg_percentage: Number(d.avg_percentage ?? 0) || 0,
        subjects: Array.isArray(d.subjects)
          ? d.subjects.map((s) => ({
              subject: String(s.subject ?? ''),
              chapter: s.chapter ? String(s.chapter) : null,
              total_q: Number.parseInt(s.total_q ?? 0, 10) || 0,
              correct: Number.parseInt(s.correct ?? 0, 10) || 0,
              wrong: Number.parseInt(s.wrong ?? 0, 10) || 0,
              skipped: Number.parseInt(s.skipped ?? 0, 10) || 0,
              percentage: Number(s.percentage ?? 0) || 0,
            }))
          : [],
        comments: Array.isArray(d.comments)
          ? d.comments
              .filter((c) => c && String(c.body ?? '').trim() !== '')
              .map((c) => ({
                author_name: String(c.author_name ?? ''),
                author_role: roleLabels[String(c.author_role ?? '')] || 'کادر آموزشی',
                body: String(c.body ?? ''),
              }))
          : [],
      };
      res.writeHead(200);
      res.end(renderExamPreviewHtml(null, result));
    } catch {
      res.setHeader('Retry-After', '30');
      res.writeHead(503);
      res.end(renderExamPreviewHtml('unavailable', null));
    }
    return;
  }

  res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
  res.end('Not Found');
});

server.listen(PORT, HOST, () => {
  console.log(`Preview harness listening on http://${HOST}:${PORT} (Node sandbox preview; PHP is served by Apache in Docker)`);
});
