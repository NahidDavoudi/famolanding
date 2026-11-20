import http from 'node:http';
import https from 'node:https';
import net from 'node:net';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import express from 'express';
import {
  famoAsset,
  famoAssetBase,
  famoApiUrl,
  famoConfigScript,
  famoInternalApiUrl,
  famoLoginUrl,
  famoPublicUrl,
} from './config.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = Number(process.env.PORT) || 3000;
const HOST = '0.0.0.0';

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.locals.famoAsset = famoAsset;
app.locals.famoAssetBase = famoAssetBase;
app.locals.famoApiUrl = famoApiUrl;
app.locals.famoConfigScript = famoConfigScript;
app.locals.famoLoginUrl = famoLoginUrl;
app.locals.famoPublicUrl = famoPublicUrl;

const FA_DIGITS_MAP = {
  0: '۰',
  1: '۱',
  2: '۲',
  3: '۳',
  4: '۴',
  5: '۵',
  6: '۶',
  7: '۷',
  8: '۸',
  9: '۹',
};

function faDigits(text) {
  return String(text ?? '').replace(/[0-9]/g, (d) => FA_DIGITS_MAP[d]);
}

function gregorianToJalaliStr(ymd) {
  const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(ymd ?? ''));
  if (!match) {
    return String(ymd ?? '');
  }
  const gy = Number.parseInt(match[1], 10);
  const gm = Number.parseInt(match[2], 10);
  const gd = Number.parseInt(match[3], 10);
  if (gm < 1 || gm > 12 || gd < 1 || gd > 31) {
    return String(ymd ?? '');
  }
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
  let jm;
  let jd;
  if (days < 186) {
    jm = 1 + Math.trunc(days / 31);
    jd = 1 + (days % 31);
  } else {
    jm = 7 + Math.trunc((days - 186) / 30);
    jd = 1 + ((days - 186) % 30);
  }
  return `${String(jy).padStart(4, '0')}/${String(jm).padStart(2, '0')}/${String(jd).padStart(2, '0')}`;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function nl2brEscaped(value) {
  return escapeHtml(value).replace(/\r\n|\r|\n/g, '<br>');
}

function extractClientIp(req) {
  let remote = String(req.socket?.remoteAddress || '').trim();
  if (remote.startsWith('::ffff:')) {
    const mappedV4 = remote.slice(7);
    if (net.isIPv4(mappedV4)) {
      remote = mappedV4;
    }
  }
  return net.isIP(remote) !== 0 ? remote : '';
}

function setExamSecurityHeaders(res) {
  res.setHeader('Content-Type', 'text/html; charset=UTF-8');
  res.setHeader('Referrer-Policy', 'no-referrer');
  res.setHeader('X-Robots-Tag', 'noindex, nofollow');
  res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
  res.setHeader('X-Content-Type-Options', 'nosniff');
}

function fetchExamResult(apiUrl, clientIp) {
  return new Promise((resolve) => {
    let parsedUrl;
    try {
      parsedUrl = new URL(apiUrl);
    } catch {
      console.error('[exam-result] invalid internal API URL configuration');
      resolve({ httpStatus: 0, body: '', retryAfter: null, isRedirect: false });
      return;
    }

    const transport = parsedUrl.protocol === 'https:' ? https : http;
    const headers = { Accept: 'application/json' };
    if (clientIp !== '') {
      headers['X-Forwarded-For'] = clientIp;
    }

    let settled = false;
    let connectTimer = null;
    let totalTimer = null;

    const cleanup = () => {
      if (connectTimer !== null) {
        clearTimeout(connectTimer);
        connectTimer = null;
      }
      if (totalTimer !== null) {
        clearTimeout(totalTimer);
        totalTimer = null;
      }
    };

    const finish = (outcome) => {
      if (settled) {
        return;
      }
      settled = true;
      cleanup();
      resolve(outcome);
    };

    const req = transport.request(
      parsedUrl,
      {
        method: 'GET',
        headers,
      },
      (upstreamRes) => {
        const statusCode = Number(upstreamRes.statusCode || 0);
        if (statusCode >= 300 && statusCode < 400) {
          upstreamRes.resume();
          console.error(`[exam-result] upstream redirect blocked status=${statusCode}`);
          finish({ httpStatus: statusCode, body: '', retryAfter: null, isRedirect: true });
          return;
        }

        const chunks = [];
        let totalBytes = 0;
        const maxBytes = 1024 * 1024; // 1 MB defensive cap

        upstreamRes.on('data', (chunk) => {
          totalBytes += chunk.length;
          if (totalBytes > maxBytes) {
            req.destroy(new Error('RESPONSE_TOO_LARGE'));
            return;
          }
          chunks.push(chunk);
        });

        upstreamRes.on('end', () => {
          const retryRaw = upstreamRes.headers['retry-after'];
          const retryAfter =
            typeof retryRaw === 'string' && /^\d+$/.test(retryRaw.trim()) && Number(retryRaw.trim()) > 0
              ? retryRaw.trim()
              : null;
          finish({
            httpStatus: statusCode,
            body: Buffer.concat(chunks).toString('utf8'),
            retryAfter,
            isRedirect: false,
          });
        });

        upstreamRes.on('error', () => {
          finish({ httpStatus: 0, body: '', retryAfter: null, isRedirect: false });
        });
      },
    );

    totalTimer = setTimeout(() => {
      req.destroy(new Error('TOTAL_TIMEOUT'));
    }, 4000);

    req.on('socket', (socket) => {
      if (socket.connecting) {
        connectTimer = setTimeout(() => {
          req.destroy(new Error('CONNECT_TIMEOUT'));
        }, 2000);
        socket.once('connect', () => {
          if (connectTimer !== null) {
            clearTimeout(connectTimer);
            connectTimer = null;
          }
        });
      }
    });

    req.on('error', (err) => {
      console.error(`[exam-result] upstream request error code=${err?.message || 'NETWORK_ERROR'}`);
      finish({ httpStatus: 0, body: '', retryAfter: null, isRedirect: false });
    });

    req.end();
  });
}

function renderExamResultPage(res, statusCode, errorState, result) {
  setExamSecurityHeaders(res);
  res.status(statusCode).render('r', {
    errorState,
    result,
    faDigits,
    gregorianToJalaliStr,
    nl2brEscaped,
  });
}

function parseWhitelistedExamResult(rawBody) {
  let decoded;
  try {
    decoded = JSON.parse(rawBody);
  } catch {
    return null;
  }

  if (!decoded || typeof decoded !== 'object' || !decoded.success || !decoded.data || typeof decoded.data !== 'object') {
    return null;
  }

  const raw = decoded.data;
  const studentRaw = raw.student && typeof raw.student === 'object' ? raw.student : {};
  const subjectsRaw = Array.isArray(raw.subjects) ? raw.subjects : [];
  const commentsRaw = Array.isArray(raw.comments) ? raw.comments : [];

  const subjects = [];
  for (const row of subjectsRaw) {
    if (!row || typeof row !== 'object') {
      continue;
    }
    const pctNum = Number(row.percentage ?? 0);
    subjects.push({
      subject: String(row.subject ?? ''),
      chapter: row.chapter !== undefined && row.chapter !== null && String(row.chapter) !== '' ? String(row.chapter) : null,
      total_q: Number.parseInt(row.total_q ?? 0, 10) || 0,
      correct: Number.parseInt(row.correct ?? 0, 10) || 0,
      wrong: Number.parseInt(row.wrong ?? 0, 10) || 0,
      skipped: Number.parseInt(row.skipped ?? 0, 10) || 0,
      percentage: Number.isFinite(pctNum) ? pctNum : 0,
    });
  }

  const roleLabels = {
    teacher: 'دبیر',
    supporter: 'پشتیبان',
    admin: 'مدیریت آموزشی',
  };
  const comments = [];
  for (const c of commentsRaw) {
    if (!c || typeof c !== 'object') {
      continue;
    }
    const body = String(c.body ?? '');
    if (body.trim() === '') {
      continue;
    }
    const roleKey = String(c.author_role ?? '');
    comments.push({
      author_name: String(c.author_name ?? ''),
      author_role: Object.prototype.hasOwnProperty.call(roleLabels, roleKey) ? roleLabels[roleKey] : 'کادر آموزشی',
      body,
    });
  }

  const gradeVal = studentRaw.grade;
  const gradeInt = gradeVal !== undefined && gradeVal !== null && String(gradeVal) !== '' ? Number.parseInt(gradeVal, 10) : null;
  const fieldVal = studentRaw.field;
  const fieldStr = fieldVal !== undefined && fieldVal !== null && String(fieldVal) !== '' ? String(fieldVal) : null;
  const avgNum = Number(raw.avg_percentage ?? 0);

  return {
    student_name: String(raw.student_name ?? studentRaw.name ?? 'دانش‌آموز'),
    grade: gradeInt !== null && Number.isFinite(gradeInt) ? gradeInt : null,
    field: fieldStr,
    exam_date: String(raw.exam_date ?? ''),
    avg_percentage: Number.isFinite(avgNum) ? avgNum : 0,
    subjects,
    comments,
  };
}

app.use('/assets', express.static(path.join(__dirname, 'assets')));
app.get('/robots.txt', (_req, res) => {
  res.sendFile(path.join(__dirname, 'robots.txt'));
});
app.get('/sitemap.xml', (_req, res) => {
  res.sendFile(path.join(__dirname, 'sitemap.xml'));
});

app.get('/r/:token', async (req, res) => {
  const token = String(req.params.token || '').trim();
  if (!/^[A-Za-z0-9_-]{32,64}$/.test(token)) {
    renderExamResultPage(res, 404, 'not_found', null);
    return;
  }

  const apiUrl = `${famoInternalApiUrl()}/public/exam-results/${encodeURIComponent(token)}`;
  const clientIp = extractClientIp(req);
  const upstream = await fetchExamResult(apiUrl, clientIp);

  if (upstream.isRedirect || upstream.httpStatus === 0 || upstream.httpStatus >= 500) {
    res.setHeader('Retry-After', upstream.retryAfter || '30');
    renderExamResultPage(res, 503, 'unavailable', null);
    return;
  }

  if (upstream.httpStatus === 404) {
    renderExamResultPage(res, 404, 'not_found', null);
    return;
  }

  if (upstream.httpStatus === 429) {
    res.setHeader('Retry-After', upstream.retryAfter || '60');
    renderExamResultPage(res, 429, 'rate_limited', null);
    return;
  }

  if (upstream.httpStatus !== 200) {
    console.error(`[exam-result] unexpected upstream status=${upstream.httpStatus}`);
    res.setHeader('Retry-After', '30');
    renderExamResultPage(res, 503, 'unavailable', null);
    return;
  }

  const result = parseWhitelistedExamResult(upstream.body);
  if (result === null) {
    console.error('[exam-result] invalid JSON payload from upstream');
    res.setHeader('Retry-After', '30');
    renderExamResultPage(res, 503, 'unavailable', null);
    return;
  }

  renderExamResultPage(res, 200, null, result);
});

app.use('/r', (req, res, next) => {
  if (req.method !== 'GET' && req.method !== 'HEAD') {
    next();
    return;
  }
  renderExamResultPage(res, 404, 'not_found', null);
});

app.get(['/', '/index.php'], (_req, res) => {
  res.render('index', {
    base: '',
    pageTitle: 'آموزشگاه فامو | مشاوره کنکور و تیزهوشان بابل',
    metaDescription: 'آموزشگاه فامو بابل - مشاوره تخصصی کنکور و تیزهوشان',
  });
});

app.get('/pages/course.php', (_req, res) => {
  res.render('pages/course', { base: '../' });
});
app.get('/courses', (_req, res) => {
  res.render('pages/course', { base: '/' });
});

app.get('/pages/team.php', (_req, res) => {
  res.render('pages/team', { base: '../' });
});
app.get('/team', (_req, res) => {
  res.render('pages/team', { base: '/' });
});

app.get('/pages/blog.php', (_req, res) => {
  res.render('pages/blog', { base: '../' });
});
app.get('/blog', (_req, res) => {
  res.render('pages/blog', { base: '/' });
});

app.get('/pages/post.php', (_req, res) => {
  res.render('pages/post', { base: '../' });
});
app.get('/post', (_req, res) => {
  res.render('pages/post', { base: '/' });
});

app.get('/sitemap-blog.php', (_req, res) => {
  res.type('application/xml').send('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
});

app.listen(PORT, HOST, () => {
  console.log(`Famo landing server listening on http://${HOST}:${PORT}`);
});

