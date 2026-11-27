import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import http from 'node:http';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { getAfterPairs, evaluatePairs } from './contrast-audit.mjs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT = path.resolve(__dirname, '..');
const BASE_URL = process.env.TEST_BASE_URL || 'http://127.0.0.1:3000';

const CANONICAL_STATIC_PATHS = [
  '/',
  '/courses/',
  '/courses/math-babol/',
  '/courses/physics-babol/',
  '/courses/chemistry-babol/',
  '/courses/biology-babol/',
  '/courses/rafa-eshkal-babol/',
  '/courses/elementary-babol/',
  '/courses/middle-school-babol/',
  '/courses/high-school-babol/',
  '/courses/konkur-babol/',
  '/courses/tizhooshan-babol/',
  '/counseling-babol/',
  '/services/',
  '/services/smart-platform/',
  '/team/',
  '/results/',
  '/blog/',
  '/contact/',
  '/locations/amirkola/',
  '/locations/babolsar-mazandaran/',
];

function fetchRaw(urlPath) {
  return new Promise((resolve, reject) => {
    const req = http.get(`${BASE_URL}${urlPath}`, (res) => {
      const chunks = [];
      res.on('data', (c) => chunks.push(c));
      res.on('end', () => {
        resolve({
          status: res.statusCode,
          headers: res.headers,
          body: Buffer.concat(chunks).toString('utf8'),
        });
      });
    });
    req.on('error', reject);
  });
}

test('SEO Architecture: .htaccess has security blocks, loop-safe THE_REQUEST 301s, clean-URL rewrites, and no <Directory>', () => {
  const ht = fs.readFileSync(path.join(ROOT, '.htaccess'), 'utf8');
  assert.doesNotMatch(ht, /<Directory\b/i, '.htaccess must not contain <Directory>');
  assert.doesNotMatch(ht, /RewriteCond\s+%\{HTTPS\}\s+off/i, '.htaccess must not force HTTPS behind Caddy');
  assert.match(ht, /Options\s+-Indexes/i);
  assert.match(ht, /config\\\.php/i);
  assert.match(ht, /layouts/i);
  assert.match(ht, /THE_REQUEST.*index\\\.php/i);
  assert.match(ht, /THE_REQUEST.*pages\/course\\\.php/i);
  assert.match(ht, /THE_REQUEST.*pages\/post\\\.php/i);
  assert.match(ht, /pages\/landing\.php\?slug=/);
  assert.match(ht, /pages\/results\.php/);
  assert.match(ht, /ErrorDocument\s+404\s+\/404\.php/);
  assert.match(ht, /r\.php\?token=/);
});

test('SEO Architecture: sitemap.xml contains all 21 canonical clean URLs with lastmod, trailing slashes, and zero .php/?/# URLs', async () => {
  const xml = fs.readFileSync(path.join(ROOT, 'sitemap.xml'), 'utf8');
  const locs = [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
  const lastmods = [...xml.matchAll(/<lastmod>(\d{4}-\d{2}-\d{2})<\/lastmod>/g)].map((m) => m[1]);
  assert.equal(locs.length, 21, `Expected 21 canonical URLs in sitemap.xml, got ${locs.length}`);
  assert.equal(lastmods.length, 21, `Expected 21 <lastmod> tags in sitemap.xml, got ${lastmods.length}`);
  assert.deepEqual(
    locs,
    CANONICAL_STATIC_PATHS.map((p) => `https://famoacademy.ir${p}`),
  );
  for (const loc of locs) {
    assert.ok(loc.endsWith('/'), `Sitemap URL missing trailing slash: ${loc}`);
    assert.doesNotMatch(loc, /\.php/);
    assert.doesNotMatch(loc, /\?/);
    assert.doesNotMatch(loc, /#/);
  }

  const smRes = await fetchRaw('/sitemap.xml');
  assert.equal(smRes.status, 200);
  assert.match(String(smRes.headers['cache-control'] || ''), /max-age=300/);

  const blogSmRes = await fetchRaw('/sitemap-blog.xml');
  assert.equal(blogSmRes.status, 200);
  assert.match(String(blogSmRes.headers['content-type'] || ''), /application\/xml/);
});

test('Phase 0: all 4 service images are optimized (< 130 KB WebP) with matching AVIF variants', () => {
  const names = ['24support', 'private_classes', 'weekly_exams', 'weekly_plans'];
  for (const name of names) {
    const webpPath = path.join(ROOT, 'assets/images/services', `${name}.webp`);
    const avifPath = path.join(ROOT, 'assets/images/services', `${name}.avif`);
    assert.ok(fs.existsSync(webpPath), `Missing ${webpPath}`);
    assert.ok(fs.existsSync(avifPath), `Missing ${avifPath}`);
    const webpSize = fs.statSync(webpPath).size;
    const avifSize = fs.statSync(avifPath).size;
    assert.ok(webpSize < 130_000, `${name}.webp too large: ${webpSize} bytes`);
    assert.ok(avifSize > 5_000 && avifSize < webpSize, `${name}.avif unexpected size: ${avifSize} vs webp ${webpSize}`);
  }
});

test('Truthfulness & banned claims check across core and SEO landing files', () => {
  const files = [
    'index.php',
    '404.php',
    'layouts/head-common.php',
    'layouts/header.php',
    'layouts/footer.php',
    'layouts/seo-pages.json',
    'pages/landing.php',
    'pages/course.php',
    'pages/services.php',
    'pages/team.php',
    'pages/results.php',
    'pages/contact.php',
    'assets/js/main.js',
  ];
  const bannedPatterns = [
    /تضمین\s+موفقیت/,
    /صددرصد/,
    /۱۰۰٪/,
    /100%\s*(تضمین|قبولی|موفقیت)/,
    /اساتید\s+برجسته/,
    /اساتید\s+برتر/,
    /years-counter/,
    /students-counter/,
    /teachers-counter/,
    /۷\s+سال/,
    /7\s+سال/,
    /href="#"/,
  ];
  for (const rel of files) {
    const content = fs.readFileSync(path.join(ROOT, rel), 'utf8');
    for (const pattern of bannedPatterns) {
      assert.doesNotMatch(content, pattern, `Banned pattern ${pattern} found in ${rel}`);
    }
  }
});

test('HTTP Security & 301 Redirects: legacy .php URLs, trailing-slash normalization, 403 hardening, and 404', async () => {
  const rConfig = await fetchRaw('/config.php');
  assert.equal(rConfig.status, 403);

  const rLayout = await fetchRaw('/layouts/header.php');
  assert.equal(rLayout.status, 403);

  const redirectCases = [
    ['/index.php', '/'],
    ['/pages/course.php', '/courses/'],
    ['/pages/services.php', '/services/'],
    ['/pages/team.php', '/team/'],
    ['/pages/admissions.php', '/results/'],
    ['/pages/testimonials.php', '/results/'],
    ['/pages/results.php', '/results/'],
    ['/pages/contact.php', '/contact/'],
    ['/pages/blog.php', '/blog/'],
    ['/pages/blog.php?category=planning', '/blog/?category=planning'],
    ['/pages/post.php?slug=exam-stress-and-sleep', '/blog/exam-stress-and-sleep/'],
    ['/pages/post.php', '/blog/'],
    ['/courses', '/courses/'],
    ['/courses/math-babol', '/courses/math-babol/'],
    ['/counseling-babol', '/counseling-babol/'],
    ['/services/smart-platform', '/services/smart-platform/'],
    ['/locations/amirkola', '/locations/amirkola/'],
    ['/results', '/results/'],
    ['/team', '/team/'],
    ['/blog/exam-stress-and-sleep', '/blog/exam-stress-and-sleep/'],
  ];

  for (const [fromPath, expectedLoc] of redirectCases) {
    const res = await fetchRaw(fromPath);
    assert.equal(res.status, 301, `${fromPath} expected 301, got ${res.status}`);
    assert.equal(res.headers.location, expectedLoc, `${fromPath} location mismatch`);
  }

  const r404 = await fetchRaw('/non-existent-page-xyz');
  assert.equal(r404.status, 404);
  assert.match(r404.body, /noindex,\s*follow/i);
  assert.doesNotMatch(r404.body, /<\?(=|php)/);
});

test('HTTP Clean URLs: all 21 sitemap URLs + dynamic blog route return 200, zero PHP leaks, single H1, exact canonical, valid JSON-LD, and zero legacy .php links', async () => {
  const routes = [
    ...CANONICAL_STATIC_PATHS.map((p) => ({ path: p, canonicalSuffix: p })),
    { path: '/blog/?category=math', canonicalSuffix: '/blog/' },
    { path: '/blog/exam-stress-and-sleep/', canonicalSuffix: '/blog/exam-stress-and-sleep/' },
  ];

  for (const c of routes) {
    const res = await fetchRaw(c.path);
    assert.equal(res.status, 200, `${c.path} returned ${res.status}`);
    assert.doesNotMatch(res.body, /<\?(=|php)/, `PHP tag leaked in ${c.path}`);
    assert.doesNotMatch(res.body, /assets\.famoacademy\.ir/, `External assets subdomain found in ${c.path}`);
    assert.doesNotMatch(res.body, /href=["'][^"']*pages\/[a-z]+\.php/i, `Legacy pages/*.php internal link found in ${c.path}`);

    const canonMatch = res.body.match(/<link rel="canonical" href="([^"]+)">/);
    assert.ok(canonMatch, `Missing canonical link in ${c.path}`);
    const canonHref = canonMatch[1];
    assert.ok(!canonHref.includes('?'), `Canonical must not contain query string in ${c.path}: ${canonHref}`);
    assert.ok(
      canonHref.endsWith(c.canonicalSuffix),
      `Canonical mismatch in ${c.path}: expected suffix ${c.canonicalSuffix}, got ${canonHref}`,
    );

    const h1Count = (res.body.match(/<h1\b/gi) || []).length;
    assert.equal(h1Count, 1, `Expected exactly 1 <h1> on ${c.path}, got ${h1Count}`);

    const ldScripts = [...res.body.matchAll(/<script[^>]+type="application\/ld\+json"[^>]*>([\s\S]*?)<\/script>/gi)];
    assert.ok(ldScripts.length >= 1, `Missing JSON-LD on ${c.path}`);
    for (const m of ldScripts) {
      assert.doesNotThrow(() => JSON.parse(m[1]), `Invalid JSON-LD on ${c.path}`);
    }
  }
});

test('Crawlable SSR / Fail-soft Fallbacks: /courses/, /team/, /results/, /blog/ never emit API error copy', async () => {
  for (const route of ['/courses/', '/team/', '/results/', '/blog/']) {
    const res = await fetchRaw(route);
    assert.equal(res.status, 200);
    assert.doesNotMatch(res.body, /خطا در بارگذاری/, `Error message found in initial HTML of ${route}`);
  }

  const coursesRes = await fetchRaw('/courses/');
  assert.match(coursesRes.body, /href="\/courses\/math-babol\/"/);
  assert.match(coursesRes.body, /href="\/courses\/rafa-eshkal-babol\/"/);
  assert.match(coursesRes.body, /id="coursesContainer"/);

  const teamRes = await fetchRaw('/team/');
  assert.match(teamRes.body, /id="instructorsContainer"/);
  assert.match(teamRes.body, /id="supportersContainer"/);

  const resultsRes = await fetchRaw('/results/');
  assert.match(resultsRes.body, /id="admissionsContainer"/);
  assert.match(resultsRes.body, /id="testimonialsList"/);
  assert.match(resultsRes.body, /id="testimonialForm"/);
});

test('Phase 0/1 HTTP: AVIF & WebP service assets serve with correct MIME types', async () => {
  const avif = await fetchRaw('/assets/images/services/private_classes.avif');
  assert.equal(avif.status, 200);
  assert.equal(avif.headers['content-type'], 'image/avif');

  const webp = await fetchRaw('/assets/images/services/private_classes.webp');
  assert.equal(webp.status, 200);
  assert.equal(webp.headers['content-type'], 'image/webp');
});

test('Home declutter: section order, preserved JS IDs, clean-URL course/subject chips, and removed heavy sections on /', async () => {
  const res = await fetchRaw('/');
  assert.equal(res.status, 200);
  const html = res.body;

  const expectedOrder = [
    'id="home"',
    'id="team"',
    'id="about-summary"',
    'id="courses"',
    'id="achievements-testimonials"',
    'id="faq-contact"',
    'id="cta-final"',
  ];
  let prevIdx = -1;
  for (const marker of expectedOrder) {
    const idx = html.indexOf(marker);
    assert.ok(idx > prevIdx, `Expected ${marker} to appear after previous section (idx=${idx}, prev=${prevIdx})`);
    prevIdx = idx;
  }

  for (const removed of ['id="advantages"', 'id="process"', 'id="smart-platform"', 'id="services"', 'famo-programs-grid']) {
    assert.ok(!html.includes(removed), `Removed homepage section/grid still present on /: ${removed}`);
  }

  for (const jsId of [
    'id="instructorsContainer"',
    'id="instCarouselPrev"',
    'id="instCarouselNext"',
    'id="instCarouselDots"',
    'id="coursesContainer"',
    'id="homeAdmissionsExcerpt"',
    'id="homeTestimonialsExcerpt"',
  ]) {
    assert.ok(html.includes(jsId), `Required JS hook missing on /: ${jsId}`);
  }

  for (const cleanHref of [
    'href="/courses/elementary-babol/"',
    'href="/courses/middle-school-babol/"',
    'href="/courses/high-school-babol/"',
    'href="/courses/konkur-babol/"',
    'href="/courses/tizhooshan-babol/"',
    'href="/courses/math-babol/"',
    'href="/courses/physics-babol/"',
    'href="/courses/chemistry-babol/"',
    'href="/courses/biology-babol/"',
    'href="/courses/rafa-eshkal-babol/"',
    'href="/counseling-babol/"',
    'href="/services/smart-platform/"',
    'href="/services/#process"',
    'href="/results/"',
    'href="/results/#testimonials"',
  ]) {
    assert.ok(html.includes(cleanHref), `Missing expected clean link on /: ${cleanHref}`);
  }

  assert.doesNotMatch(html, /href="#smart-platform"/);
  assert.doesNotMatch(html, /pages\/course\.php#/);
});

test('Home declutter: migrated sections (#advantages, #process, #smart-platform) live cleanly on /services/', async () => {
  const res = await fetchRaw('/services/');
  assert.equal(res.status, 200);
  const html = res.body;

  for (const anchorId of [
    'id="advantages"',
    'id="private-classes"',
    'id="personalized-exams"',
    'id="tutoring"',
    'id="counseling"',
    'id="performance-tracking"',
    'id="weekly-report"',
    'id="process"',
    'id="smart-platform"',
  ]) {
    assert.ok(html.includes(anchorId), `Missing expected anchor ${anchorId} on /services/`);
  }

  assert.ok(!html.includes('id="services"'), 'services.php must not duplicate homepage #services grid');
  assert.doesNotMatch(html, /<\?(=|php)/, 'PHP tag leaked in /services/');
});

test('WCAG AA Contrast Audit: all audited AFTER color pairs pass minimum ratios', () => {
  const results = evaluatePairs(getAfterPairs());
  const failed = results.filter((r) => !r.pass);
  assert.equal(
    failed.length,
    0,
    `WCAG AA contrast failures:\n${failed.map((f) => `- ${f.id}: ${f.ratio}:1 < ${f.min}:1 (${f.fg} on ${f.worstBg})`).join('\n')}`,
  );
});

test('Local assets: all required shared assets exist locally and zero external assets.famoacademy.ir refs remain', () => {
  const requiredFiles = [
    'assets/css/tokens.css',
    'assets/css/output.css',
    'assets/css/libs/swiper-bundle.min.css',
    'assets/fonts/Vazirmatn-Regular.woff2',
    'assets/fonts/Vazirmatn-Regular.ttf',
    'assets/fonts/Vazirmatn-Medium.woff2',
    'assets/fonts/Vazirmatn-Medium.ttf',
    'assets/fonts/Vazirmatn-Bold.woff2',
    'assets/fonts/Vazirmatn-Bold.ttf',
    'assets/images/logo.png',
    'assets/images/logo.webp',
    'assets/images/favicon-16.png',
    'assets/images/favicon-32.png',
    'assets/images/favicon-96.png',
    'assets/js/api.js',
    'assets/js/lucide-adapter.js',
    'assets/js/libs/gsap.min.js',
    'assets/js/libs/ScrollTrigger.min.js',
    'assets/js/libs/ScrollToPlugin.min.js',
    'assets/js/libs/swiper-bundle.min.js',
    'assets/js/libs/lucide.min.js',
  ];
  for (const rel of requiredFiles) {
    const p = path.join(ROOT, rel);
    assert.ok(fs.existsSync(p) && fs.statSync(p).size > 0, `Missing or empty local asset: ${rel}`);
  }
  for (const w of ['Regular', 'Medium', 'Bold']) {
    const hdr = fs.readFileSync(path.join(ROOT, `assets/fonts/Vazirmatn-${w}.woff2`)).subarray(0, 4).toString('ascii');
    assert.equal(hdr, 'wOF2', `Vazirmatn-${w}.woff2 must have valid wOF2 header`);
  }

  const appFiles = [
    'config.php',
    'index.php',
    '404.php',
    'r.php',
    'layouts/head-common.php',
    'layouts/header.php',
    'layouts/footer.php',
    'pages/landing.php',
    'pages/results.php',
    'assets/js/main.js',
    'assets/js/courses.js',
    'assets/js/team.js',
    'assets/js/blog.js',
  ];
  for (const rel of appFiles) {
    const content = fs.readFileSync(path.join(ROOT, rel), 'utf8');
    assert.doesNotMatch(content, /assets\.famoacademy\.ir/, `External assets subdomain still referenced in ${rel}`);
    assert.doesNotMatch(content, /\.\.\/shared\//, `Legacy ../shared/ path still referenced in ${rel}`);
    assert.doesNotMatch(content, /\.subset\.woff2/, `Missing .subset.woff2 still referenced in ${rel}`);
  }
});

test('Local assets HTTP: serve local WOFF2/CSS/JS/images with 200 and expected MIME types', async () => {
  const mimeChecks = [
    ['/assets/fonts/Vazirmatn-Regular.woff2', 'font/woff2'],
    ['/assets/fonts/Vazirmatn-Bold.woff2', 'font/woff2'],
    ['/assets/fonts/Vazirmatn-Regular.ttf', 'font/ttf'],
    ['/assets/css/tokens.css', 'text/css'],
    ['/assets/css/output.css', 'text/css'],
    ['/assets/css/libs/swiper-bundle.min.css', 'text/css'],
    ['/assets/js/api.js', 'application/javascript'],
    ['/assets/js/lucide-adapter.js', 'application/javascript'],
    ['/assets/js/libs/lucide.min.js', 'application/javascript'],
    ['/assets/images/logo.webp', 'image/webp'],
    ['/assets/images/logo.png', 'image/png'],
    ['/assets/images/favicon-32.png', 'image/png'],
  ];
  for (const [assetPath, expectedMime] of mimeChecks) {
    const res = await fetchRaw(assetPath);
    assert.equal(res.status, 200, `${assetPath} returned ${res.status}`);
    assert.ok(
      String(res.headers['content-type'] || '').includes(expectedMime),
      `${assetPath} content-type mismatch: ${res.headers['content-type']}`,
    );
  }
});
