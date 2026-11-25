import fs from 'node:fs';
import http from 'node:http';
import https from 'node:https';
import path from 'node:path';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');

export const FAMO_HOME_DATA_ENDPOINTS = {
  courses: '/public/courses',
  instructors: '/public/instructors',
  supporters: '/public/supporters',
};

const CACHE_TTL_SEC = 60;
const CACHE_MAX_STALE_SEC = 3600;
const CACHE_RETRY_AFTER_SEC = 15;
const CACHE_FILENAME = 'home-data-v1.json';
const LOCK_FILENAME = 'home-data-v1.lock';

export const FA_ICON_MAP = {
  'fa-home': 'icon-home', 'fa-book': 'icon-book', 'fa-book-open': 'icon-book',
  'fa-school': 'icon-school', 'fa-graduation-cap': 'icon-school', 'fa-user-graduate': 'icon-school',
  'fa-chalkboard-teacher': 'icon-school', 'fa-chart-line': 'icon-chart-bar', 'fa-chart-bar': 'icon-chart-bar',
  'fa-user': 'icon-user', 'fa-users': 'icon-users', 'fa-user-friends': 'icon-users',
  'fa-user-plus': 'icon-user-plus', 'fa-calendar': 'icon-calendar', 'fa-calendar-alt': 'icon-calendar',
  'fa-star': 'icon-star', 'fa-headset': 'icon-headset', 'fa-phone': 'icon-phone',
  'fa-phone-alt': 'icon-phone', 'fa-envelope': 'icon-mail', 'fa-search': 'icon-search',
  'fa-edit': 'icon-edit', 'fa-pen': 'icon-edit', 'fa-pencil-alt': 'icon-edit',
  'fa-trash': 'icon-trash', 'fa-trash-alt': 'icon-trash', 'fa-download': 'icon-download',
  'fa-upload': 'icon-upload', 'fa-file': 'icon-file', 'fa-file-alt': 'icon-file',
  'fa-eye': 'icon-eye', 'fa-check': 'icon-check', 'fa-times': 'icon-x',
  'fa-plus': 'icon-plus', 'fa-cog': 'icon-settings', 'fa-cogs': 'icon-settings',
  'fa-calculator': 'icon-chart-bar', 'fa-flask': 'icon-report', 'fa-atom': 'icon-star',
  'fa-brain': 'icon-school', 'fa-clipboard': 'icon-clipboard', 'fa-clock': 'icon-clock',
  'fa-map-marker-alt': 'icon-map-pin', 'fa-filter': 'icon-filter', 'fa-bars': 'icon-menu',
  'fa-question-circle': 'icon-help-circle', 'fa-microscope': 'icon-search',
  'fa-square-root-alt': 'icon-chart-bar', 'fa-language': 'icon-book',
  'fa-paint-brush': 'icon-edit', 'fa-laptop-code': 'icon-settings',
  'fa-dna': 'icon-report', 'fa-globe': 'icon-external-link',
};

export const LUCIDE_ICON_MAP = {
  'icon-alert': 'triangle-alert', 'icon-arrow-left': 'arrow-left', 'icon-arrow-right': 'arrow-right',
  'icon-book': 'book-open', 'icon-brand-instagram': 'instagram', 'icon-brand-whatsapp': 'message-circle',
  'icon-calendar': 'calendar-days', 'icon-chart-bar': 'chart-column', 'icon-check': 'check',
  'icon-chevron-down': 'chevron-down', 'icon-chevron-left': 'chevron-left', 'icon-chevron-right': 'chevron-right',
  'icon-close': 'x', 'icon-clipboard': 'clipboard-list', 'icon-clock': 'clock-3',
  'icon-device-floppy': 'save', 'icon-download': 'download', 'icon-edit': 'pencil',
  'icon-external-link': 'external-link', 'icon-eye': 'eye', 'icon-eye-closed': 'eye-off',
  'icon-eye-open': 'eye', 'icon-file': 'file-text', 'icon-filter': 'list-filter',
  'icon-headset': 'headset', 'icon-help-circle': 'circle-help', 'icon-home': 'house',
  'icon-id-card': 'id-card', 'icon-info': 'info', 'icon-key': 'key-round',
  'icon-keyboard': 'keyboard', 'icon-logout': 'log-out', 'icon-mail': 'mail',
  'icon-map-pin': 'map-pin', 'icon-menu': 'menu', 'icon-minus': 'minus',
  'icon-newspaper': 'newspaper', 'icon-pause': 'pause', 'icon-phone': 'phone',
  'icon-plus': 'plus', 'icon-report': 'file-chart-column', 'icon-save': 'save',
  'icon-school': 'school', 'icon-search': 'search', 'icon-settings': 'settings',
  'icon-star': 'star', 'icon-telegram': 'send', 'icon-trash': 'trash-2',
  'icon-upload': 'upload', 'icon-user': 'user', 'icon-user-plus': 'user-plus',
  'icon-users': 'users', 'icon-x': 'x',
};

export const SOCIAL_ICON_MAP = {
  telegram: 'icon-telegram',
  instagram: 'icon-brand-instagram',
  whatsapp: 'icon-brand-whatsapp',
};

function envStr(key, fallback = '') {
  const val = process.env[key];
  return val === undefined || val === null || val === '' ? fallback : String(val);
}

function internalApiBase() {
  const apiUrl = envStr('API_URL', 'https://api.famoacademy.ir').replace(/\/+$/, '');
  const base = envStr('INTERNAL_API_URL', apiUrl).replace(/\/+$/, '');
  return /\/api\/v1$/.test(base) ? base : `${base}/api/v1`;
}

function uploadsBase() {
  return envStr('UPLOADS_URL', 'https://api.famoacademy.ir/uploads').replace(/\/+$/, '');
}

export function escHtml(value) {
  if (value === null || value === undefined || typeof value === 'object') return '';
  return String(value).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[c]));
}

function strVal(value) {
  if (value === null || value === undefined || typeof value === 'object' || typeof value === 'boolean') return '';
  return String(value);
}

export function safeUrl(value) {
  const s = strVal(value).trim();
  if (!s || /[\x00-\x1F\x7F]/.test(s)) return '';
  if (/^https?:\/\//i.test(s)) return s;
  if (s.startsWith('/') && !s.startsWith('//')) return s;
  return '';
}

export function safeHex(value, fallback) {
  const s = strVal(value).trim();
  return /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(s) ? s : fallback;
}

export function resolveImageUrl(value) {
  const src = strVal(value).trim();
  if (!src || /[\x00-\x1F\x7F]/.test(src)) return '';
  if (src.startsWith('//')) return '';
  if (/^https?:\/\//i.test(src)) return src;
  if (/^[a-z][a-z0-9+.-]*:/i.test(src)) return '';
  if (src.startsWith('/')) return src;
  if (src.startsWith('uploads/') || src.startsWith('../') || src.startsWith('assets/')) return '';
  return `${uploadsBase()}/${src.replace(/^\/+/, '')}`;
}

export function faToSvg(faClass) {
  const raw = strVal(faClass).trim();
  if (!raw) return 'icon-book';
  for (const part of raw.split(/\s+/)) {
    if (FA_ICON_MAP[part]) return FA_ICON_MAP[part];
  }
  return 'icon-book';
}

export function lucideName(name) {
  if (LUCIDE_ICON_MAP[name]) return LUCIDE_ICON_MAP[name];
  const stripped = String(name).replace(/^icon-/, '');
  return stripped || 'circle-help';
}

export function svgIcon(name, cls = '') {
  const c = cls ? ` ${cls}` : '';
  return `<i class="icon${escHtml(c)}" data-lucide="${escHtml(lucideName(name))}" aria-hidden="true"></i>`;
}

export function courseCleanUrl(course) {
  const name = strVal(course?.name);
  const grades = strVal(course?.target_grades);
  const haystack = `${name} ${grades}`;
  const map = [
    ['رفع اشکال', '/courses/rafa-eshkal-babol/'],
    ['تیزهوشان', '/courses/tizhooshan-babol/'],
    ['مشاوره', '/counseling-babol/'],
    ['زیست', '/courses/biology-babol/'],
    ['تجربی', '/courses/biology-babol/'],
    ['شیمی', '/courses/chemistry-babol/'],
    ['فیزیک', '/courses/physics-babol/'],
    ['ریاضی', '/courses/math-babol/'],
    ['حسابان', '/courses/math-babol/'],
    ['ابتدایی', '/courses/elementary-babol/'],
    ['متوسطه اول', '/courses/middle-school-babol/'],
    ['متوسطه دوم', '/courses/high-school-babol/'],
    ['کنکور', '/courses/konkur-babol/'],
  ];
  for (const [kw, url] of map) {
    if (haystack.includes(kw)) return url;
  }
  return '/courses/';
}

export function renderCourseSlide(course, index) {
  const from = safeHex(course.gradient_color_from, '#445D84');
  const to = safeHex(course.gradient_color_to, '#6D8B9E');
  const gradient = `linear-gradient(135deg, ${from}, ${to})`;
  const imageUrl = safeUrl(resolveImageUrl(course.background_image_url));
  const courseHref = courseCleanUrl(course);
  const courseName = strVal(course.name);
  const badgeLabel = strVal(course.badge_label);
  const targetGrades = strVal(course.target_grades);
  const format = strVal(course.format);
  const description = strVal(course.description);

  const badgeBlock = badgeLabel ? `<span class="famo-course-badge">${escHtml(badgeLabel)}</span>` : '';
  const metaItems = [
    targetGrades ? `<span>${escHtml(targetGrades)}</span>` : '',
    format ? `<span>${escHtml(format)}</span>` : '',
  ].filter(Boolean).join('');
  const metaBlock = metaItems ? `<div class="famo-course-meta">${metaItems}</div>` : '';
  const imgAttrs = `width="320" height="200" decoding="async"${index >= 3 ? ' loading="lazy"' : ''}`;
  const mediaInner = imageUrl
    ? `<img src="${escHtml(imageUrl)}" alt="${escHtml(courseName)}" ${imgAttrs} class="famo-course-img">`
    : `<div class="course-image-fallback">${svgIcon(faToSvg(course.icon), 'icon--3xl text-white/90')}</div>`;
  const descBlock = description ? `<p class="famo-course-desc">${escHtml(description)}</p>` : '';

  return `<div class="swiper-slide"><a class="famo-course-card" href="${escHtml(courseHref)}" aria-label="${escHtml(courseName)} - مشاهده جزئیات دوره"><div class="famo-course-media" style="background-image: ${gradient};">${mediaInner}${badgeBlock}</div><div class="famo-course-body"><h3 class="famo-course-name">${escHtml(courseName)}</h3>${metaBlock}${descBlock}<div class="famo-course-footer"><span class="famo-course-cta">جزئیات دوره <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i></span></div></div></a></div>`;
}

export function renderCourseGridCard(course) {
  const from = safeHex(course.gradient_color_from, '#445D84');
  const to = safeHex(course.gradient_color_to, '#6D8B9E');
  const rawImg = strVal(course.background_image_url) || strVal(course.image_url) || strVal(course.image);
  const imageUrl = safeUrl(resolveImageUrl(rawImg));
  const courseId = strVal(course.id);
  const courseName = strVal(course.name);
  const badgeLabel = strVal(course.badge_label);
  const targetGrades = strVal(course.target_grades);
  const format = strVal(course.format);
  const bodyText = strVal(course.full_description) || strVal(course.description);
  const detailUrl = courseCleanUrl(course);

  const badgeBlock = badgeLabel ? `<span class="course-badge">${escHtml(badgeLabel)}</span>` : '';
  const metaItems = [
    targetGrades ? `<span>${escHtml(targetGrades)}</span>` : '',
    format ? `<span>${escHtml(format)}</span>` : '',
  ].filter(Boolean).join('');
  const metaBlock = metaItems ? `<div class="course-meta">${metaItems}</div>` : '';
  const featuresHtml = Array.isArray(course.features)
    ? course.features
      .map((f) => strVal(f).trim())
      .filter(Boolean)
      .map((f) => `<li>${svgIcon('icon-check', 'icon--sm text-[#445D84]')}<span>${escHtml(f)}</span></li>`)
      .join('')
    : '';
  const featuresBlock = featuresHtml ? `<ul class="course-features">${featuresHtml}</ul>` : '';
  const mediaInner = imageUrl
    ? `<img src="${escHtml(imageUrl)}" alt="${escHtml(courseName)}" loading="lazy" decoding="async" class="course-image">`
    : `<div class="course-image-fallback">${svgIcon(faToSvg(course.icon), 'icon--3xl text-white/90')}</div>`;
  const detailLink = detailUrl !== '/courses/'
    ? `<a href="${escHtml(detailUrl)}" class="text-sm font-bold text-[#445D84] hover:underline inline-flex items-center gap-1">جزئیات بیشتر ${svgIcon('icon-chevron-left', 'icon--sm')}</a>`
    : '';

  return `<article id="course-${escHtml(courseId)}" class="course-card"><div class="course-image-wrapper" style="background: linear-gradient(135deg, ${from}, ${to});">${mediaInner}${badgeBlock}</div><div class="course-body"><h3 class="course-name">${escHtml(courseName)}</h3>${metaBlock}${bodyText ? `<p class="course-desc">${escHtml(bodyText)}</p>` : ''}${featuresBlock}<div class="course-footer"><a href="/contact/#consultation" class="course-cta">درخواست مشاوره</a>${detailLink}</div></div></article>`;
}

export function renderSocialLinks(links) {
  if (!Array.isArray(links) || links.length === 0) return '';
  const items = links
    .map((link) => {
      if (!link || typeof link !== 'object') return '';
      const url = safeUrl(link.url);
      if (!url) return '';
      const platform = strVal(link.platform);
      const icon = SOCIAL_ICON_MAP[platform] || 'icon-external-link';
      return `<a href="${escHtml(url)}" target="_blank" rel="noopener" class="inst-social" aria-label="${escHtml(platform)}">${svgIcon(icon, 'icon--sm')}</a>`;
    })
    .filter(Boolean)
    .join('');
  return items ? `<div class="inst-socials">${items}</div>` : '';
}

export function normalizeInstructor(inst) {
  return {
    role: 'instructor',
    roleLabel: 'استاد',
    name: strVal(inst.name),
    title: strVal(inst.title) || 'استاد تخصصی',
    description: strVal(inst.description) || strVal(inst.full_bio),
    image_url: strVal(inst.image_url) || strVal(inst.avatar_url),
    initial_letter: strVal(inst.initial_letter),
    social_links: Array.isArray(inst.social_links) ? inst.social_links : [],
    tag: '',
  };
}

export function normalizeSupporter(sup) {
  const field = strVal(sup.field);
  const grade = strVal(sup.grade);
  const fieldLabel = field ? `رشته ${field}` : (grade ? `پایه ${grade}` : 'تحصیلی');
  return {
    role: 'supporter',
    roleLabel: 'پشتیبان',
    name: strVal(sup.name),
    title: strVal(sup.title) || `پشتیبان تخصصی ${fieldLabel}`,
    description: strVal(sup.description) || strVal(sup.full_bio) || strVal(sup.bio)
      || 'پیگیری برنامه مطالعه، رفع اشکال درسی و همراهی مستمر دانش‌آموزان تا رسیدن به هدف.',
    image_url: strVal(sup.image_url) || strVal(sup.avatar_url) || strVal(sup.image),
    initial_letter: strVal(sup.initial_letter),
    social_links: Array.isArray(sup.social_links) ? sup.social_links : [],
    tag: '',
  };
}

export function buildTeamList(instructors, supporters) {
  return [
    ...instructors.filter((x) => x && typeof x === 'object').map(normalizeInstructor),
    ...supporters.filter((x) => x && typeof x === 'object').map(normalizeSupporter),
  ];
}

export function renderPersonCard(person) {
  const role = person.role === 'supporter' ? 'supporter' : 'instructor';
  const name = strVal(person.name);
  const title = strVal(person.title);
  const description = strVal(person.description);
  const roleLabel = strVal(person.roleLabel);
  const tag = strVal(person.tag);
  const initialLetter = strVal(person.initial_letter) || (name.trim() ? Array.from(name.trim())[0] : '');
  const rawImg = strVal(person.image_url) || strVal(person.avatar_url) || strVal(person.image);
  const avatarUrl = safeUrl(resolveImageUrl(rawImg));
  const socialsBlock = renderSocialLinks(person.social_links);
  const footerBlock = socialsBlock || (tag ? `<div class="inst-socials person-card__footer"><span class="person-tag">${escHtml(tag)}</span></div>` : '');

  const mediaInner = avatarUrl
    ? `<img class="inst-avatar" src="${escHtml(avatarUrl)}" alt="${escHtml(name)}" width="640" height="450" loading="lazy" decoding="async">`
    : `<div class="inst-avatar inst-avatar-ph" aria-hidden="true">${escHtml(initialLetter)}</div>`;

  return `<article class="inst-card person-card person-card--${role}" data-person-role="${role}"><div class="person-media">${mediaInner}<span class="person-role-badge">${escHtml(roleLabel)}</span></div><h3 class="inst-name">${escHtml(name)}</h3><p class="inst-title">${escHtml(title)}</p>${description ? `<p class="inst-bio">${escHtml(description)}</p>` : ''}${footerBlock}</article>`;
}

export function renderTeamCards(teamList) {
  const total = teamList.length;
  if (total === 0) return '';
  if (total < 3) {
    return teamList
      .map((person) => `<div class="curve-carousel__card" data-person-role="${person.role === 'supporter' ? 'supporter' : 'instructor'}">${renderPersonCard(person)}</div>`)
      .join('');
  }
  const repeatMultiplier = total < 5 ? Math.ceil(7 / total) : 1;
  const renderCount = Math.max(total * repeatMultiplier, 7);
  const out = [];
  for (let j = 0; j < renderCount; j += 1) {
    const idx = j % total;
    const person = teamList[idx];
    const role = person.role === 'supporter' ? 'supporter' : 'instructor';
    out.push(`<div class="curve-carousel__card" data-virtual-idx="${j}" data-item-idx="${idx}" data-person-role="${role}">${renderPersonCard(person)}</div>`);
  }
  return out.join('');
}

export function renderTeamDots(teamList) {
  const total = teamList.length;
  if (total <= 1 || total > 12) return '';
  return teamList
    .map((person, i) => `<button type="button" class="curve-carousel__dot" data-curve-dot="${i}" aria-label="${escHtml(`اسلاید ${i + 1}: ${strVal(person.roleLabel)} ${strVal(person.name)}`)}"></button>`)
    .join('');
}

function emptyResult() {
  const out = {};
  for (const key of Object.keys(FAMO_HOME_DATA_ENDPOINTS)) out[key] = null;
  return out;
}

function normalizeList(val) {
  return Array.isArray(val) && val.length > 0 ? val : null;
}

function readCacheFile(cacheFile) {
  try {
    if (!fs.existsSync(cacheFile)) return null;
    const raw = fs.readFileSync(cacheFile, 'utf8');
    const parsed = JSON.parse(raw);
    return parsed && typeof parsed === 'object' ? parsed : null;
  } catch {
    return null;
  }
}

function extractCached(cached, nowSec) {
  const out = emptyResult();
  const dataMap = cached && typeof cached.data === 'object' && cached.data ? cached.data : {};
  const updatedMap = cached && typeof cached.key_updated_at === 'object' && cached.key_updated_at ? cached.key_updated_at : {};
  const fallbackTs = Number(cached?.fetched_at) || 0;
  for (const key of Object.keys(FAMO_HOME_DATA_ENDPOINTS)) {
    const keyTs = Number(updatedMap[key]) || fallbackTs;
    if (keyTs <= 0 || (nowSec - keyTs) > CACHE_MAX_STALE_SEC) {
      out[key] = null;
    } else {
      out[key] = normalizeList(dataMap[key]);
    }
  }
  return out;
}

function fetchJsonEndpoint(url) {
  return new Promise((resolve) => {
    let parsed;
    try {
      parsed = new URL(url);
    } catch {
      resolve({ ok: false, data: null, status: 0 });
      return;
    }
    const transport = parsed.protocol === 'https:' ? https : http;
    let done = false;
    let connectTimer = null;
    let totalTimer = null;
    const finish = (res) => {
      if (done) return;
      done = true;
      if (connectTimer) clearTimeout(connectTimer);
      if (totalTimer) clearTimeout(totalTimer);
      resolve(res);
    };

    const req = transport.request(
      parsed,
      { method: 'GET', headers: { Accept: 'application/json' } },
      (upRes) => {
        const status = Number(upRes.statusCode || 0);
        if (status >= 300 && status < 400) {
          upRes.resume();
          finish({ ok: false, data: null, status });
          return;
        }
        const chunks = [];
        upRes.on('data', (c) => chunks.push(c));
        upRes.on('end', () => {
          if (status !== 200) {
            finish({ ok: false, data: null, status });
            return;
          }
          try {
            const decoded = JSON.parse(Buffer.concat(chunks).toString('utf8'));
            if (!decoded || typeof decoded !== 'object' || !Array.isArray(decoded.data)) {
              finish({ ok: false, data: null, status });
              return;
            }
            finish({ ok: true, data: normalizeList(decoded.data), status: 200 });
          } catch {
            finish({ ok: false, data: null, status });
          }
        });
        upRes.on('error', () => finish({ ok: false, data: null, status: 0 }));
      },
    );

    totalTimer = setTimeout(() => req.destroy(new Error('TIMEOUT')), 2000);
    req.on('socket', (sock) => {
      if (sock.connecting) {
        connectTimer = setTimeout(() => req.destroy(new Error('CONNECT_TIMEOUT')), 1000);
        sock.once('connect', () => {
          if (connectTimer) clearTimeout(connectTimer);
        });
      }
    });
    req.on('error', () => finish({ ok: false, data: null, status: 0 }));
    req.end();
  });
}

async function fetchAllConcurrent(baseUrl) {
  const entries = Object.entries(FAMO_HOME_DATA_ENDPOINTS);
  const settled = await Promise.all(
    entries.map(async ([key, ep]) => [key, await fetchJsonEndpoint(`${baseUrl}/${ep.replace(/^\/+/, '')}`)]),
  );
  return Object.fromEntries(settled);
}

function mergeResults(fetched, previousCache, nowSec) {
  const prevData = previousCache?.data && typeof previousCache.data === 'object' ? previousCache.data : {};
  const prevUpdated = previousCache?.key_updated_at && typeof previousCache.key_updated_at === 'object' ? previousCache.key_updated_at : {};
  const prevFetchedAt = Number(previousCache?.fetched_at) || 0;

  const newData = {};
  const newUpdated = {};
  let anyFailure = false;

  for (const key of Object.keys(FAMO_HOME_DATA_ENDPOINTS)) {
    const item = fetched[key] || { ok: false, data: null, status: 0 };
    if (item.ok) {
      newData[key] = item.data;
      newUpdated[key] = nowSec;
    } else {
      anyFailure = true;
      const keyTs = Number(prevUpdated[key]) || prevFetchedAt;
      const prevList = normalizeList(prevData[key]);
      if (prevList !== null && keyTs > 0 && (nowSec - keyTs) <= CACHE_MAX_STALE_SEC) {
        newData[key] = prevList;
        newUpdated[key] = keyTs;
      } else {
        newData[key] = null;
        newUpdated[key] = 0;
      }
    }
  }

  return {
    payload: {
      fetched_at: anyFailure ? prevFetchedAt : nowSec,
      retry_after: anyFailure ? (nowSec + CACHE_RETRY_AFTER_SEC) : 0,
      data: newData,
      key_updated_at: newUpdated,
    },
    data: newData,
  };
}

function writeCacheAtomic(cacheDir, cacheFile, payload) {
  const suffix = crypto.randomBytes(4).toString('hex');
  const tmpFile = path.join(cacheDir, `home-data-v1.${process.pid}.${suffix}.tmp`);
  try {
    fs.writeFileSync(tmpFile, JSON.stringify(payload), 'utf8');
    fs.renameSync(tmpFile, cacheFile);
  } catch {
    try { fs.unlinkSync(tmpFile); } catch {}
  }
}

// Track active in-process lock handles by cacheDir so concurrent async requests emulate LOCK_EX | LOCK_NB
const activeLocks = new Set();

export async function famoHomeData(opts = {}) {
  try {
    const killSwitch = (opts.homeSsr ?? envStr('HOME_SSR', '')).trim();
    if (killSwitch === '0') {
      return emptyResult();
    }

    const nowSec = typeof opts.nowSec === 'number' ? opts.nowSec : Math.floor(Date.now() / 1000);
    const cacheDir = opts.cacheDir ?? path.join(ROOT_DIR, 'storage', 'cache');
    const cacheFile = path.join(cacheDir, CACHE_FILENAME);
    const lockFile = path.join(cacheDir, LOCK_FILENAME);
    const apiBase = (opts.internalApiUrl ?? internalApiBase()).replace(/\/+$/, '');

    let canCache = false;
    try {
      const st = fs.statSync(cacheDir);
      if (st.isDirectory()) {
        fs.accessSync(cacheDir, fs.constants.W_OK | fs.constants.R_OK);
        canCache = true;
      }
    } catch {
      canCache = false;
    }

    if (!canCache) {
      const fetched = await fetchAllConcurrent(apiBase);
      const out = emptyResult();
      for (const key of Object.keys(FAMO_HOME_DATA_ENDPOINTS)) {
        out[key] = fetched[key]?.ok ? fetched[key].data : null;
      }
      return out;
    }

    let cached = readCacheFile(cacheFile);
    if (cached) {
      const fetchedAt = Number(cached.fetched_at) || 0;
      const retryAfter = Number(cached.retry_after) || 0;
      if (fetchedAt > 0 && (nowSec - fetchedAt) < CACHE_TTL_SEC) {
        return extractCached(cached, nowSec);
      }
      if (retryAfter > 0 && nowSec < retryAfter) {
        return extractCached(cached, nowSec);
      }
    }

    // Non-blocking lock emulation (in-process set + O_CREAT|O_EXCL lock file)
    if (activeLocks.has(lockFile)) {
      return cached ? extractCached(cached, nowSec) : emptyResult();
    }

    let lockFd = null;
    try {
      lockFd = fs.openSync(lockFile, 'wx');
      activeLocks.add(lockFile);
    } catch (err) {
      if (err && err.code === 'EEXIST') {
        return cached ? extractCached(cached, nowSec) : emptyResult();
      }
      // Lock file cannot be created (e.g., permission denied): degrade to no caching
      const fetched = await fetchAllConcurrent(apiBase);
      const out = emptyResult();
      for (const key of Object.keys(FAMO_HOME_DATA_ENDPOINTS)) {
        out[key] = fetched[key]?.ok ? fetched[key].data : null;
      }
      return out;
    }

    try {
      const freshCheck = readCacheFile(cacheFile);
      if (freshCheck) {
        const fetchedAt = Number(freshCheck.fetched_at) || 0;
        const retryAfter = Number(freshCheck.retry_after) || 0;
        if (
          (fetchedAt > 0 && (nowSec - fetchedAt) < CACHE_TTL_SEC) ||
          (retryAfter > 0 && nowSec < retryAfter)
        ) {
          return extractCached(freshCheck, nowSec);
        }
        cached = freshCheck;
      }

      const fetched = await fetchAllConcurrent(apiBase);
      const merged = mergeResults(fetched, cached, nowSec);
      writeCacheAtomic(cacheDir, cacheFile, merged.payload);
      return merged.data;
    } finally {
      activeLocks.delete(lockFile);
      try { if (lockFd !== null) fs.closeSync(lockFd); } catch {}
      try { fs.unlinkSync(lockFile); } catch {}
    }
  } catch {
    return emptyResult();
  }
}
