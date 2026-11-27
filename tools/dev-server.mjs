import fs from 'node:fs';
import http from 'node:http';
import https from 'node:https';
import net from 'node:net';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import {
  famoHomeData,
  famoTestimonialsPageData,
  renderCourseSlide,
  renderCourseGridCard,
  normalizeInstructor,
  normalizeSupporter,
  buildTeamList,
  renderPersonCard,
  renderTeamCards,
  renderTeamDots,
  renderHomeTestimonialExcerpt,
  renderTestimonialSummary,
  renderTestimonialCard,
  renderTestimonialsPagination,
  buildTestimonialsSchema,
} from './home-ssr-preview.mjs';

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
  return '/assets';
}

function famoAppendVersion(url, version) {
  if (!version) return url;
  const sep = url.includes('?') ? '&' : '?';
  return `${url}${sep}v=${encodeURIComponent(version)}`;
}

function famoAsset(assetPath) {
  const clean = String(assetPath).replace(/^\/+/, '');
  const rel = clean.startsWith('assets/') ? clean : `assets/${clean}`;
  return famoLocalAsset(rel, '/');
}

function famoLocalAsset(relPath, base = '/') {
  const cleanRel = String(relPath).replace(/^\/+/, '');
  const fullPath = path.join(ROOT_DIR, cleanRel);
  const url = `${base}${cleanRel}`;
  try {
    const stat = fs.statSync(fullPath);
    return famoAppendVersion(url, String(Math.floor(stat.mtimeMs / 1000)));
  } catch {
    return url;
  }
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
  '.avif': 'image/avif',
  '.ico': 'image/x-icon',
  '.xml': 'application/xml; charset=utf-8',
  '.txt': 'text/plain; charset=utf-8',
  '.html': 'text/html; charset=utf-8',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
};

function loadSeoPagesMap() {
  const jsonPath = path.join(ROOT_DIR, 'layouts', 'seo-pages.json');
  try {
    const parsed = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));
    const map = new Map();
    if (Array.isArray(parsed)) {
      for (const item of parsed) {
        if (item && typeof item.slug === 'string' && item.slug.trim()) {
          map.set(item.slug.trim().replace(/^\/+|\/+$/g, ''), item);
        }
      }
    } else if (parsed && typeof parsed === 'object') {
      for (const [key, item] of Object.entries(parsed)) {
        if (item && typeof item === 'object') {
          const slug = String(item.slug || key).trim().replace(/^\/+|\/+$/g, '');
          if (slug) map.set(slug, { ...item, slug });
        }
      }
    }
    return map;
  } catch {
    return new Map();
  }
}

function parsePageLibsFromPreamble(preamble) {
  const libs = {
    gsap: true,
    scroll_trigger: true,
    scroll_to: true,
    swiper: true,
    lucide: true,
    animation: true,
  };
  const m = preamble.match(/\$page_libs\s*=\s*\[([\s\S]*?)\];/);
  if (!m) return libs;
  const entryRegex = /'([a-z_]+)'\s*=>\s*(true|false)/g;
  let em;
  while ((em = entryRegex.exec(m[1])) !== null) {
    libs[em[1]] = em[2] === 'true';
  }
  return libs;
}

function evalLibsConditionals(html, libs) {
  let out = html;
  let prev;
  const leafIf = /<\?php\s+if\s*\(!empty\(\$libs\['([a-z_]+)'\]\)\):\s*\?>((?:(?!<\?php\s+if\b)[\s\S])*?)<\?php\s+endif;\s*\?>/g;
  do {
    prev = out;
    out = out.replace(leafIf, (_m, key, body) => (libs[key] ? body : ''));
  } while (out !== prev);
  return out;
}

function famoPublicSiteUrl() {
  const url = famoEnv('PUBLIC_URL', '').replace(/\/+$/, '');
  return url || 'https://famoacademy.ir';
}

function famoCanonicalUrl(relPath = '') {
  const siteUrl = famoPublicSiteUrl();
  const clean = String(relPath).replace(/^\/+/, '');
  return clean === '' ? `${siteUrl}/` : `${siteUrl}/${clean}`;
}

function parsePhpStringVar(preamble, varName) {
  const m = preamble.match(new RegExp(`\\$${varName}\\s*=\\s*'((?:\\\\'|[^'])*)'\\s*;`));
  return m ? m[1].replace(/\\'/g, "'") : '';
}

function escapeHtml(val) {
  return String(val ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function buildOrgAndWebsite(siteUrl, ogImage) {
  const orgId = `${siteUrl}/#organization`;
  const websiteId = `${siteUrl}/#website`;
  return {
    orgId,
    websiteId,
    org: {
      '@type': ['EducationalOrganization', 'Organization'],
      '@id': orgId,
      name: 'آموزشگاه فامو',
      alternateName: 'Famo Academy',
      url: `${siteUrl}/`,
      logo: { '@type': 'ImageObject', url: `${siteUrl}/assets/images/logo.png` },
      image: ogImage,
      email: 'info@famoacademy.ir',
      telephone: '+98-901-440-2300',
      address: {
        '@type': 'PostalAddress',
        addressLocality: 'بابل',
        addressRegion: 'مازندران',
        addressCountry: 'IR',
        streetAddress: 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
      },
      sameAs: ['https://t.me/famoacademy'],
    },
    website: {
      '@type': 'WebSite',
      '@id': websiteId,
      url: `${siteUrl}/`,
      name: 'آموزشگاه فامو',
      inLanguage: 'fa-IR',
      publisher: { '@id': orgId },
    },
  };
}

function buildPreviewGraph(relPath, seo, extraSchemas = [], customCrumbs = null, organizationExtra = null) {
  const siteUrl = famoPublicSiteUrl();
  const { orgId, websiteId, org, website } = buildOrgAndWebsite(siteUrl, seo.ogImage);
  if (organizationExtra && typeof organizationExtra === 'object') {
    Object.assign(org, organizationExtra);
  }
  const webpageId = `${seo.canonical}#webpage`;
  const breadcrumbId = `${seo.canonical}#breadcrumb`;

  let crumbs;
  if (Array.isArray(customCrumbs) && customCrumbs.length > 0) {
    crumbs = customCrumbs.map((c, idx) => ({
      '@type': 'ListItem',
      position: idx + 1,
      name: c.name,
      item: c.url,
    }));
  } else {
    crumbs = [{ '@type': 'ListItem', position: 1, name: 'خانه', item: famoCanonicalUrl('') }];
    const pageLabels = {
      'pages/course.php': 'دوره‌های آموزشی',
      'pages/services.php': 'خدمات آموزشی فامو',
      'pages/team.php': 'اساتید و پشتیبانان',
      'pages/results.php': 'نتایج، قبولی‌ها و نظرات',
      'pages/blog.php': 'وبلاگ و مقالات',
      'pages/post.php': 'وبلاگ و مقالات',
      'pages/contact.php': 'تماس با ما و درخواست مشاوره',
    };
    if (pageLabels[relPath]) {
      crumbs.push({
        '@type': 'ListItem',
        position: 2,
        name: pageLabels[relPath],
        item: relPath === 'pages/post.php' ? famoCanonicalUrl('blog/') : seo.canonical,
      });
    }
  }

  const webpage = {
    '@type': seo.webPageType,
    '@id': webpageId,
    url: seo.canonical,
    name: seo.title,
    description: seo.description,
    inLanguage: 'fa-IR',
    isPartOf: { '@id': websiteId },
    about: { '@id': orgId },
    breadcrumb: { '@id': breadcrumbId },
  };

  const graph = [
    org,
    website,
    webpage,
    { '@type': 'BreadcrumbList', '@id': breadcrumbId, itemListElement: crumbs },
  ];

  if (relPath === 'index.php') {
    graph.push({
      '@type': 'FAQPage',
      '@id': `${seo.canonical}#faq`,
      inLanguage: 'fa-IR',
      mainEntity: [
        { '@type': 'Question', name: 'دوره‌ها و کلاس‌های آموزشگاه فامو حضوری است یا آنلاین؟', acceptedAnswer: { '@type': 'Answer', text: 'کلاس‌ها و جلسات آموزشی فامو به‌صورت حضوری در محل آموزشگاه در بابل (میدان باغ فردوس) برگزار می‌شوند.' } },
        { '@type': 'Question', name: 'آیا برای هر پایه و رشته، آزمون و برنامه جداگانه طراحی می‌شود؟', acceptedAnswer: { '@type': 'Answer', text: 'بله؛ آزمون‌ها و برنامه‌های هفتگی بر اساس پایه تحصیلی، رشته، بودجه‌بندی و وضعیت درسی هر دانش‌آموز تنظیم می‌شوند.' } },
        { '@type': 'Question', name: 'پایش عملکرد و گزارش وضعیت دانش‌آموز چگونه به خانواده اطلاع داده می‌شود؟', acceptedAnswer: { '@type': 'Answer', text: 'پس از هر آزمون، کارنامه تحلیلی و گزارش روند عملکرد در سامانه هوشمند فامو ثبت می‌شود و در دسترس دانش‌آموز و خانواده قرار می‌گیرد.' } },
      ],
    });
  }

  if (relPath === 'pages/services.php') {
    graph.push({
      '@type': 'ItemList',
      '@id': `${seo.canonical}#services`,
      name: 'خدمات آموزشی آموزشگاه فامو بابل',
      itemListElement: [
        { '@type': 'ListItem', position: 1, item: { '@type': 'Service', name: 'کلاس خصوصی و تقویتی' } },
        { '@type': 'ListItem', position: 2, item: { '@type': 'Service', name: 'آزمون شخصی‌سازی‌شده' } },
        { '@type': 'ListItem', position: 3, item: { '@type': 'Service', name: 'رفع اشکال انفرادی' } },
        { '@type': 'ListItem', position: 4, item: { '@type': 'Service', name: 'مشاوره و برنامه‌ریزی تحصیلی' } },
        { '@type': 'ListItem', position: 5, item: { '@type': 'Service', name: 'پایش عملکرد و گزارش مستمر' } },
        { '@type': 'ListItem', position: 6, item: { '@type': 'Service', name: 'کارنامه هفتگی تحلیلی' } },
      ],
    });
  }

  for (const extra of extraSchemas) {
    graph.push(extra);
  }

  return JSON.stringify({ '@context': 'https://schema.org', '@graph': graph });
}

const COURSE_HUB_SUBJECT_LANDINGS = [
  {
    name: 'آموزشگاه و کلاس خصوصی ریاضی بابل',
    desc: 'تدریس مفهومی ریاضی، حسابان، هندسه و آمار از پایه چهارم تا دوازدهم و کنکور با آزمون مبحثی.',
    level: 'ابتدایی تا کنکور',
    url: '/courses/math-babol/',
    icon: 'chart-column',
  },
  {
    name: 'کلاس خصوصی فیزیک بابل',
    desc: 'آموزش شهودی و حل مسائل تشریحی امتحان نهایی و تست‌های کنکور فیزیک دهم تا دوازدهم.',
    level: 'متوسطه دوم و کنکور',
    url: '/courses/physics-babol/',
    icon: 'star',
  },
  {
    name: 'کلاس خصوصی شیمی بابل',
    desc: 'تسلط هم‌زمان بر مسائل استوکیومتری، شیمی آلی و متن کتاب درسی ویژه نهایی و کنکور.',
    level: 'متوسطه دوم و کنکور',
    url: '/courses/chemistry-babol/',
    icon: 'file-chart-column',
  },
  {
    name: 'کلاس خصوصی زیست بابل',
    desc: 'تحلیل خط‌به‌خط کتاب درسی، شکل‌ها و تست‌های ترکیبی زیست‌شناسی رشته تجربی.',
    level: 'دهم تا دوازدهم تجربی',
    url: '/courses/biology-babol/',
    icon: 'book-open',
  },
  {
    name: 'کلاس رفع اشکال بابل',
    desc: 'جلسات انفرادی رفع اشکال ریاضی و دروس تخصصی پیش از امتحانات مدرسه، نهایی و آزمون‌ها.',
    level: 'تمام مقاطع تحصیلی',
    url: '/courses/rafa-eshkal-babol/',
    icon: 'circle-help',
  },
  {
    name: 'مشاوره تحصیلی و کنکور بابل',
    desc: 'برنامه‌ریزی فردی، انتخاب منابع، تحلیل آزمون و پایش هفتگی وضعیت تحصیلی دانش‌آموز.',
    level: 'متوسطه، تیزهوشان و کنکور',
    url: '/counseling-babol/',
    icon: 'compass',
  },
];

const COURSE_HUB_GRADE_PROGRAMS = [
  {
    badge: 'مقطع ابتدایی',
    title: 'کلاس تقویتی ابتدایی بابل (چهارم، پنجم و ششم)',
    desc: 'تقویت مفاهیم پایه ریاضی و علوم، تمرین حل مسئله و رفع اشکال انفرادی با ریتم متناسب با یادگیری دانش‌آموز.',
    points: ['تقویت مفاهیم پایه درسی', 'رفع اشکال مباحث', 'افزایش تسلط و دقت', 'آماده‌سازی تدریجی مسیر تیزهوشان'],
    url: '/courses/elementary-babol/',
    cta: 'صفحه دوره ابتدایی',
  },
  {
    badge: 'متوسطه اول',
    title: 'کلاس تقویتی متوسطه اول بابل (هفتم، هشتم و نهم)',
    desc: 'تثبیت پایه‌های ریاضی و علوم، آموزش مفهومی و شناسایی نقاط ضعف پیش از هدایت تحصیلی و ورود به دبیرستان.',
    points: ['تقویت پایه‌های درسی', 'آموزش مفهومی ریاضی و علوم', 'رفع اشکال تخصصی', 'آمادگی آزمون‌های ورودی مدارس'],
    url: '/courses/middle-school-babol/',
    cta: 'صفحه دوره متوسطه اول',
  },
  {
    badge: 'متوسطه دوم',
    title: 'کلاس تقویتی متوسطه دوم بابل (دهم، یازدهم و دوازدهم)',
    desc: 'آموزش دروس تخصصی و عمومی با تمرکز هم‌زمان بر نمرات امتحانات نهایی (سوابق تحصیلی) و زیربنای کنکور.',
    points: ['آمادگی امتحان نهایی', 'آموزش دروس تخصصی و عمومی', 'رفع اشکال موضوعی', 'پایش هفتگی روند نمرات'],
    url: '/courses/high-school-babol/',
    cta: 'صفحه دوره متوسطه دوم',
  },
  {
    badge: 'کنکور سراسری',
    title: 'آموزشگاه کنکور بابل (تجربی، ریاضی و انسانی)',
    desc: 'کلاس خصوصی دروس کنکور، آزمون‌های شخصی‌سازی‌شده، تحلیل درصدها و گزارش هفتگی وضعیت آموزشی داوطلبان.',
    points: ['کلاس خصوصی دروس کنکور', 'آزمون شخصی‌سازی‌شده', 'تحلیل نتایج و درصدها', 'گزارش هفتگی پیامکی به اولیا'],
    url: '/courses/konkur-babol/',
    cta: 'صفحه آمادگی کنکور',
  },
  {
    badge: 'مدارس استعدادهای درخشان',
    title: 'آموزشگاه تیزهوشان بابل (شهید بهشتی و فرزانگان)',
    desc: 'تقویت استدلال تحلیلی، سرعت و دقت در مسائل هوش و تسلط بر دروس استعداد تحصیلی ویژه ششم و نهم.',
    points: ['هوش تحلیلی و ریاضی-منطقی', 'تمرین سرعت و دقت', 'استعداد تحصیلی نهم به دهم', 'آزمون‌های شبیه‌ساز زمان‌دار'],
    url: '/courses/tizhooshan-babol/',
    cta: 'صفحه آمادگی تیزهوشان',
  },
];

function buildPreviewCourseSchema() {
  const siteUrl = famoPublicSiteUrl();
  const pageUrl = famoCanonicalUrl('courses/');
  const orgRef = {
    '@type': 'EducationalOrganization',
    '@id': `${siteUrl}/#organization`,
    name: 'آموزشگاه فامو',
    url: `${siteUrl}/`,
  };
  let pos = 1;
  const items = [
    ...COURSE_HUB_GRADE_PROGRAMS.map((gp) => ({
      '@type': 'ListItem',
      position: pos++,
      item: {
        '@type': 'Course',
        name: gp.title,
        description: gp.desc,
        url: famoCanonicalUrl(gp.url.replace(/^\/+/, '')),
        inLanguage: 'fa-IR',
        provider: orgRef,
      },
    })),
    ...COURSE_HUB_SUBJECT_LANDINGS.map((sl) => ({
      '@type': 'ListItem',
      position: pos++,
      item: {
        '@type': 'Course',
        name: sl.name,
        description: sl.desc,
        url: famoCanonicalUrl(sl.url.replace(/^\/+/, '')),
        inLanguage: 'fa-IR',
        educationalLevel: sl.level,
        provider: orgRef,
      },
    })),
  ];
  return JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    '@id': `${pageUrl}#courses`,
    name: 'دوره‌های آموزشی و تقویتی آموزشگاه فامو بابل',
    itemListElement: items,
  });
}

function applyCommonReplacements(content, { relPath, base, seo, extraSchemas = [], customCrumbs = null, organizationExtra = null }) {
  const loginBase = famoEnv('LOGIN_URL', 'https://auth.famoacademy.ir').replace(/\/+$/, '');
  const loginUrl = `${loginBase}/`;
  const homeHref = base === '' ? './' : base;

  return content
    .replace(/<\?=\s*famo_config_script\(\)\s*\?>/g, famoConfigScript())
    .replace(/<\?=\s*\$jsonLdGraph\s*\?>/g, buildPreviewGraph(relPath, seo, extraSchemas, customCrumbs, organizationExtra))
    .replace(/<\?=\s*\$courseSchemaJson\s*\?>/g, buildPreviewCourseSchema())
    .replace(/<\?=\s*\$homeHref\s*\?>/g, homeHref)
    .replace(/<\?=\s*\$base\s*===\s*''\s*\?\s*'\.\/'\s*:\s*\$baseEsc\s*\?>/g, homeHref)
    .replace(/<\?=\s*htmlspecialchars\(\$seoTitle[^)]+\)\s*\?>/g, escapeHtml(seo.title))
    .replace(/<\?=\s*htmlspecialchars\(\$seoDescription[^)]+\)\s*\?>/g, escapeHtml(seo.description))
    .replace(/<\?=\s*htmlspecialchars\(\$seoCanonical[^)]+\)\s*\?>/g, escapeHtml(seo.canonical))
    .replace(/<\?=\s*htmlspecialchars\(\$seoRobots[^)]+\)\s*\?>/g, escapeHtml(seo.robots))
    .replace(/<\?=\s*htmlspecialchars\(\$seoOgType[^)]+\)\s*\?>/g, escapeHtml(seo.ogType))
    .replace(/<\?=\s*htmlspecialchars\(\$seoOgImage[^)]+\)\s*\?>/g, escapeHtml(seo.ogImage))
    .replace(/<\?=\s*htmlspecialchars\(\$seoOgImageAlt[^)]+\)\s*\?>/g, escapeHtml(seo.ogImageAlt))
    .replace(/<\?=\s*\$fontRegularWoff2\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Regular.woff2', base)))
    .replace(/<\?=\s*\$fontRegularTtf\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Regular.ttf', base)))
    .replace(/<\?=\s*\$fontMediumWoff2\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Medium.woff2', base)))
    .replace(/<\?=\s*\$fontMediumTtf\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Medium.ttf', base)))
    .replace(/<\?=\s*\$fontBoldWoff2\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Bold.woff2', base)))
    .replace(/<\?=\s*\$fontBoldTtf\s*\?>/g, escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Bold.ttf', base)))
    .replace(/<\?=\s*htmlspecialchars\(\$pageTitle[^)]+\)\s*\?>/g, escapeHtml(seo.title))
    .replace(/<\?=\s*htmlspecialchars\(\$metaDescription[^)]+\)\s*\?>/g, escapeHtml(seo.description))
    .replace(/<\?=\s*htmlspecialchars\(\$base,\s*ENT_QUOTES,\s*'UTF-8'\)\s*\?>/g, base)
    .replace(/<\?=\s*\$baseEsc\s*\?>/g, base)
    .replace(/<\?=\s*\$loginUrlEsc\s*\?>/g, loginUrl)
    .replace(/<\?=\s*htmlspecialchars\(famo_login_url\(\)\s*\.\s*'\/'[^)]+\)\s*\?>/g, loginUrl)
    .replace(/<\?=\s*json_encode\(famo_public_url\(\)[^)]+\)\s*\?>/g, JSON.stringify(famoEnv('PUBLIC_URL', '').replace(/\/+$/, '')))
    .replace(/<\?=\s*htmlspecialchars\(famo_local_asset\('([^']+)',\s*\$base\),\s*ENT_QUOTES,\s*'UTF-8'\)\s*\?>/g, (_m, p) => escapeHtml(famoLocalAsset(p, base)))
    .replace(/<\?=\s*htmlspecialchars\(famo_asset\('([^']+)'[^)]+\),\s*ENT_QUOTES,\s*'UTF-8'\)\s*\?>/g, (_m, p) => escapeHtml(famoAsset(p)));
}

function renderPhpPreviewFile(relPath, base = '/', vars = {}) {
  const fullPath = path.join(ROOT_DIR, relPath);
  let content = fs.readFileSync(fullPath, 'utf8');

  const preambleMatch = content.match(/^<\?php([\s\S]*?)\?>\s*/m);
  const preamble = preambleMatch ? preambleMatch[1] : '';
  const libs = parsePageLibsFromPreamble(preamble);

  const siteUrl = famoPublicSiteUrl();
  const parsedCanonical = parsePhpStringVar(preamble, 'canonicalPath');
  const effectiveCanonicalRel =
    vars.canonicalPath !== undefined
      ? vars.canonicalPath
      : (parsedCanonical || (relPath === 'index.php' ? '' : relPath));

  const seo = {
    title: vars.pageTitle || parsePhpStringVar(preamble, 'pageTitle') || 'آموزشگاه فامو بابل | آموزش شخصی، با نگاهی هوشمند',
    description: vars.metaDescription || parsePhpStringVar(preamble, 'metaDescription') || 'آموزشگاه فامو در بابل؛ آموزش شخصی‌سازی‌شده، کلاس خصوصی، آزمون شخصی، مشاوره و پایش عملکرد از ابتدایی تا کنکور و تیزهوشان.',
    canonical: famoCanonicalUrl(effectiveCanonicalRel),
    robots: vars.robots || parsePhpStringVar(preamble, 'robots') || 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
    ogType: vars.ogType || parsePhpStringVar(preamble, 'ogType') || 'website',
    ogImage: relPath === 'pages/course.php'
      ? `${siteUrl}/assets/images/courses_banner.webp`
      : relPath === 'pages/team.php'
        ? `${siteUrl}/assets/images/team.webp`
        : `${siteUrl}/assets/images/poster_hero.webp`,
    ogImageAlt: parsePhpStringVar(preamble, 'ogImageAlt') || parsePhpStringVar(preamble, 'pageTitle') || 'آموزشگاه فامو',
    webPageType: parsePhpStringVar(preamble, 'webPageType') || 'WebPage',
  };

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

  // Evaluate $libs conditionals from head-common.php
  content = evalLibsConditionals(content, libs);

  // Page-specific PHP block evaluation
  let organizationExtra = null;
  if (relPath === 'index.php') {
    const homeData = vars.homeData || { courses: null, instructors: null, supporters: null, testimonials: null };
    const ssrCoursesRaw = Array.isArray(homeData.courses)
      ? homeData.courses.filter((c) => c && typeof c === 'object')
      : [];
    const ssrCourses = ssrCoursesRaw.length > 0 ? ssrCoursesRaw.slice(0, 4) : null;
    const hasCoursesSsr = ssrCourses !== null;

    const ssrTeam = (Array.isArray(homeData.instructors) && Array.isArray(homeData.supporters))
      ? buildTeamList(homeData.instructors, homeData.supporters)
      : [];
    const hasTeamSsr = Array.isArray(homeData.instructors) && Array.isArray(homeData.supporters) && ssrTeam.length > 0;
    const ssrTeamTotal = ssrTeam.length;
    const ssrTeamOffMode = hasTeamSsr && ssrTeamTotal < 3;

    const ssrTestiPayload = homeData.testimonials && typeof homeData.testimonials === 'object' ? homeData.testimonials : null;
    const ssrTestiItems = Array.isArray(ssrTestiPayload?.items) ? ssrTestiPayload.items.slice(0, 2) : [];
    const ssrTestiSummary = ssrTestiPayload?.summary && typeof ssrTestiPayload.summary === 'object' ? ssrTestiPayload.summary : null;
    const hasTestimonialsSsr = ssrTestiItems.length > 0;
    if (hasTestimonialsSsr) {
      const schemaExtra = buildTestimonialsSchema(ssrTestiSummary, ssrTestiItems, 2);
      if (Object.keys(schemaExtra).length > 0) organizationExtra = schemaExtra;
    }

    content = content
      .replace(/<\?=\s*\$ssrTeamOffMode\s*\?\s*' curve-carousel--off-mode'\s*:\s*''\s*\?>/g, ssrTeamOffMode ? ' curve-carousel--off-mode' : '')
      .replace(/<\?=\s*\$hasTeamSsr\s*\?\s*''\s*:\s*' is-skeleton'\s*\?>/g, hasTeamSsr ? '' : ' is-skeleton')
      .replace(/<\?=\s*\$hasTeamSsr\s*\?\s*'false'\s*:\s*'true'\s*\?>/g, hasTeamSsr ? 'false' : 'true')
      .replace(/<\?=\s*\$hasTeamSsr\s*\?\s*' data-ssr="1" data-ssr-total="'\s*\.\s*\$ssrTeamTotal\s*\.\s*'"'\s*:\s*''\s*\?>/g, hasTeamSsr ? ` data-ssr="1" data-ssr-total="${ssrTeamTotal}"` : '')
      .replace(/<\?=\s*\$ssrTeamOffMode\s*\?\s*' hidden'\s*:\s*''\s*\?>/g, ssrTeamOffMode ? ' hidden' : '')
      .replace(/<\?=\s*\(\$hasTeamSsr\s*&&\s*!\$ssrTeamOffMode\)\s*\?\s*famo_render_team_dots\(\$ssrTeam\)\s*:\s*''\s*\?>/g, (hasTeamSsr && !ssrTeamOffMode) ? renderTeamDots(ssrTeam) : '')
      .replace(/<\?php if \(\$hasTeamSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/g, (_m, fallbackHtml) => (hasTeamSsr ? renderTeamCards(ssrTeam) : fallbackHtml))
      .replace(/<\?=\s*\$hasCoursesSsr\s*\?\s*''\s*:\s*' coursesSwiper--loading'\s*\?>/g, hasCoursesSsr ? '' : ' coursesSwiper--loading')
      .replace(/<\?=\s*\$hasCoursesSsr\s*\?\s*'false'\s*:\s*'true'\s*\?>/g, hasCoursesSsr ? 'false' : 'true')
      .replace(/<\?=\s*\$hasCoursesSsr\s*\?\s*' data-ssr="1"'\s*:\s*''\s*\?>/g, hasCoursesSsr ? ' data-ssr="1"' : '')
      .replace(/<\?php if \(\$hasCoursesSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/g, (_m, fallbackHtml) => (
        hasCoursesSsr
          ? ssrCourses.map((c, idx) => renderCourseSlide(c, idx)).join('\n')
          : fallbackHtml
      ))
      .replace(/<\?=\s*\$hasTestimonialsSsr\s*\?\s*' data-ssr="1"'\s*:\s*''\s*\?>/g, hasTestimonialsSsr ? ' data-ssr="1"' : '')
      .replace(/<\?php if \(\$hasTestimonialsSsr\): foreach \(\$ssrTestiItems as \$tItem\): echo famo_render_home_testimonial_excerpt\(\$tItem\); endforeach; endif; \?>/g, hasTestimonialsSsr ? ssrTestiItems.map((t) => renderHomeTestimonialExcerpt(t)).join('') : '');
  } else if (relPath === 'pages/course.php') {
    const homeData = vars.homeData || { courses: null };
    const rawCourses = Array.isArray(homeData.courses)
      ? homeData.courses.filter((c) => c && typeof c === 'object')
      : [];
    const hasCoursesSsr = rawCourses.length > 0;

    const subjectCardsHtml = COURSE_HUB_SUBJECT_LANDINGS.map((sl) => `
            <article class="famo-advantage-card hover-lift">
                <span class="famo-advantage-icon" aria-hidden="true">
                    <i class="icon icon--lg" data-lucide="${escapeHtml(sl.icon)}" aria-hidden="true"></i>
                </span>
                <h3 class="famo-advantage-title">${escapeHtml(sl.name)}</h3>
                <p class="text-xs font-bold text-[#736158] mb-2">${escapeHtml(sl.level)}</p>
                <p class="famo-advantage-text">${escapeHtml(sl.desc)}</p>
                <a href="${escapeHtml(sl.url)}" class="famo-inline-link">
                    مشاهده صفحه اختصاصی
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>`).join('\n');

    const gradeProgramsHtml = COURSE_HUB_GRADE_PROGRAMS.map((gp) => `
        <article class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">${escapeHtml(gp.badge)}</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">${escapeHtml(gp.title)}</h2>
            <p class="text-gray-600 leading-relaxed mb-4">${escapeHtml(gp.desc)}</p>
            <ul class="famo-bullet-grid">
                ${gp.points.map((pt) => `<li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>${escapeHtml(pt)}</li>`).join('\n')}
            </ul>
            <div class="flex flex-wrap items-center gap-3">
                <a href="${escapeHtml(gp.url)}" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                    ${escapeHtml(gp.cta)}
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
                <a href="/contact/#consultation" class="inline-flex items-center gap-2 border-2 border-[#445D84] text-[#445D84] font-bold px-5 py-2 rounded-full hover:bg-[#445D84] hover:text-white transition text-sm">
                    درخواست مشاوره
                </a>
            </div>
        </article>`).join('\n');

    const fallbackCoursesHtml = COURSE_HUB_SUBJECT_LANDINGS.slice(0, 3).map((fb) => `
            <article class="course-card">
                <div class="course-image-wrapper" style="background: linear-gradient(135deg, #445D84, #6D8B9E);">
                    <div class="course-image-fallback">
                        <i class="icon icon--3xl text-white/90" data-lucide="${escapeHtml(fb.icon)}" aria-hidden="true"></i>
                    </div>
                    <span class="course-badge">برنامه فعال بابل</span>
                </div>
                <div class="course-body">
                    <h3 class="course-name">${escapeHtml(fb.name)}</h3>
                    <div class="course-meta"><span>${escapeHtml(fb.level)}</span><span>خصوصی و انفرادی</span></div>
                    <p class="course-desc">${escapeHtml(fb.desc)}</p>
                    <div class="course-footer">
                        <a href="/contact/#consultation" class="course-cta">درخواست مشاوره</a>
                        <a href="${escapeHtml(fb.url)}" class="text-sm font-bold text-[#445D84] hover:underline inline-flex items-center gap-1">
                            جزئیات بیشتر
                            <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>`).join('\n');

    content = content
      .replace(/<\?php foreach \(\$subjectLandings as \$sl\): \?>[\s\S]*?<\/article>\s*<\?php endforeach; \?>/, subjectCardsHtml)
      .replace(/<\?php foreach \(\$gradePrograms as \$gp\): \?>[\s\S]*?<\/article>\s*<\?php endforeach; \?>/, gradeProgramsHtml)
      .replace(/<\?=\s*\$hasCoursesSsr\s*\?\s*' data-ssr="1"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, hasCoursesSsr ? ' data-ssr="1"' : ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasCoursesSsr\): \?>[\s\S]*?<\?php else: \?>[\s\S]*?<\?php endif; \?>/, hasCoursesSsr ? rawCourses.map((c) => renderCourseGridCard(c)).join('\n') : fallbackCoursesHtml);
  } else if (relPath === 'pages/team.php') {
    const homeData = vars.homeData || { instructors: null, supporters: null };
    const ssrInstructors = Array.isArray(homeData.instructors)
      ? homeData.instructors.filter((x) => x && typeof x === 'object').map(normalizeInstructor)
      : [];
    const ssrSupporters = Array.isArray(homeData.supporters)
      ? homeData.supporters.filter((x) => x && typeof x === 'object').map(normalizeSupporter)
      : [];
    const hasInstructorsSsr = ssrInstructors.length > 0;
    const hasSupportersSsr = ssrSupporters.length > 0;

    content = content
      .replace(/<\?=\s*\$hasInstructorsSsr\s*\?\s*' data-ssr="1"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, hasInstructorsSsr ? ' data-ssr="1"' : ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasInstructorsSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => (hasInstructorsSsr ? ssrInstructors.map((p) => renderPersonCard(p)).join('\n') : fb))
      .replace(/<\?=\s*\$hasSupportersSsr\s*\?\s*' data-ssr="1"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, hasSupportersSsr ? ' data-ssr="1"' : ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasSupportersSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => (hasSupportersSsr ? ssrSupporters.map((p) => renderPersonCard(p)).join('\n') : fb));
  } else if (relPath === 'pages/results.php') {
    const ssrTestiPayload = vars.testimonialsData && typeof vars.testimonialsData === 'object' ? vars.testimonialsData : null;
    const testiSummary = ssrTestiPayload?.summary && typeof ssrTestiPayload.summary === 'object' ? ssrTestiPayload.summary : null;
    const ssrTestiItems = Array.isArray(ssrTestiPayload?.items) ? ssrTestiPayload.items.filter((x) => x && typeof x === 'object') : [];
    const testiTotalPages = Math.max(1, Number.parseInt(String(ssrTestiPayload?.pagination?.total_pages ?? 1), 10) || 1);
    const hasTestiSsr = ssrTestiItems.length > 0;
    if (hasTestiSsr || testiSummary) {
      const schemaExtra = buildTestimonialsSchema(testiSummary, ssrTestiItems, 9);
      if (Object.keys(schemaExtra).length > 0) organizationExtra = schemaExtra;
    }

    content = content
      .replace(/<\?=\s*\$hasAdmSsr\s*\?\s*' data-ssr="1"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasAdmSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => fb)
      .replace(/<\?php if \(\$testiSummary !== null\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => (testiSummary ? renderTestimonialSummary(testiSummary) : fb))
      .replace(/<\?=\s*\$hasTestiSsr\s*\?\s*' data-ssr="1" data-ssr-total-pages="'\s*\.\s*\$testiTotalPages\s*\.\s*'"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, hasTestiSsr ? ` data-ssr="1" data-ssr-total-pages="${testiTotalPages}"` : ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasTestiSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => (hasTestiSsr ? ssrTestiItems.map((it) => renderTestimonialCard(it, 'h4')).join('\n') : fb))
      .replace(/<\?=\s*\(\$hasTestiSsr\s*&&\s*\$testiTotalPages\s*>\s*1\)\s*\?\s*famo_render_testimonials_pagination\(1,\s*\$testiTotalPages\)\s*:\s*''\s*\?>/g, (hasTestiSsr && testiTotalPages > 1) ? renderTestimonialsPagination(1, testiTotalPages) : '');
  } else if (relPath === 'pages/blog.php') {
    content = content
      .replace(/<\?=\s*\$hasPostsSsr\s*\?\s*' data-ssr="1"'\s*:\s*' data-has-fallback="1"'\s*\?>/g, ' data-has-fallback="1"')
      .replace(/<\?php if \(\$hasPostsSsr\): \?>[\s\S]*?<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, fb) => fb);
  } else if (relPath === 'pages/post.php') {
    const isNotFound = !vars.blogSlug;
    content = content
      .replace(/<\?=\s*is_array\(\$ssrPost\)\s*\?[\s\S]*?\?>/g, isNotFound ? ' data-not-found="1"' : ' data-has-fallback="1"')
      .replace(/<\?php if \(is_array\(\$ssrPost\)\):[\s\S]*?<\?php elseif \(\$isNotFound\): \?>([\s\S]*?)<\?php else: \?>([\s\S]*?)<\?php endif; \?>/, (_m, nfHtml, fbHtml) => (isNotFound ? nfHtml : fbHtml));
  }

  return applyCommonReplacements(content, { relPath, base, seo, organizationExtra });
}

function renderLandingPreviewPage(pageData) {
  const base = '/';
  const siteUrl = famoPublicSiteUrl();
  const slug = String(pageData.slug || '').replace(/^\/+|\/+$/g, '');
  const canonicalPath = `${slug}/`;
  const pageCanonicalUrl = famoCanonicalUrl(canonicalPath);
  const pageTitle = String(pageData.pageTitle || 'آموزشگاه فامو بابل');
  const metaDescription = String(pageData.metaDescription || '');
  const heroImage = String(pageData.heroImage || '/assets/images/courses_banner.webp');
  const ogImage = heroImage.startsWith('http') ? heroImage : `${siteUrl}${heroImage}`;
  const ogImageAlt = String(pageData.heroImageAlt || pageTitle);

  const rawCrumbs = Array.isArray(pageData.breadcrumbs) ? pageData.breadcrumbs : [];
  const breadcrumbs = rawCrumbs
    .filter((c) => c && c.name)
    .map((c) => ({
      name: String(c.name),
      path: String(c.path || ''),
      url: famoCanonicalUrl(String(c.path || '')),
    }));

  const orgRef = {
    '@type': 'EducationalOrganization',
    '@id': `${siteUrl}/#organization`,
    name: 'آموزشگاه فامو',
    url: `${siteUrl}/`,
  };

  const schemaType = String(pageData.schemaType || 'Course');
  const extraSchemas = [];
  if (schemaType === 'Course') {
    const courseEntity = {
      '@type': 'Course',
      '@id': `${pageCanonicalUrl}#course`,
      name: String(pageData.h1 || pageTitle),
      description: metaDescription,
      url: pageCanonicalUrl,
      inLanguage: 'fa-IR',
      provider: orgRef,
      hasCourseInstance: {
        '@type': 'CourseInstance',
        courseMode: 'onsite',
        location: {
          '@type': 'Place',
          name: 'آموزشگاه فامو بابل',
          address: {
            '@type': 'PostalAddress',
            addressLocality: 'بابل',
            addressRegion: 'مازندران',
            addressCountry: 'IR',
            streetAddress: 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
          },
        },
      },
    };
    if (pageData.educationalLevel) courseEntity.educationalLevel = String(pageData.educationalLevel);
    extraSchemas.push(courseEntity);
  } else if (schemaType === 'Service') {
    extraSchemas.push({
      '@type': 'Service',
      '@id': `${pageCanonicalUrl}#service`,
      name: String(pageData.h1 || pageTitle),
      description: metaDescription,
      url: pageCanonicalUrl,
      provider: orgRef,
      areaServed: [
        { '@type': 'City', name: 'بابل' },
        { '@type': 'AdministrativeArea', name: 'مازندران' },
      ],
    });
  } else {
    extraSchemas.push({
      '@type': 'LocalBusiness',
      '@id': `${pageCanonicalUrl}#local`,
      name: 'آموزشگاه فامو بابل',
      description: metaDescription,
      url: pageCanonicalUrl,
      telephone: '+98-901-440-2300',
      address: {
        '@type': 'PostalAddress',
        addressLocality: 'بابل',
        addressRegion: 'مازندران',
        addressCountry: 'IR',
        streetAddress: 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
      },
      areaServed: [
        { '@type': 'City', name: 'بابل' },
        { '@type': 'City', name: 'امیرکلا' },
        { '@type': 'City', name: 'بابلسر' },
        { '@type': 'City', name: 'آمل' },
        { '@type': 'City', name: 'قائمشهر' },
      ],
    });
  }

  const faqs = Array.isArray(pageData.faqs) ? pageData.faqs : [];
  if (faqs.length > 0) {
    extraSchemas.push({
      '@type': 'FAQPage',
      '@id': `${pageCanonicalUrl}#faq`,
      inLanguage: 'fa-IR',
      mainEntity: faqs
        .filter((f) => f && f.q && f.a)
        .map((f) => ({
          '@type': 'Question',
          name: String(f.q),
          acceptedAnswer: { '@type': 'Answer', text: String(f.a) },
        })),
    });
  }

  const libs = {
    gsap: false,
    scroll_trigger: false,
    scroll_to: false,
    swiper: false,
    lucide: true,
    animation: false,
  };

  const readLayout = (name) => fs.readFileSync(path.join(ROOT_DIR, 'layouts', name), 'utf8').replace(/^<\?php[\s\S]*?\?>\s*/m, '');
  const headHtml = evalLibsConditionals(readLayout('head-common.php'), libs);
  const headerHtml = readLayout('header.php');
  const footerHtml = readLayout('footer.php');

  const crumbsNavHtml = breadcrumbs.length > 1
    ? `<nav aria-label="مسیر صفحه" class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm famo-on-dark-soft mb-4">${breadcrumbs
        .map((c, idx) => {
          const sep = idx > 0 ? '<span aria-hidden="true">/</span>' : '';
          if (idx < breadcrumbs.length - 1) {
            const href = `/${c.path.replace(/^\/+/, '')}`;
            return `${sep}<a href="${escapeHtml(href)}" class="hover:text-white transition underline-offset-4 hover:underline">${escapeHtml(c.name)}</a>`;
          }
          return `${sep}<span class="famo-on-dark-cream font-bold" aria-current="page">${escapeHtml(c.name)}</span>`;
        })
        .join('')}</nav>`
    : '';

  const highlights = Array.isArray(pageData.highlights) ? pageData.highlights : [];
  const highlightsSection = highlights.length > 0
    ? `<section class="py-12 sm:py-16 px-4 bg-white border-b border-[#E2D9C6]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-10">
                <span class="famo-eyebrow famo-eyebrow--light">ویژگی‌های برنامه در فامو</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84]">شیوه اجرا و پیگیری آموزشی</h2>
            </div>
            <div class="famo-summary-grid">
                ${highlights.map((item) => `
                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true"><i class="icon icon--lg" data-lucide="check" aria-hidden="true"></i></span>
                    <h3 class="famo-advantage-title">${escapeHtml(item.title || '')}</h3>
                    <p class="famo-advantage-text">${escapeHtml(item.text || '')}</p>
                </article>`).join('')}
            </div>
        </div>
    </section>`
    : '';

  const audience = Array.isArray(pageData.audience) ? pageData.audience : [];
  const syllabus = Array.isArray(pageData.syllabus) ? pageData.syllabus : [];
  const related = Array.isArray(pageData.related) ? pageData.related : [];

  const faqsSection = faqs.length > 0
    ? `<section class="py-12 sm:py-16 px-4 bg-white border-t border-[#E2D9C6]">
        <div class="container mx-auto max-w-4xl">
            <div class="text-center mb-8">
                <span class="famo-eyebrow famo-eyebrow--light">پرسش‌های پرتکرار</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84]">سؤالات متداول خانواده‌ها و دانش‌آموزان</h2>
            </div>
            <div class="space-y-4">
                ${faqs.map((faq) => `
                <div class="faq-item bg-[#f9f7f3] rounded-xl shadow-sm border border-[#E2D9C6] overflow-hidden">
                    <div class="faq-question cursor-pointer p-5 flex justify-between items-center gap-4" tabindex="0" role="button" aria-expanded="false">
                        <h3 class="text-base sm:text-lg font-bold text-[#445D84] m-0">${escapeHtml(faq.q || '')}</h3>
                        <span class="faq-icon transform transition-transform duration-300 shrink-0"><i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i></span>
                    </div>
                    <div class="faq-answer px-5">
                        <p class="pb-5 text-gray-700 leading-relaxed m-0">${escapeHtml(faq.a || '')}</p>
                    </div>
                </div>`).join('')}
            </div>
        </div>
    </section>`
    : '';

  const relatedBlock = related.length > 0
    ? `<div class="famo-detail-block">
            <h2 class="text-xl font-bold text-[#445D84] mb-4">مسیرها و صفحات مرتبط در آموزشگاه فامو</h2>
            <div class="flex flex-wrap gap-3">
                ${related.map((rel) => `<a href="/${escapeHtml(String(rel.path || '').replace(/^\/+/, ''))}" class="famo-anchor-pill">${escapeHtml(rel.label || '')}</a>`).join('')}
            </div>
        </div>`
    : '';

  const doc = `<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    ${headHtml}
</head>
<body class="font-family text-gray-800 bg-[#f9f7f3]" data-static-nav="1">
${headerHtml}
<main id="main-content">
    <section class="famo-page-hero gradient-bg text-white text-center">
        <div class="container mx-auto px-4 max-w-4xl">
            ${crumbsNavHtml}
            <p class="famo-eyebrow">${escapeHtml(pageData.eyebrow || 'آموزشگاه فامو | بابل')}</p>
            <h1 class="text-2xl sm:text-4xl font-bold mb-5 text-white leading-snug">${escapeHtml(pageData.h1 || pageTitle)}</h1>
            <p class="famo-on-dark-soft text-base sm:text-lg leading-relaxed max-w-3xl mx-auto mb-7">${escapeHtml(pageData.lead || '')}</p>
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="/contact/#consultation" class="inline-flex items-center gap-2 famo-btn-cream font-bold px-7 py-3 rounded-full transition duration-300 shadow-lg">
                    <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره و تعیین سطح
                </a>
                <a href="/courses/" class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-6 py-3 rounded-full transition duration-300">
                    <i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i>
                    همه دوره‌ها و مقاطع
                </a>
            </div>
        </div>
    </section>
    ${highlightsSection}
    <section class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl famo-two-col">
            <article class="famo-detail-block">
                <span class="famo-eyebrow famo-eyebrow--light">مخاطبان این برنامه</span>
                <h2 class="text-xl sm:text-2xl font-bold text-[#445D84] mb-4">این برنامه برای چه دانش‌آموزانی مناسب است؟</h2>
                <ul class="space-y-3 text-gray-700 leading-relaxed list-none p-0 m-0">
                    ${audience.map((line) => `<li class="flex items-start gap-2.5"><span class="famo-feature-check mt-1 shrink-0" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span><span>${escapeHtml(line)}</span></li>`).join('')}
                </ul>
            </article>
            <article class="famo-detail-block">
                <span class="famo-eyebrow famo-eyebrow--light">سرفصل‌ها و محورهای کلیدی</span>
                <h2 class="text-xl sm:text-2xl font-bold text-[#445D84] mb-4">آنچه در این مسیر پوشش داده می‌شود</h2>
                <ul class="space-y-3 text-gray-700 leading-relaxed list-none p-0 m-0">
                    ${syllabus.map((topic) => `<li class="flex items-start gap-2.5"><span class="famo-feature-check mt-1 shrink-0" aria-hidden="true"><i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i></span><span>${escapeHtml(topic)}</span></li>`).join('')}
                </ul>
            </article>
        </div>
    </section>
    ${faqsSection}
    <section class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl space-y-10">
            ${relatedBlock}
            <div class="gradient-bg rounded-3xl p-8 sm:p-10 text-center text-white shadow-xl">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">آماده شروع برنامه شخصی‌سازی‌شده هستید؟</h2>
                <p class="famo-on-dark-soft max-w-2xl mx-auto leading-relaxed mb-7">
                    برای بررسی پایه تحصیلی، انتخاب استاد مناسب و هماهنگی جلسه مشاوره حضوری در میدان باغ فردوس بابل با واحد مشاوره فامو در تماس باشید.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="/contact/#consultation" class="inline-flex items-center gap-2 famo-btn-cream font-bold px-8 py-3 rounded-full transition duration-300 shadow-lg">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="tel:+989014402300" dir="ltr" class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-7 py-3 rounded-full transition duration-300">
                        0901-440-2300
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
${footerHtml}
</body>
</html>`;

  return applyCommonReplacements(doc, {
    relPath: 'pages/landing.php',
    base,
    seo: {
      title: pageTitle,
      description: metaDescription,
      canonical: pageCanonicalUrl,
      robots: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
      ogType: 'website',
      ogImage,
      ogImageAlt,
      webPageType: 'WebPage',
    },
    extraSchemas,
    customCrumbs: breadcrumbs,
  });
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
  const styleMatches = [...rPhp.matchAll(/<style>[\s\S]*?<\/style>/g)];
  const inlineStyle = (styleMatches.length > 1 ? styleMatches[1][0] : (styleMatches[0]?.[0] || ''))
    .replace(/<\?[\s\S]*?\?>/g, '');

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

  const fontStyle = `<style>@font-face{font-family:'Vazirmatn';src:url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Regular.woff2', '/'))}') format('woff2'),url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Regular.ttf', '/'))}') format('truetype');font-weight:400;font-style:normal;font-display:swap;}@font-face{font-family:'Vazirmatn';src:url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Medium.woff2', '/'))}') format('woff2'),url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Medium.ttf', '/'))}') format('truetype');font-weight:500;font-style:normal;font-display:swap;}@font-face{font-family:'Vazirmatn';src:url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Bold.woff2', '/'))}') format('woff2'),url('${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Bold.ttf', '/'))}') format('truetype');font-weight:700;font-style:normal;font-display:swap;}</style>`;
  return `<!DOCTYPE html><html lang="fa" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="robots" content="noindex, nofollow"><meta name="referrer" content="no-referrer"><meta name="color-scheme" content="light"><title>کارنامه آزمون | آموزشگاه فامو</title><link rel="preload" as="font" type="font/woff2" href="${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Regular.woff2', '/'))}" crossorigin><link rel="preload" as="font" type="font/woff2" href="${escapeHtml(famoLocalAsset('assets/fonts/Vazirmatn-Bold.woff2', '/'))}" crossorigin>${fontStyle}<link rel="stylesheet" href="${escapeHtml(famoLocalAsset('assets/css/tokens.css', '/'))}"><link rel="icon" type="image/png" sizes="32x32" href="${escapeHtml(famoLocalAsset('assets/images/favicon-32.png', '/'))}">${inlineStyle}</head><body><header class="exam-header"><div class="exam-header-inner"><div class="exam-brand"><picture><source srcset="${escapeHtml(famoLocalAsset('assets/images/logo.webp', '/'))}" type="image/webp"><img src="${escapeHtml(famoLocalAsset('assets/images/logo.png', '/'))}" alt="آموزشگاه فامو" width="40" height="40" decoding="async" class="exam-logo"></picture><div><div class="exam-brand-title">آموزشگاه فامو</div><div class="exam-brand-sub">گزارش اختصاصی نتیجه آزمون</div></div></div>${data ? '<button type="button" onclick="window.print()" class="no-print exam-print-btn">چاپ کارنامه</button>' : ''}</div></header><main class="exam-main">${bodyInner}</main><footer class="exam-footer">آموزشگاه فامو — مشاوره تخصصی کنکور و تیزهوشان</footer></body></html>`;
}

const LEGACY_PHP_REDIRECTS = {
  '/index.php': { dest: '/', keepQs: true },
  '/pages/course.php': { dest: '/courses/', keepQs: false },
  '/pages/services.php': { dest: '/services/', keepQs: false },
  '/pages/team.php': { dest: '/team/', keepQs: false },
  '/pages/admissions.php': { dest: '/results/', keepQs: false },
  '/pages/testimonials.php': { dest: '/results/', keepQs: false },
  '/pages/results.php': { dest: '/results/', keepQs: false },
  '/pages/contact.php': { dest: '/contact/', keepQs: false },
  '/pages/blog.php': { dest: '/blog/', keepQs: true },
};

const CLEAN_STATIC_ROUTES = {
  '/courses/': 'pages/course.php',
  '/services/': 'pages/services.php',
  '/team/': 'pages/team.php',
  '/results/': 'pages/results.php',
  '/contact/': 'pages/contact.php',
  '/blog/': 'pages/blog.php',
};

const server = http.createServer(async (req, res) => {
  const reqUrl = new URL(req.url || '/', `http://${req.headers.host || '127.0.0.1'}`);
  const pathname = reqUrl.pathname;
  const qs = reqUrl.search || '';

  // Mirror .htaccess Phase 0 hardening: block internal/config files & directories
  if (
    /^\/(\.env|\.git|config\.php|Dockerfile|docker-compose\.ya?ml|package(?:-lock)?\.json)$/i.test(pathname) ||
    /^\/(layouts|docker|scripts|tools)(\/|$)/i.test(pathname)
  ) {
    res.writeHead(403, { 'Content-Type': 'text/plain; charset=utf-8' });
    res.end('Forbidden');
    return;
  }

  // Mirror .htaccess 301 redirects for legacy .php URLs
  if (Object.prototype.hasOwnProperty.call(LEGACY_PHP_REDIRECTS, pathname)) {
    const cfg = LEGACY_PHP_REDIRECTS[pathname];
    res.writeHead(301, { Location: cfg.keepQs ? `${cfg.dest}${qs}` : cfg.dest });
    res.end();
    return;
  }
  if (pathname === '/pages/post.php') {
    const slug = String(reqUrl.searchParams.get('slug') || '').trim();
    const dest = slug ? `/blog/${encodeURIComponent(slug)}/` : '/blog/';
    res.writeHead(301, { Location: dest });
    res.end();
    return;
  }

  // Static assets, robots.txt, sitemap.xml
  if (pathname.startsWith('/assets/') || pathname === '/robots.txt' || pathname === '/sitemap.xml') {
    const safeRel = path.normalize(pathname).replace(/^(\.\.(\/|\\|$))+/, '');
    const filePath = path.join(ROOT_DIR, safeRel);
    if (filePath.startsWith(ROOT_DIR) && fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      const ext = path.extname(filePath).toLowerCase();
      const headers = { 'Content-Type': MIME_TYPES[ext] || 'application/octet-stream' };
      if (pathname === '/sitemap.xml') {
        headers['Cache-Control'] = 'public, max-age=300, must-revalidate';
      }
      res.writeHead(200, headers);
      fs.createReadStream(filePath).pipe(res);
      return;
    }
    res.writeHead(404);
    res.end('Not Found');
    return;
  }

  const seoPagesMap = loadSeoPagesMap();

  // Trailing-slash 301 normalization for clean URLs
  if (pathname.length > 1 && !pathname.endsWith('/')) {
    const withSlash = `${pathname}/`;
    const candidateSlug = pathname.replace(/^\/+/, '');
    if (
      Object.prototype.hasOwnProperty.call(CLEAN_STATIC_ROUTES, withSlash) ||
      seoPagesMap.has(candidateSlug) ||
      /^\/blog\/[^/]+$/.test(pathname)
    ) {
      res.writeHead(301, { Location: `${withSlash}${qs}` });
      res.end();
      return;
    }
  }

  // Homepage
  if (pathname === '/') {
    const homeData = await famoHomeData();
    const html = renderPhpPreviewFile('index.php', '/', { homeData });
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(html);
    return;
  }

  // Clean hub pages (/courses/, /services/, /team/, /results/, /contact/, /blog/)
  if (Object.prototype.hasOwnProperty.call(CLEAN_STATIC_ROUTES, pathname)) {
    const targetFile = CLEAN_STATIC_ROUTES[pathname];
    const vars = {};
    if (targetFile === 'pages/course.php' || targetFile === 'pages/team.php') {
      vars.homeData = await famoHomeData();
    } else if (targetFile === 'pages/results.php') {
      vars.testimonialsData = await famoTestimonialsPageData({ perPage: 9 });
    }
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile(targetFile, '/', vars));
    return;
  }

  // Dedicated SEO landing pages (14 slugs from layouts/seo-pages.json)
  if (pathname.endsWith('/')) {
    const slugKey = pathname.replace(/^\/+|\/+$/g, '');
    if (seoPagesMap.has(slugKey)) {
      const html = renderLandingPreviewPage(seoPagesMap.get(slugKey));
      res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
      res.end(html);
      return;
    }
  }

  // Clean dynamic blog post route (/blog/{slug}/)
  const blogMatch = /^\/blog\/([^/]+)\/$/.exec(pathname);
  if (blogMatch) {
    let decodedSlug = blogMatch[1];
    try {
      decodedSlug = decodeURIComponent(blogMatch[1]);
    } catch {}
    const html = renderPhpPreviewFile('pages/post.php', '/', {
      blogSlug: decodedSlug,
      canonicalPath: `blog/${encodeURIComponent(decodedSlug)}/`,
    });
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(html);
    return;
  }

  if (pathname === '/404.php') {
    res.writeHead(404, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(renderPhpPreviewFile('404.php', '/'));
    return;
  }
  if (pathname === '/sitemap-blog.php' || pathname === '/sitemap-blog.xml') {
    res.writeHead(200, {
      'Content-Type': 'application/xml; charset=utf-8',
      'Cache-Control': 'public, max-age=1800',
    });
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

  res.writeHead(404, { 'Content-Type': 'text/html; charset=utf-8' });
  res.end(renderPhpPreviewFile('404.php', '/'));
});

server.listen(PORT, HOST, () => {
  console.log(`Preview harness listening on http://${HOST}:${PORT} (Node sandbox preview; PHP is served by Apache in Docker)`);
});
