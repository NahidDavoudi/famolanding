import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import http from 'node:http';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import {
  famoHomeData,
  famoTestimonialsPageData,
  renderCourseSlide,
  buildTeamList,
  renderTeamCards,
  renderTeamDots,
  renderHomeTestimonialExcerpt,
  renderTestimonialSummary,
  renderTestimonialCard,
  renderTestimonialsPagination,
  buildTestimonialsSchema,
  safeUrl,
  safeHex,
  resolveImageUrl,
  escHtml,
} from './home-ssr-preview.mjs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT_DIR = path.resolve(__dirname, '..');

function makeMockApi(handler) {
  const log = [];
  const server = http.createServer((req, res) => {
    const reqUrl = new URL(req.url || '/', 'http://127.0.0.1');
    log.push({
      path: reqUrl.pathname,
      search: reqUrl.search,
      accept: req.headers.accept || '',
      hasXff: Boolean(req.headers['x-forwarded-for']),
      at: Date.now(),
    });
    handler(reqUrl.pathname, req, res);
  });
  return new Promise((resolve) => {
    server.listen(0, '127.0.0.1', () => {
      const { port } = server.address();
      resolve({
        url: `http://127.0.0.1:${port}/api/v1`,
        log,
        close: () => new Promise((r) => server.close(r)),
      });
    });
  });
}

function makeTempCacheDir() {
  return fs.mkdtempSync(path.join(os.tmpdir(), 'famo-home-ssr-'));
}

const SAMPLE_COURSES = [
  {
    id: 1,
    name: 'علوم تجربی',
    description: 'دوره تخصصی زیست و شیمی',
    target_grades: 'دهم تا دوازدهم',
    format: 'خصوصی',
    badge_label: 'پرمخاطب',
    background_image_url: 'https://assets.famoacademy.ir/images/course-1.webp',
    gradient_color_from: '#445D84',
    gradient_color_to: '#6D8B9E',
    icon: 'fas fa-flask',
  },
  {
    id: 2,
    name: 'ریاضی فیزیک',
    description: 'آموزش مفهومی ریاضی و فیزیک',
    target_grades: 'متوسطه دوم',
    format: '',
    badge_label: '',
    background_image_url: 'course-2.webp',
    gradient_color_from: '#334869',
    gradient_color_to: '#445D84',
    icon: 'fas fa-calculator',
  },
  {
    id: 3,
    name: 'علوم انسانی',
    description: 'دروس اختصاصی انسانی',
    target_grades: '',
    format: 'انفرادی',
    badge_label: '',
    background_image_url: '',
    gradient_color_from: '#445D84',
    gradient_color_to: '#6D8B9E',
    icon: 'fas fa-book-open',
  },
  {
    id: 4,
    name: 'آمادگی تیزهوشان',
    description: 'هوش و استعداد تحلیلی',
    target_grades: 'ششم و نهم',
    format: 'خصوصی',
    badge_label: 'ویژه',
    background_image_url: '/assets/images/poster_hero.webp',
    gradient_color_from: '#445D84',
    gradient_color_to: '#6D8B9E',
    icon: 'fas fa-brain',
  },
  {
    id: 5,
    name: 'دوره پنجم نباید نمایش داده شود',
    description: 'تست محدودیت ۴ دوره',
  },
];

const SAMPLE_INSTRUCTORS = [
  {
    name: 'دکتر مانی محسنی',
    title: 'مدرس زیست',
    description: 'سابقه ۳ سال تدریس زیست',
    image_url: 'https://assets.famoacademy.ir/images/logo.png',
    social_links: [{ platform: 'telegram', url: 'https://t.me/famoacademy' }],
  },
  {
    name: 'دکتر یاسین داوودزاده',
    title: '',
    full_bio: 'مدرس فیزیک کنکور',
    avatar_url: '',
    initial_letter: 'ی',
    social_links: [],
  },
];

const SAMPLE_SUPPORTERS = [
  {
    name: 'سارا محمدی',
    field: 'تجربی',
    bio: 'پیگیری هفتگی آزمون‌ها',
    image: '',
    social_links: [{ platform: 'instagram', url: 'https://instagram.com/famo' }],
  },
];

const SAMPLE_TESTIMONIALS_RESPONSE = {
  success: true,
  data: {
    items: [
      {
        id: 8,
        author_name: 'سارا احمدی',
        author_role: 'student',
        author_meta: 'دوازدهم تجربی ۱۴۰۴',
        rating: 5,
        content: 'برنامه‌ریزی شخصی و آزمون‌های هفتگی فامو واقعاً باعث پیشرفت چشمگیرم شد.',
        course_name: 'زیست‌شناسی کنکور',
        instructor_name: 'دکتر مانی محسنی',
        is_featured: true,
        created_at: '2026-11-20 14:20:00',
      },
      {
        id: 7,
        author_name: 'مادر رضا کریمی',
        author_role: 'parent',
        author_meta: 'ولی پایه نهم',
        rating: 4,
        content: 'گزارش‌های منظم هفتگی به ما کمک کرد همیشه در جریان وضعیت درسی فرزندمان باشیم.',
        course_name: null,
        instructor_name: null,
        is_featured: false,
        created_at: '2026-11-18 10:05:00',
      },
    ],
    summary: {
      total_approved: 12,
      average_rating: 4.8,
    },
  },
  pagination: {
    page: 1,
    per_page: 9,
    total: 12,
    total_pages: 2,
  },
};

const EMPTY_HOME_RESULT = { courses: null, instructors: null, supporters: null, testimonials: null };

test('PHP SSR files conform to strict types, security, curl_multi, flock, atomic rename, testimonials SSR, and kill switch', () => {
  const homeDataPhp = fs.readFileSync(path.join(ROOT_DIR, 'layouts/home-data.php'), 'utf8');
  const homeRenderPhp = fs.readFileSync(path.join(ROOT_DIR, 'layouts/home-render.php'), 'utf8');
  const indexPhp = fs.readFileSync(path.join(ROOT_DIR, 'index.php'), 'utf8');
  const resultsPhp = fs.readFileSync(path.join(ROOT_DIR, 'pages/results.php'), 'utf8');
  const mainJs = fs.readFileSync(path.join(ROOT_DIR, 'assets/js/main.js'), 'utf8');
  const testiJs = fs.readFileSync(path.join(ROOT_DIR, 'assets/js/testimonials.js'), 'utf8');

  assert.match(homeDataPhp, /^<\?php\s+declare\(strict_types=1\);/);
  assert.match(homeRenderPhp, /^<\?php\s+declare\(strict_types=1\);/);
  assert.match(indexPhp, /^<\?php\s+declare\(strict_types=1\);/);
  assert.match(resultsPhp, /^<\?php\s+declare\(strict_types=1\);/);

  // Data layer requirements
  assert.match(homeDataPhp, /function famo_home_data\(/);
  assert.match(homeDataPhp, /function famo_testimonials_page_data\(/);
  assert.match(homeDataPhp, /'testimonials'\s*=>\s*'\/public\/testimonials\?page=1&per_page=3'/);
  assert.match(homeDataPhp, /curl_multi_init/);
  assert.match(homeDataPhp, /curl_multi_exec/);
  assert.match(homeDataPhp, /CURLOPT_CONNECTTIMEOUT\s*=>\s*1/);
  assert.match(homeDataPhp, /CURLOPT_TIMEOUT\s*=>\s*2/);
  assert.match(homeDataPhp, /CURLOPT_FOLLOWLOCATION\s*=>\s*false/);
  assert.match(homeDataPhp, /Accept: application\/json/);
  assert.match(homeDataPhp, /LOCK_EX\s*\|\s*LOCK_NB/);
  assert.match(homeDataPhp, /@?rename\(/);
  assert.match(homeDataPhp, /HOME_SSR/);
  assert.match(homeDataPhp, /home-data-v1\.json/);
  assert.match(homeDataPhp, /testimonials-page1-v1\.json/);
  assert.match(homeDataPhp, /retry_after/);
  assert.match(homeDataPhp, /fetched_at/);
  // Never forward visitor headers/IP
  assert.doesNotMatch(homeDataPhp, /\$_SERVER|X-Forwarded-For|REMOTE_ADDR|HTTP_USER_AGENT/i);

  // Render layer security + testimonials renderers
  assert.match(homeRenderPhp, /ENT_QUOTES\s*\|\s*ENT_SUBSTITUTE/);
  assert.match(homeRenderPhp, /\/\^#\(\[0-9a-f\]\{3\}\|\[0-9a-f\]\{6\}\)\$\/i/);
  assert.match(homeRenderPhp, /https:\/\/api\.famoacademy\.ir\/uploads/);
  assert.match(homeRenderPhp, /function famo_render_home_testimonial_excerpt\(/);
  assert.match(homeRenderPhp, /function famo_render_testimonial_summary\(/);
  assert.match(homeRenderPhp, /function famo_render_testimonial_card\(/);
  assert.match(homeRenderPhp, /function famo_build_testimonials_schema\(/);

  // index.php wiring
  assert.match(indexPhp, /require_once __DIR__ \. '\/layouts\/home-data\.php';/);
  assert.match(indexPhp, /require_once __DIR__ \. '\/layouts\/home-render\.php';/);
  assert.match(indexPhp, /famo_home_data\(\)/);
  assert.match(indexPhp, /famo_render_home_testimonial_excerpt\(/);
  assert.match(indexPhp, /famo_build_testimonials_schema\(/);
  assert.match(indexPhp, /id="homeTestimonialsExcerpt"[^>]*\$hasTestimonialsSsr/);
  assert.match(indexPhp, /data-ssr="1"/);
  assert.match(indexPhp, /data-ssr-total="/);

  // results.php wiring
  assert.match(resultsPhp, /require_once __DIR__ \. '\/\.\.\/layouts\/home-data\.php';/);
  assert.match(resultsPhp, /famo_testimonials_page_data\(9\)/);
  assert.match(resultsPhp, /famo_render_testimonial_summary\(/);
  assert.match(resultsPhp, /famo_render_testimonial_card\(/);
  assert.match(resultsPhp, /famo_build_testimonials_schema\(/);
  assert.match(resultsPhp, /data-ssr-total-pages="/);

  // JS hydration skips SSR sections
  assert.match(mainJs, /testiBox && testiBox\.dataset\.ssr !== '1'/);
  assert.match(testiJs, /listEl\.dataset\.ssr === '1'/);
  assert.match(testiJs, /ssrTotalPages/);
});

test('Warm cache makes zero API calls within 60s TTL; cold fetch runs concurrently across all 4 endpoints', async () => {
  const cacheDir = makeTempCacheDir();
  const mock = await makeMockApi((pathname, _req, res) => {
    setTimeout(() => {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      if (pathname === '/api/v1/public/courses') return res.end(JSON.stringify({ success: true, data: SAMPLE_COURSES }));
      if (pathname === '/api/v1/public/instructors') return res.end(JSON.stringify({ success: true, data: SAMPLE_INSTRUCTORS }));
      if (pathname === '/api/v1/public/supporters') return res.end(JSON.stringify({ success: true, data: SAMPLE_SUPPORTERS }));
      if (pathname === '/api/v1/public/testimonials') return res.end(JSON.stringify(SAMPLE_TESTIMONIALS_RESPONSE));
      res.writeHead(404);
      res.end('{}');
    }, 180);
  });

  try {
    const t0 = Date.now();
    const first = await famoHomeData({ internalApiUrl: mock.url, cacheDir, nowSec: 1000 });
    const elapsed = Date.now() - t0;

    assert.equal(first.courses.length, 5);
    assert.equal(first.instructors.length, 2);
    assert.equal(first.supporters.length, 1);
    assert.equal(first.testimonials.items.length, 2);
    assert.equal(first.testimonials.summary.total_approved, 12);
    assert.equal(mock.log.length, 4, 'Cold fetch calls all 4 endpoints once');
    assert.ok(elapsed < 480, `Expected concurrent fetch (<480ms for 4×180ms), got ${elapsed}ms`);
    assert.ok(mock.log.every((r) => r.accept === 'application/json' && !r.hasXff));

    // Warm cache within 60s -> zero API calls
    const second = await famoHomeData({ internalApiUrl: mock.url, cacheDir, nowSec: 1059 });
    assert.equal(mock.log.length, 4, 'Warm cache must make 0 API calls');
    assert.deepEqual(second, first);
  } finally {
    await mock.close();
    fs.rmSync(cacheDir, { recursive: true, force: true });
  }
});

test('HOME_SSR=0 kill switch returns all nulls immediately with zero API calls (home + testimonials page)', async () => {
  const cacheDir = makeTempCacheDir();
  const mock = await makeMockApi((_p, _req, res) => {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify(SAMPLE_TESTIMONIALS_RESPONSE));
  });
  try {
    const res = await famoHomeData({ internalApiUrl: mock.url, cacheDir, homeSsr: '0' });
    assert.deepEqual(res, EMPTY_HOME_RESULT);
    const testiRes = await famoTestimonialsPageData({ internalApiUrl: mock.url, cacheDir, homeSsr: '0' });
    assert.equal(testiRes, null);
    assert.equal(mock.log.length, 0);
  } finally {
    await mock.close();
    fs.rmSync(cacheDir, { recursive: true, force: true });
  }
});

test('Failure modes: unreachable API, 500, 429 + retry_after backoff, invalid JSON, empty items, partial failure', async () => {
  // 1) Unreachable API
  const dir1 = makeTempCacheDir();
  const unreachable = await famoHomeData({ internalApiUrl: 'http://127.0.0.1:1/api/v1', cacheDir: dir1, nowSec: 2000 });
  assert.deepEqual(unreachable, EMPTY_HOME_RESULT);
  fs.rmSync(dir1, { recursive: true, force: true });

  // 2) HTTP 429 sets retry_after = now + 15s and suppresses API calls before then
  const dir2 = makeTempCacheDir();
  let statusMode = 429;
  const mock429 = await makeMockApi((pathname, _req, res) => {
    if (statusMode === 429) {
      res.writeHead(429, { 'Content-Type': 'application/json' });
      return res.end('{"error":"rate_limited"}');
    }
    if (statusMode === 500) {
      res.writeHead(500);
      return res.end('server error');
    }
    if (statusMode === 'badjson') {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      return res.end('not-json{{{');
    }
    if (statusMode === 'empty') {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      if (pathname.endsWith('testimonials')) {
        return res.end(JSON.stringify({ success: true, data: { items: [], summary: { total_approved: 0, average_rating: 0 } } }));
      }
      return res.end(JSON.stringify({ success: true, data: [] }));
    }
    if (statusMode === 'partial') {
      if (pathname === '/api/v1/public/supporters') {
        res.writeHead(500);
        return res.end('fail');
      }
      res.writeHead(200, { 'Content-Type': 'application/json' });
      if (pathname.endsWith('testimonials')) return res.end(JSON.stringify(SAMPLE_TESTIMONIALS_RESPONSE));
      return res.end(JSON.stringify({
        success: true,
        data: pathname.endsWith('courses') ? SAMPLE_COURSES : SAMPLE_INSTRUCTORS,
      }));
    }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ success: true, data: SAMPLE_COURSES }));
  });

  try {
    const r429 = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3000 });
    assert.deepEqual(r429, EMPTY_HOME_RESULT);
    assert.equal(mock429.log.length, 4);

    // 10s later (< 15s retry_after): MUST NOT hit API again
    const rBackoff = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3010 });
    assert.deepEqual(rBackoff, EMPTY_HOME_RESULT);
    assert.equal(mock429.log.length, 4, 'Must respect retry_after=15s backoff');

    // 16s later: retries (test 500)
    statusMode = 500;
    const r500 = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3016 });
    assert.deepEqual(r500, EMPTY_HOME_RESULT);
    assert.equal(mock429.log.length, 8);

    // Invalid JSON after backoff
    statusMode = 'badjson';
    const rBad = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3032 });
    assert.deepEqual(rBad, EMPTY_HOME_RESULT);

    // Empty arrays / empty testimonials items treated as null
    statusMode = 'empty';
    const rEmpty = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3048 });
    assert.deepEqual(rEmpty, EMPTY_HOME_RESULT);

    // Partial failure: courses, instructors, testimonials succeed; supporters fails -> supporters is null
    statusMode = 'partial';
    const rPartial = await famoHomeData({ internalApiUrl: mock429.url, cacheDir: dir2, nowSec: 3120 });
    assert.equal(rPartial.courses.length, 5);
    assert.equal(rPartial.instructors.length, 2);
    assert.equal(rPartial.supporters, null);
    assert.equal(rPartial.testimonials.items.length, 2);
  } finally {
    await mock429.close();
    fs.rmSync(dir2, { recursive: true, force: true });
  }
});

test('Stale cache (<= 1h) served when API fails; expired (> 1h) returns null (home + testimonials page)', async () => {
  const cacheDir = makeTempCacheDir();
  let failApi = false;
  const mock = await makeMockApi((pathname, _req, res) => {
    if (failApi) {
      res.writeHead(500);
      return res.end('down');
    }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    if (pathname.endsWith('courses')) return res.end(JSON.stringify({ success: true, data: SAMPLE_COURSES }));
    if (pathname.endsWith('instructors')) return res.end(JSON.stringify({ success: true, data: SAMPLE_INSTRUCTORS }));
    if (pathname.endsWith('testimonials')) return res.end(JSON.stringify(SAMPLE_TESTIMONIALS_RESPONSE));
    return res.end(JSON.stringify({ success: true, data: SAMPLE_SUPPORTERS }));
  });

  try {
    const warm = await famoHomeData({ internalApiUrl: mock.url, cacheDir, nowSec: 10000 });
    assert.equal(warm.courses.length, 5);
    assert.equal(warm.testimonials.items.length, 2);

    const warmPage = await famoTestimonialsPageData({ internalApiUrl: mock.url, cacheDir, nowSec: 10000, perPage: 9 });
    assert.equal(warmPage.items.length, 2);
    assert.equal(warmPage.pagination.total_pages, 2);

    // 10 minutes later (> 60s TTL, <= 1h stale) and API is down -> serves stale cache
    failApi = true;
    const stale = await famoHomeData({ internalApiUrl: mock.url, cacheDir, nowSec: 10600 });
    assert.equal(stale.courses.length, 5);
    assert.equal(stale.instructors.length, 2);
    assert.equal(stale.supporters.length, 1);
    assert.equal(stale.testimonials.items.length, 2);

    const stalePage = await famoTestimonialsPageData({ internalApiUrl: mock.url, cacheDir, nowSec: 10600, perPage: 9 });
    assert.equal(stalePage.items.length, 2);

    // > 1 hour later (13700 - 10000 = 3700 > 3600) and API still down -> returns nulls
    const expired = await famoHomeData({ internalApiUrl: mock.url, cacheDir, nowSec: 13700 });
    assert.deepEqual(expired, EMPTY_HOME_RESULT);

    const expiredPage = await famoTestimonialsPageData({ internalApiUrl: mock.url, cacheDir, nowSec: 13700, perPage: 9 });
    assert.equal(expiredPage, null);
  } finally {
    await mock.close();
    fs.rmSync(cacheDir, { recursive: true, force: true });
  }
});

test('Missing or read-only cache dir degrades cleanly without errors; concurrent empty-cache requests do not stampede', async () => {
  const mock = await makeMockApi((pathname, _req, res) => {
    setTimeout(() => {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      if (pathname.endsWith('courses')) return res.end(JSON.stringify({ success: true, data: SAMPLE_COURSES }));
      if (pathname.endsWith('instructors')) return res.end(JSON.stringify({ success: true, data: SAMPLE_INSTRUCTORS }));
      if (pathname.endsWith('testimonials')) return res.end(JSON.stringify(SAMPLE_TESTIMONIALS_RESPONSE));
      return res.end(JSON.stringify({ success: true, data: SAMPLE_SUPPORTERS }));
    }, 120);
  });

  const roDir = makeTempCacheDir();
  const concDir = makeTempCacheDir();
  try {
    // Missing dir -> degrades to no caching without error
    const missingRes = await famoHomeData({
      internalApiUrl: mock.url,
      cacheDir: path.join(os.tmpdir(), 'famo-nonexistent-cache-dir-xyz'),
    });
    assert.equal(missingRes.courses.length, 5);
    assert.equal(missingRes.testimonials.items.length, 2);

    // Read-only dir -> degrades to no caching without error
    fs.chmodSync(roDir, 0o555);
    const roRes = await famoHomeData({ internalApiUrl: mock.url, cacheDir: roDir });
    assert.equal(roRes.courses.length, 5);
    assert.equal(roRes.testimonials.items.length, 2);
    fs.chmodSync(roDir, 0o755);

    // Two concurrent requests on empty cache -> only 1 fetches; the 2nd returns all nulls immediately
    const logBefore = mock.log.length;
    const [rA, rB] = await Promise.all([
      famoHomeData({ internalApiUrl: mock.url, cacheDir: concDir, nowSec: 50000 }),
      famoHomeData({ internalApiUrl: mock.url, cacheDir: concDir, nowSec: 50000 }),
    ]);
    assert.equal(mock.log.length - logBefore, 4, 'Only one request should hit the API (4 endpoints)');
    const results = [rA, rB];
    assert.equal(results.filter((r) => r.courses !== null).length, 1);
    assert.equal(results.filter((r) => r.courses === null).length, 1);
  } finally {
    await mock.close();
    fs.chmodSync(roDir, 0o755);
    fs.rmSync(roDir, { recursive: true, force: true });
    fs.rmSync(concDir, { recursive: true, force: true });
  }
});

test('Testimonials SSR renderers & Schema.org builder output valid HTML, stars, tags, pagination, and real-only JSON-LD', () => {
  const item = SAMPLE_TESTIMONIALS_RESPONSE.data.items[0];
  const summary = SAMPLE_TESTIMONIALS_RESPONSE.data.summary;

  const excerptHtml = renderHomeTestimonialExcerpt(item);
  assert.match(excerptHtml, /class="famo-excerpt-item"/);
  assert.match(excerptHtml, /سارا احمدی/);
  assert.match(excerptHtml, /دانش‌آموز، دوازدهم تجربی ۱۴۰۴/);
  assert.match(excerptHtml, /class="famo-stars"/);
  assert.match(excerptHtml, /برنامه‌ریزی شخصی/);

  const summaryHtml = renderTestimonialSummary(summary);
  assert.match(summaryHtml, /۱۲/);
  assert.match(summaryHtml, /۴٫۸/);
  assert.match(summaryHtml, /famo-stars--summary/);

  const cardHtml = renderTestimonialCard(item, 'h4');
  assert.match(cardHtml, /famo-testi-card--featured/);
  assert.match(cardHtml, /<h4 class="famo-testi-name">سارا احمدی<\/h4>/);
  assert.match(cardHtml, /دوره: زیست‌شناسی کنکور/);
  assert.match(cardHtml, /استاد: دکتر مانی محسنی/);
  assert.equal((cardHtml.match(/famo-star--filled/g) || []).length, 5);

  const pagHtml = renderTestimonialsPagination(1, 3);
  assert.match(pagHtml, /صفحه ۱ از ۳/);
  assert.match(pagHtml, /data-page-dir="prev" disabled/);
  assert.match(pagHtml, /data-page-dir="next">/);
  assert.equal(renderTestimonialsPagination(1, 1), '');

  const schema = buildTestimonialsSchema(summary, SAMPLE_TESTIMONIALS_RESPONSE.data.items, 9);
  assert.equal(schema.aggregateRating['@type'], 'AggregateRating');
  assert.equal(schema.aggregateRating.ratingValue, 4.8);
  assert.equal(schema.aggregateRating.reviewCount, 12);
  assert.equal(schema.review.length, 2);
  assert.equal(schema.review[0]['@type'], 'Review');
  assert.equal(schema.review[0].author.name, 'سارا احمدی');
  assert.equal(schema.review[0].reviewRating.ratingValue, 5);
  assert.equal(schema.review[0].datePublished, '2026-11-20');

  // Never fabricate ratings/reviews when empty
  assert.deepEqual(buildTestimonialsSchema({ total_approved: 0, average_rating: 0 }, []), {});
});

test('Security: XSS tags, quotes, javascript:/data: URLs, and invalid hex colours are escaped or rejected', () => {
  assert.equal(safeUrl('javascript:alert(1)'), '');
  assert.equal(safeUrl('data:text/html,<script>alert(1)</script>'), '');
  assert.equal(safeUrl('//evil.example/x.js'), '');
  assert.equal(safeUrl('https://t.me/famoacademy'), 'https://t.me/famoacademy');
  assert.equal(safeUrl('/uploads/pic.webp'), '/uploads/pic.webp');

  assert.equal(resolveImageUrl('javascript:alert(1)'), '');
  assert.equal(resolveImageUrl('vbscript:msgbox(1)'), '');
  assert.equal(resolveImageUrl('data:image/svg+xml,<svg onload=alert(1)>'), '');
  assert.equal(resolveImageUrl('//evil.com/img.png'), '');
  assert.equal(resolveImageUrl('photo.webp'), 'https://api.famoacademy.ir/uploads/photo.webp');

  assert.equal(safeHex('#abc', '#445D84'), '#abc');
  assert.equal(safeHex('#1A2b3C', '#445D84'), '#1A2b3C');
  assert.equal(safeHex('red; background:url(javascript:alert(1))', '#445D84'), '#445D84');
  assert.equal(safeHex('#12345', '#445D84'), '#445D84');

  const evilCourseHtml = renderCourseSlide({
    id: '1" onclick="alert(1)',
    name: '<script>alert("xss")</script>',
    description: '"\'><img src=x onerror=alert(1)>',
    badge_label: '<b>bad</b>',
    target_grades: '<svg onload=1>',
    format: '"quote"',
    background_image_url: 'javascript:alert(1)',
    gradient_color_from: 'red;expression(1)',
    gradient_color_to: '#zzzzzz',
    icon: 'fa-flask',
  }, 0);

  assert.doesNotMatch(evilCourseHtml, /<script>/i);
  assert.doesNotMatch(evilCourseHtml, /javascript:/i);
  assert.doesNotMatch(evilCourseHtml, /onclick="/i);
  assert.doesNotMatch(evilCourseHtml, /<img[^>]*onerror=/i);
  assert.match(evilCourseHtml, /&lt;img src=x onerror=alert\(1\)&gt;/);
  assert.match(evilCourseHtml, /#445D84, #6D8B9E/);
  assert.match(evilCourseHtml, /class="course-image-fallback"/);
  assert.match(evilCourseHtml, /&lt;script&gt;/);

  const evilTeam = buildTeamList(
    [{
      name: '<script>alert(1)</script>',
      title: '" onclick="evil()',
      description: '<b>bio</b>',
      image_url: 'javascript:alert(1)',
      social_links: [
        { platform: 'telegram', url: 'javascript:alert(1)' },
        { platform: 'instagram', url: 'https://instagram.com/ok"onload="x' },
      ],
    }],
    [{ name: 'پشتیبان ایمن', field: '<script>x</script>' }],
  );
  const evilTeamHtml = renderTeamCards(evilTeam);
  const evilDotsHtml = renderTeamDots(evilTeam);

  assert.doesNotMatch(evilTeamHtml, /<script>/i);
  assert.doesNotMatch(evilTeamHtml, /javascript:/i);
  assert.match(evilTeamHtml, /class="inst-avatar inst-avatar-ph"/);
  assert.doesNotMatch(evilDotsHtml, /<script>/i);

  const evilTesti = {
    author_name: '<script>alert("t")</script>',
    author_role: 'student',
    author_meta: '" onmouseover="evil()',
    rating: 5,
    content: '<img src=x onerror=alert(1)>',
    course_name: '<svg onload=alert(1)>',
    instructor_name: '<b>teacher</b>',
    is_featured: true,
  };
  const evilExcerpt = renderHomeTestimonialExcerpt(evilTesti);
  const evilCard = renderTestimonialCard(evilTesti, 'h4');
  for (const html of [evilExcerpt, evilCard]) {
    assert.doesNotMatch(html, /<script>/i);
    assert.doesNotMatch(html, /<img[^>]*onerror=/i);
    assert.doesNotMatch(html, /onmouseover="/i);
    assert.match(html, /&lt;script&gt;/);
  }

  assert.equal(escHtml(`a&b<c>d"e'f`), 'a&amp;b&lt;c&gt;d&quot;e&#039;f');
});
