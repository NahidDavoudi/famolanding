import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT = path.resolve(__dirname, '..');

// WCAG 2.1 color utilities
export function parseColor(input) {
  const s = String(input).trim().toLowerCase();
  if (s.startsWith('#')) {
    let hex = s.slice(1);
    if (hex.length === 3) {
      hex = hex.split('').map((c) => c + c).join('');
    }
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    const a = hex.length === 8 ? parseInt(hex.slice(6, 8), 16) / 255 : 1;
    return { r, g, b, a };
  }
  const m = s.match(/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)$/);
  if (m) {
    return {
      r: Number(m[1]),
      g: Number(m[2]),
      b: Number(m[3]),
      a: m[4] !== undefined ? Number(m[4]) : 1,
    };
  }
  throw new Error(`Unsupported color: ${input}`);
}

export function composite(fg, bg) {
  const a = fg.a ?? 1;
  if (a >= 1) return { r: fg.r, g: fg.g, b: fg.b, a: 1 };
  return {
    r: fg.r * a + bg.r * (1 - a),
    g: fg.g * a + bg.g * (1 - a),
    b: fg.b * a + bg.b * (1 - a),
    a: 1,
  };
}

export function toHex({ r, g, b }) {
  const clamp = (v) => Math.max(0, Math.min(255, Math.round(v)));
  return '#' + [clamp(r), clamp(g), clamp(b)].map((v) => v.toString(16).padStart(2, '0')).join('').toUpperCase();
}

export function relativeLuminance({ r, g, b }) {
  const lin = (c) => {
    const s = c / 255;
    return s <= 0.03928 ? s / 12.92 : ((s + 0.055) / 1.055) ** 2.4;
  };
  return 0.2126 * lin(r) + 0.7152 * lin(g) + 0.0722 * lin(b);
}

export function contrastRatio(fgInput, bgInput) {
  const bg = parseColor(bgInput);
  const fgRaw = parseColor(fgInput);
  const fg = composite(fgRaw, bg);
  const l1 = relativeLuminance(fg);
  const l2 = relativeLuminance(bg);
  const lighter = Math.max(l1, l2);
  const darker = Math.min(l1, l2);
  return (lighter + 0.05) / (darker + 0.05);
}

// Compute worst-case (minimum) contrast across multiple background stops (e.g., gradients)
export function worstContrast(fgInput, bgStops) {
  const stops = Array.isArray(bgStops) ? bgStops : [bgStops];
  let worst = Infinity;
  let worstStop = stops[0];
  for (const stop of stops) {
    const ratio = contrastRatio(fgInput, stop);
    if (ratio < worst) {
      worst = ratio;
      worstStop = stop;
    }
  }
  return { ratio: worst, worstStop };
}

// Composite an alpha surface (like glass header over light page) then compute contrast
export function compositeSurface(surfaceRgba, backdropHex) {
  return toHex(composite(parseColor(surfaceRgba), parseColor(backdropHex)));
}

// Extract :root custom properties from assets/css/style.css for live verification
function extractStyleVars() {
  const css = fs.readFileSync(path.join(ROOT, 'assets/css/style.css'), 'utf8');
  const vars = {};
  for (const m of css.matchAll(/(--famo-[a-z0-9-]+)\s*:\s*([^;]+);/gi)) {
    vars[m[1]] = m[2].trim();
  }
  return { css, vars };
}

export function getBeforePairs() {
  // Header glass over white page (#ffffff) using lightest stop rgba(85,113,155,0.68) and mid rgba(68,93,132,0.62)
  const headerGlassWorst = compositeSurface('rgba(68, 93, 132, 0.62)', '#FFFFFF');
  // Header portal btn: rgba(255,255,255,0.10) over headerGlassWorst
  const headerPortalBg = compositeSurface('rgba(255, 255, 255, 0.10)', headerGlassWorst);
  // Hero eyebrow pill: rgba(226,217,198,0.18) over #55719b (lightest stop of .gradient-bg)
  const eyebrowDarkBg = compositeSurface('rgba(226, 217, 198, 0.18)', '#55719B');
  // Footer map card: rgba(255,255,255,0.10) over #55719b (lightest stop of .gradient-bg)
  const footerCardBg = compositeSurface('rgba(255, 255, 255, 0.10)', '#55719B');
  // Footer consult btn hover: rgba(255,255,255,0.14) over #55719b
  const footerConsultHoverBg = compositeSurface('rgba(255, 255, 255, 0.14)', '#55719B');

  return [
    // Header
    { id: 'header-brand-sub', label: 'زیرتیتر هدر (text-white/80 روی شیشه هدر بالای صفحه سفید)', fg: 'rgba(255, 255, 255, 0.80)', bg: [headerGlassWorst, '#445D84'], min: 4.5 },
    { id: 'header-brand-accent', label: 'کلمه «فامو» در هدر (#E2D9C6 روی شیشه هدر بالای صفحه سفید)', fg: '#E2D9C6', bg: [headerGlassWorst, '#445D84'], min: 3.0 },
    { id: 'header-nav-link', label: 'لینک منو دسکتاپ/موبایل (#FFFFFF روی شیشه هدر بالای صفحه سفید)', fg: '#FFFFFF', bg: [headerGlassWorst, '#445D84'], min: 4.5 },
    { id: 'header-nav-hover', label: 'لینک منو حالت hover (#E2D9C6 روی شیشه هدر)', fg: '#E2D9C6', bg: [headerGlassWorst, '#445D84'], min: 4.5 },
    { id: 'header-cta-primary', label: 'دکمه «درخواست مشاوره» هدر (#445D84 روی #E2D9C6)', fg: '#445D84', bg: ['#E2D9C6'], min: 4.5 },
    { id: 'header-portal-btn', label: 'دکمه «ورود به پنل» هدر (#FFFFFF روی سطح شیشه‌ای)', fg: '#FFFFFF', bg: [headerPortalBg], min: 4.5 },

    // Hero & .gradient-bg (#445D84 -> #55719B)
    { id: 'hero-eyebrow', label: 'برچسب بالای هیرو (.famo-eyebrow روی .gradient-bg)', fg: '#E2D9C6', bg: [eyebrowDarkBg, '#55719B'], min: 4.5 },
    { id: 'hero-title', label: 'تیتر اصلی هیرو H1 (#FFFFFF روی .gradient-bg)', fg: '#FFFFFF', bg: ['#445D84', '#55719B'], min: 3.0 },
    { id: 'hero-title-accent', label: 'زیرتیتر H1 هیرو (#E2D9C6 روی .gradient-bg)', fg: '#E2D9C6', bg: ['#445D84', '#55719B'], min: 3.0 },
    { id: 'hero-desc', label: 'توضیح هیرو (rgba(255,255,255,0.88) روی .gradient-bg)', fg: 'rgba(255, 255, 255, 0.88)', bg: ['#445D84', '#55719B'], min: 4.5 },
    { id: 'hero-cta-primary', label: 'دکمه اصلی هیرو و CTA پایانی (#445D84 روی #E2D9C6)', fg: '#445D84', bg: ['#E2D9C6'], min: 4.5 },
    { id: 'hero-cta-secondary', label: 'دکمه دوم هیرو و CTA پایانی (#E2D9C6 روی .gradient-bg)', fg: '#E2D9C6', bg: ['#445D84', '#55719B'], min: 4.5 },
    { id: 'cta-final-desc', label: 'توضیح CTA پایانی (text-white/90 روی .gradient-bg)', fg: 'rgba(255, 255, 255, 0.90)', bg: ['#445D84', '#55719B'], min: 4.5 },

    // Light sections (#FFFFFF, #F9F7F3, #EFE9DD, #EFECE4)
    { id: 'eyebrow-light', label: 'برچسب سکشن روشن (.famo-eyebrow--light روی #F9F7F3)', fg: '#5B4D46', bg: ['#F9F7F3', '#FFFFFF'], min: 4.5 },
    { id: 'heading-light', label: 'تیتر H2/H3 سکشن روشن (#445D84 روی #F9F7F3/#FFFFFF)', fg: '#445D84', bg: ['#F9F7F3', '#FFFFFF'], min: 3.0 },
    { id: 'body-gray-600', label: 'متن بدنه (text-gray-600 روی #F9F7F3/#FFFFFF)', fg: '#4B5563', bg: ['#F9F7F3', '#FFFFFF'], min: 4.5 },
    { id: 'inst-title', label: 'عنوان شغلی استاد (.inst-title: #736158 روی #FFFFFF)', fg: '#736158', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'inst-bio', label: 'توضیح کارت استاد (.inst-bio: #525252 روی #FFFFFF)', fg: '#525252', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'course-desc', label: 'توضیح کارت دوره (.famo-course-desc: #737373 روی #FFFFFF)', fg: '#737373', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'course-meta-chip', label: 'برچسب متای دوره (#445D84 روی #EFECE4)', fg: '#445D84', bg: ['#EFECE4'], min: 4.5 },
    { id: 'course-empty', label: 'پیام خالی/خطای دوره‌ها (#737373 روی #EFECE4)', fg: '#737373', bg: ['#EFECE4'], min: 4.5 },
    { id: 'btn-primary-solid', label: 'دکمه توپر آبی (#FFFFFF روی #445D84)', fg: '#FFFFFF', bg: ['#445D84'], min: 4.5 },
    { id: 'btn-primary-hover', label: 'دکمه توپر آبی hover (#FFFFFF روی #344868)', fg: '#FFFFFF', bg: ['#344868'], min: 4.5 },
    { id: 'faq-icon', label: 'آیکون آکاردئون FAQ (.faq-icon: #8B786D روی #FFFFFF)', fg: '#8B786D', bg: ['#FFFFFF'], min: 3.0 },
    { id: 'faq-link', label: 'لینک پایین FAQ (#445D84 روی #F9F7F3)', fg: '#445D84', bg: ['#F9F7F3'], min: 4.5 },
    { id: 'mock-subtitle', label: 'زیرتیتر شماتیک کارنامه (text-gray-500 #6B7280 روی #FFFFFF)', fg: '#6B7280', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'mock-caption', label: 'پاورقی شماتیک کارنامه (.famo-mock-caption #6B7280 روی #FFFFFF)', fg: '#6B7280', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'mock-badge', label: 'برچسب «اطلاع‌رسانی پیامکی» (#445D84 روی #EFE9DD)', fg: '#445D84', bg: ['#EFE9DD'], min: 4.5 },

    // Footer (.gradient-bg)
    { id: 'footer-heading', label: 'تیتر ستون‌های فوتر (#E2D9C6 روی .gradient-bg)', fg: '#E2D9C6', bg: ['#445D84', '#55719B'], min: 3.0 },
    { id: 'footer-links', label: 'لینک‌های فوتر (text-white/80 روی .gradient-bg)', fg: 'rgba(255, 255, 255, 0.80)', bg: ['#445D84', '#55719B'], min: 4.5 },
    { id: 'footer-desc', label: 'متن معرفی و تماس فوتر (text-white/85 روی .gradient-bg)', fg: 'rgba(255, 255, 255, 0.85)', bg: ['#445D84', '#55719B'], min: 4.5 },
    { id: 'footer-map-text', label: 'متن کارت نقشه فوتر (text-white/85 روی rgba(255,255,255,0.1))', fg: 'rgba(255, 255, 255, 0.85)', bg: [footerCardBg], min: 4.5 },
    { id: 'footer-map-link', label: 'لینک نقشه فوتر (#E2D9C6 روی کارت شیشه‌ای فوتر)', fg: '#E2D9C6', bg: [footerCardBg], min: 4.5 },
    { id: 'footer-consult-hover', label: 'دکمه مشاوره فوتر حالت hover (#FFFFFF روی rgba(255,255,255,0.14))', fg: '#FFFFFF', bg: [footerConsultHoverBg], min: 4.5 },
    { id: 'footer-credit', label: 'نام مدیر و طراح در فوتر (#E2D9C6 روی .gradient-bg)', fg: '#E2D9C6', bg: ['#445D84', '#55719B'], min: 4.5 },
  ];
}

export function getAfterPairs() {
  const { vars } = extractStyleVars();
  const brandDark = vars['--famo-brand-dark'] || '#334869';
  const brand = vars['--famo-brand'] || '#445D84';
  const brandGradEnd = vars['--famo-brand-grad-end'] || '#445D84';
  const brandGradStart = vars['--famo-brand-grad-start'] || '#354B6E';
  const creamOnDark = vars['--famo-cream-on-dark'] || '#F3ECDD';
  const textOnDark = vars['--famo-text-on-dark'] || '#FFFFFF';
  const textOnDarkSoft = vars['--famo-text-on-dark-soft'] || '#F1F5F9';
  const mutedOnLight = vars['--famo-muted-on-light'] || '#4B5563';
  const accentOnLight = vars['--famo-accent-on-light'] || '#63534B';
  const inkOnCream = vars['--famo-ink-on-cream'] || '#2F4261';
  const eyebrowDarkSurface = compositeSurface('rgba(24, 36, 56, 0.34)', brandGradEnd);
  const headerGlassWorst = compositeSurface('rgba(46, 65, 96, 0.88)', '#FFFFFF');
  const headerPortalBg = compositeSurface('rgba(24, 36, 56, 0.30)', headerGlassWorst);
  const footerCardBg = compositeSurface('rgba(24, 36, 56, 0.28)', brandGradEnd);
  const footerConsultHoverBg = compositeSurface('rgba(24, 36, 56, 0.32)', brandGradEnd);

  return [
    { id: 'header-brand-sub', label: 'زیرتیتر هدر روی شیشه هدر بالای صفحه سفید', fg: textOnDarkSoft, bg: [headerGlassWorst, brandDark], min: 4.5 },
    { id: 'header-brand-accent', label: 'کلمه «فامو» در هدر روی شیشه هدر بالای صفحه سفید', fg: creamOnDark, bg: [headerGlassWorst, brandDark], min: 3.0 },
    { id: 'header-nav-link', label: 'لینک منو دسکتاپ/موبایل روی شیشه هدر', fg: textOnDark, bg: [headerGlassWorst, brandDark], min: 4.5 },
    { id: 'header-nav-hover', label: 'لینک منو حالت hover روی شیشه هدر', fg: creamOnDark, bg: [headerGlassWorst, brandDark], min: 4.5 },
    { id: 'header-cta-primary', label: 'دکمه «درخواست مشاوره» هدر روی #E2D9C6', fg: inkOnCream, bg: ['#E2D9C6'], min: 4.5 },
    { id: 'header-portal-btn', label: 'دکمه «ورود به پنل» هدر', fg: textOnDark, bg: [headerPortalBg], min: 4.5 },

    { id: 'hero-eyebrow', label: 'برچسب بالای هیرو (.famo-eyebrow روی .gradient-bg)', fg: creamOnDark, bg: [eyebrowDarkSurface, brandGradEnd], min: 4.5 },
    { id: 'hero-title', label: 'تیتر اصلی هیرو H1 روی .gradient-bg', fg: textOnDark, bg: [brandGradStart, brandGradEnd], min: 3.0 },
    { id: 'hero-title-accent', label: 'زیرتیتر H1 هیرو روی .gradient-bg', fg: creamOnDark, bg: [brandGradStart, brandGradEnd], min: 3.0 },
    { id: 'hero-desc', label: 'توضیح هیرو روی .gradient-bg', fg: textOnDarkSoft, bg: [brandGradStart, brandGradEnd], min: 4.5 },
    { id: 'hero-cta-primary', label: 'دکمه اصلی هیرو و CTA پایانی روی #E2D9C6', fg: inkOnCream, bg: ['#E2D9C6'], min: 4.5 },
    { id: 'hero-cta-secondary', label: 'دکمه دوم هیرو و CTA پایانی روی .gradient-bg', fg: creamOnDark, bg: [brandGradStart, brandGradEnd], min: 4.5 },
    { id: 'cta-final-desc', label: 'توضیح CTA پایانی روی .gradient-bg', fg: textOnDarkSoft, bg: [brandGradStart, brandGradEnd], min: 4.5 },

    { id: 'eyebrow-light', label: 'برچسب سکشن روشن (.famo-eyebrow--light روی #F9F7F3)', fg: '#5B4D46', bg: ['#F9F7F3', '#FFFFFF'], min: 4.5 },
    { id: 'heading-light', label: 'تیتر H2/H3 سکشن روشن روی #F9F7F3/#FFFFFF', fg: brand, bg: ['#F9F7F3', '#FFFFFF'], min: 3.0 },
    { id: 'body-gray-600', label: 'متن بدنه روی #F9F7F3/#FFFFFF', fg: '#4B5563', bg: ['#F9F7F3', '#FFFFFF'], min: 4.5 },
    { id: 'inst-title', label: 'عنوان شغلی استاد (.inst-title روی #FFFFFF)', fg: accentOnLight, bg: ['#FFFFFF'], min: 4.5 },
    { id: 'inst-bio', label: 'توضیح کارت استاد (.inst-bio روی #FFFFFF)', fg: '#525252', bg: ['#FFFFFF'], min: 4.5 },
    { id: 'course-desc', label: 'توضیح کارت دوره (.famo-course-desc روی #FFFFFF)', fg: mutedOnLight, bg: ['#FFFFFF'], min: 4.5 },
    { id: 'course-meta-chip', label: 'برچسب متای دوره روی #EFECE4', fg: inkOnCream, bg: ['#EFECE4'], min: 4.5 },
    { id: 'course-empty', label: 'پیام خالی/خطای دوره‌ها روی #EFECE4', fg: mutedOnLight, bg: ['#EFECE4'], min: 4.5 },
    { id: 'btn-primary-solid', label: 'دکمه توپر آبی روی #445D84', fg: '#FFFFFF', bg: [brand], min: 4.5 },
    { id: 'btn-primary-hover', label: 'دکمه توپر آبی hover روی #334869', fg: '#FFFFFF', bg: [brandDark], min: 4.5 },
    { id: 'faq-icon', label: 'آیکون آکاردئون FAQ روی #FFFFFF', fg: accentOnLight, bg: ['#FFFFFF'], min: 3.0 },
    { id: 'faq-link', label: 'لینک پایین FAQ روی #F9F7F3', fg: brand, bg: ['#F9F7F3'], min: 4.5 },
    { id: 'mock-subtitle', label: 'زیرتیتر شماتیک کارنامه روی #FFFFFF', fg: mutedOnLight, bg: ['#FFFFFF'], min: 4.5 },
    { id: 'mock-caption', label: 'پاورقی شماتیک کارنامه روی #FFFFFF', fg: mutedOnLight, bg: ['#FFFFFF'], min: 4.5 },
    { id: 'mock-badge', label: 'برچسب «اطلاع‌رسانی پیامکی» روی #EFE9DD', fg: inkOnCream, bg: ['#EFE9DD'], min: 4.5 },

    { id: 'footer-heading', label: 'تیتر ستون‌های فوتر روی .gradient-bg', fg: creamOnDark, bg: [brandGradStart, brandGradEnd], min: 3.0 },
    { id: 'footer-links', label: 'لینک‌های فوتر روی .gradient-bg', fg: textOnDarkSoft, bg: [brandGradStart, brandGradEnd], min: 4.5 },
    { id: 'footer-desc', label: 'متن معرفی و تماس فوتر روی .gradient-bg', fg: textOnDarkSoft, bg: [brandGradStart, brandGradEnd], min: 4.5 },
    { id: 'footer-map-text', label: 'متن کارت نقشه فوتر', fg: textOnDarkSoft, bg: [footerCardBg], min: 4.5 },
    { id: 'footer-map-link', label: 'لینک نقشه فوتر', fg: creamOnDark, bg: [footerCardBg], min: 4.5 },
    { id: 'footer-consult-hover', label: 'دکمه مشاوره فوتر حالت hover', fg: textOnDark, bg: [footerConsultHoverBg], min: 4.5 },
    { id: 'footer-credit', label: 'نام مدیر و طراح در فوتر روی .gradient-bg', fg: creamOnDark, bg: [brandGradStart, brandGradEnd], min: 4.5 },
  ];
}

export function evaluatePairs(pairs) {
  return pairs.map((item) => {
    const { ratio, worstStop } = worstContrast(item.fg, item.bg);
    const pass = ratio >= item.min;
    return {
      ...item,
      worstBg: worstStop,
      ratio: Number(ratio.toFixed(2)),
      pass,
    };
  });
}

if (process.argv[1] && path.resolve(process.argv[1]) === __filename) {
  const modeArg = process.argv.includes('--mode')
    ? process.argv[process.argv.indexOf('--mode') + 1]
    : 'before';
  const pairs = modeArg === 'after' ? getAfterPairs() : getBeforePairs();
  const results = evaluatePairs(pairs);
  const fails = results.filter((r) => !r.pass);

  console.log(`\n=== WCAG AA Contrast Audit (${modeArg.toUpperCase()}) ===`);
  console.log('| ID | المان | رنگ متن | پس‌زمینه (بدترین حالت) | نسبت | حداقل | وضعیت |');
  console.log('|---|---|---|---|---|---|---|');
  for (const r of results) {
    console.log(
      `| ${r.id} | ${r.label} | \`${r.fg}\` | \`${r.worstBg}\` | **${r.ratio}:1** | ${r.min}:1 | ${r.pass ? 'قبول ✅' : 'رد ❌'} |`,
    );
  }
  console.log(`\nSummary: ${results.length - fails.length}/${results.length} passed, ${fails.length} failed.\n`);
  if (modeArg === 'after' && fails.length > 0) {
    process.exitCode = 1;
  }
}
