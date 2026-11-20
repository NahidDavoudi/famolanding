<?php
declare(strict_types=1);

// No login, no PHP session, no JWT cookies.
header('Content-Type: text/html; charset=UTF-8');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/config.php';

function famo_e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function famo_nl2br_escaped(mixed $value): string
{
    return nl2br(famo_e($value), false);
}

function famo_fa_digits(string $text): string
{
    return strtr($text, [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ]);
}

function famo_gregorian_to_jalali_str(string $ymd): string
{
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $ymd, $m) !== 1) {
        return $ymd;
    }
    $gy = (int) $m[1];
    $gm = (int) $m[2];
    $gd = (int) $m[3];
    if ($gm < 1 || $gm > 12 || $gd < 1 || $gd > 31) {
        return $ymd;
    }
    $gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
    $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd + $gdm[$gm - 1];
    $jy = -1595 + (33 * intdiv($days, 12053));
    $days %= 12053;
    $jy += 4 * intdiv($days, 1461);
    $days %= 1461;
    if ($days > 365) {
        $jy += intdiv($days - 1, 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + intdiv($days, 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + intdiv($days - 186, 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
}

$token = trim((string) ($_GET['token'] ?? ''));
$statusCode = 200;
$errorState = null; // null | 'not_found' | 'rate_limited' | 'unavailable'
$result = null;
$retryAfterSeconds = null;

if ($token === '' || preg_match('/^[A-Za-z0-9_-]{32,64}$/', $token) !== 1) {
    $statusCode = 404;
    $errorState = 'not_found';
} elseif (!function_exists('curl_init')) {
    error_log('[exam-result] cURL extension is not available');
    $statusCode = 503;
    $errorState = 'unavailable';
    $retryAfterSeconds = '30';
} else {
    $apiUrl = famo_internal_api_url() . '/public/exam-results/' . rawurlencode($token);
    $headers = ['Accept: application/json'];

    // Forward Apache mod_remoteip-verified client IP (never trust raw browser X-Forwarded-For directly)
    $clientIp = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    if ($clientIp !== '' && filter_var($clientIp, FILTER_VALIDATE_IP) !== false) {
        $headers[] = 'X-Forwarded-For: ' . $clientIp;
    }

    $upstreamRetryAfter = null;
    $ch = curl_init($apiUrl);
    if ($ch === false) {
        error_log('[exam-result] curl_init failed');
        $statusCode = 503;
        $errorState = 'unavailable';
        $retryAfterSeconds = '30';
    } else {
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS      => 0,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADERFUNCTION => static function ($curlHandle, string $headerLine) use (&$upstreamRetryAfter): int {
                $len = strlen($headerLine);
                $parts = explode(':', $headerLine, 2);
                if (count($parts) === 2 && strtolower(trim($parts[0])) === 'retry-after') {
                    $val = trim($parts[1]);
                    if (ctype_digit($val) && (int) $val > 0) {
                        $upstreamRetryAfter = $val;
                    }
                }
                return $len;
            },
        ]);

        $rawBody = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatus >= 300 && $httpStatus < 400) {
            error_log(sprintf('[exam-result] upstream redirect blocked status=%d', $httpStatus));
            $statusCode = 503;
            $errorState = 'unavailable';
            $retryAfterSeconds = '30';
        } elseif ($rawBody === false || $curlErrno !== 0 || $httpStatus === 0 || $httpStatus >= 500) {
            error_log(sprintf('[exam-result] upstream request failed curl_errno=%d status=%d', $curlErrno, $httpStatus));
            $statusCode = 503;
            $errorState = 'unavailable';
            $retryAfterSeconds = $upstreamRetryAfter ?? '30';
        } elseif ($httpStatus === 404) {
            $statusCode = 404;
            $errorState = 'not_found';
        } elseif ($httpStatus === 429) {
            $statusCode = 429;
            $errorState = 'rate_limited';
            $retryAfterSeconds = $upstreamRetryAfter ?? '60';
        } elseif ($httpStatus !== 200) {
            error_log(sprintf('[exam-result] unexpected upstream status=%d', $httpStatus));
            $statusCode = 503;
            $errorState = 'unavailable';
            $retryAfterSeconds = '30';
        } else {
            $decoded = json_decode((string) $rawBody, true);
            if (!is_array($decoded) || empty($decoded['success']) || !is_array($decoded['data'] ?? null)) {
                error_log('[exam-result] invalid JSON payload from upstream');
                $statusCode = 503;
                $errorState = 'unavailable';
                $retryAfterSeconds = '30';
            } else {
                // Strict field whitelist
                $raw = $decoded['data'];
                $studentRaw = is_array($raw['student'] ?? null) ? $raw['student'] : [];
                $subjectsRaw = is_array($raw['subjects'] ?? null) ? $raw['subjects'] : [];
                $commentsRaw = is_array($raw['comments'] ?? null) ? $raw['comments'] : [];

                $subjects = [];
                foreach ($subjectsRaw as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $chapterRaw = $row['chapter'] ?? null;
                    $chapterStr = ($chapterRaw !== null && trim((string) $chapterRaw) !== '') ? (string) $chapterRaw : null;
                    $subjects[] = [
                        'subject'    => (string) ($row['subject'] ?? ''),
                        'chapter'    => $chapterStr,
                        'total_q'    => (int) ($row['total_q'] ?? 0),
                        'correct'    => (int) ($row['correct'] ?? 0),
                        'wrong'      => (int) ($row['wrong'] ?? 0),
                        'skipped'    => (int) ($row['skipped'] ?? 0),
                        'percentage' => (float) ($row['percentage'] ?? 0.0),
                    ];
                }

                $roleLabels = [
                    'teacher'   => 'دبیر',
                    'supporter' => 'پشتیبان',
                    'admin'     => 'مدیریت آموزشی',
                ];
                $comments = [];
                foreach ($commentsRaw as $c) {
                    if (!is_array($c)) {
                        continue;
                    }
                    $body = (string) ($c['body'] ?? '');
                    if (trim($body) === '') {
                        continue;
                    }
                    $roleKey = (string) ($c['author_role'] ?? '');
                    $comments[] = [
                        'author_name' => (string) ($c['author_name'] ?? ''),
                        'author_role' => $roleLabels[$roleKey] ?? 'کادر آموزشی',
                        'body'        => $body,
                    ];
                }

                $gradeRaw = $studentRaw['grade'] ?? null;
                $gradeVal = ($gradeRaw !== null && $gradeRaw !== '') ? (int) $gradeRaw : null;
                $fieldRaw = $studentRaw['field'] ?? null;
                $fieldVal = ($fieldRaw !== null && trim((string) $fieldRaw) !== '') ? (string) $fieldRaw : null;

                $result = [
                    'student_name'   => (string) ($raw['student_name'] ?? ($studentRaw['name'] ?? 'دانش‌آموز')),
                    'grade'          => $gradeVal,
                    'field'          => $fieldVal,
                    'exam_date'      => (string) ($raw['exam_date'] ?? ''),
                    'avg_percentage' => (float) ($raw['avg_percentage'] ?? 0.0),
                    'subjects'       => $subjects,
                    'comments'       => $comments,
                ];
            }
        }
    }
}

if ($retryAfterSeconds !== null && ($statusCode === 429 || $statusCode === 503)) {
    header('Retry-After: ' . $retryAfterSeconds);
}
http_response_code($statusCode);
?><!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <meta name="color-scheme" content="light">
    <title>کارنامه آزمون | آموزشگاه فامو</title>
    <link rel="stylesheet" href="<?= famo_e(famo_asset('css/tokens.css', '/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= famo_e(famo_asset('css/fonts.css', '/assets/css/fonts.css')) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= famo_e(famo_asset('images/favicon-32.png', '/favicon.ico')) ?>">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f9f7f3;
            color: var(--ds-color-foreground, #1f2937);
            font-family: Vazirmatn, var(--ds-font-sans, system-ui, -apple-system, sans-serif);
            line-height: 1.6;
        }
        .exam-header {
            background-color: var(--ds-color-primary, #445d84);
            color: #ffffff;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(17, 29, 42, 0.14);
        }
        .exam-header-inner {
            max-width: 48rem;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .exam-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .exam-logo {
            width: 2.5rem;
            height: 2.5rem;
            object-fit: contain;
            border-radius: 9999px;
            background-color: rgba(255, 255, 255, 0.12);
            padding: 0.25rem;
            flex-shrink: 0;
        }
        .exam-brand-title {
            font-weight: 700;
            font-size: 1rem;
        }
        .exam-brand-sub {
            font-size: 0.75rem;
            color: var(--ds-color-border, #e2d9c6);
        }
        .exam-print-btn {
            background-color: var(--ds-color-border, #e2d9c6);
            color: var(--ds-color-primary, #445d84);
            border: none;
            font-family: inherit;
            font-size: 0.8125rem;
            font-weight: 700;
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: opacity 0.15s ease;
        }
        .exam-print-btn:hover {
            opacity: 0.9;
        }
        .exam-main {
            flex: 1 1 auto;
            width: 100%;
            max-width: 48rem;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }
        .exam-card {
            background-color: var(--ds-color-surface, #ffffff);
            border: 1px solid var(--ds-color-border, #e2d9c6);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(17, 29, 42, 0.06);
        }
        .exam-state-card {
            padding: 1.75rem 1.5rem;
            text-align: center;
        }
        .exam-state-title {
            margin: 0 0 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ds-color-primary, #445d84);
        }
        .exam-state-desc {
            margin: 0;
            color: var(--ds-color-neutral-600, #4b5563);
            font-size: 0.9375rem;
            line-height: 1.75;
        }
        .exam-summary {
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .exam-summary-inner {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .exam-student-name {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ds-color-primary, #445d84);
        }
        .exam-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.625rem;
            font-size: 0.8125rem;
            color: var(--ds-color-neutral-600, #4b5563);
        }
        .exam-badge {
            background-color: #f9f7f3;
            border: 1px solid var(--ds-color-border, #e2d9c6);
            padding: 0.25rem 0.625rem;
            border-radius: 0.5rem;
        }
        .exam-avg-box {
            background-color: var(--ds-color-primary, #445d84);
            color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            text-align: center;
            flex-shrink: 0;
        }
        .exam-avg-label {
            font-size: 0.75rem;
            color: var(--ds-color-border, #e2d9c6);
        }
        .exam-avg-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.125rem;
        }
        .exam-mobile-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .exam-subject-card {
            padding: 1rem;
        }
        .exam-subject-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .exam-subject-title {
            font-weight: 700;
            color: var(--ds-color-primary, #445d84);
        }
        .exam-subject-chapter {
            font-size: 0.75rem;
            color: var(--ds-color-muted-foreground, #6b7280);
            margin-top: 0.125rem;
        }
        .exam-subject-pct {
            background-color: #f9f7f3;
            color: var(--ds-color-primary, #445d84);
            border: 1px solid var(--ds-color-border, #e2d9c6);
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.25rem 0.625rem;
            border-radius: 0.5rem;
            white-space: nowrap;
        }
        .exam-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
            text-align: center;
            font-size: 0.75rem;
        }
        .exam-stat-cell {
            border-radius: 0.5rem;
            padding: 0.5rem 0.25rem;
        }
        .exam-stat-cell--total { background-color: #f9fafb; color: #374151; }
        .exam-stat-cell--correct { background-color: #ecfdf5; color: #047857; }
        .exam-stat-cell--wrong { background-color: #fff1f2; color: #be123c; }
        .exam-stat-cell--skipped { background-color: #fffbeb; color: #b45309; }
        .exam-stat-num {
            font-weight: 700;
            margin-top: 0.125rem;
        }
        .exam-table-wrap {
            display: none;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .exam-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: right;
        }
        .exam-table thead {
            background-color: #f9f7f3;
            color: var(--ds-color-primary, #445d84);
            border-bottom: 1px solid var(--ds-color-border, #e2d9c6);
        }
        .exam-table th, .exam-table td {
            padding: 0.875rem 0.75rem;
        }
        .exam-table th:first-child, .exam-table td:first-child,
        .exam-table th:last-child, .exam-table td:last-child {
            padding-inline: 1rem;
        }
        .exam-table tbody tr + tr {
            border-top: 1px solid #f3f4f6;
        }
        .exam-text-center { text-align: center; }
        .exam-col-subject { font-weight: 700; color: #1f2937; }
        .exam-col-correct { color: #047857; font-weight: 600; }
        .exam-col-wrong { color: #be123c; font-weight: 600; }
        .exam-col-skipped { color: #b45309; font-weight: 600; }
        .exam-col-pct { font-weight: 700; color: var(--ds-color-primary, #445d84); }
        .exam-comments {
            padding: 1.25rem;
        }
        .exam-comments-heading {
            margin: 0 0 1rem;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--ds-color-primary, #445d84);
        }
        .exam-comments-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .exam-comment-item {
            background-color: #f9f7f3;
            border: 1px solid var(--ds-color-border, #e2d9c6);
            border-radius: 0.75rem;
            padding: 1rem;
        }
        .exam-comment-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
        }
        .exam-comment-author {
            font-weight: 700;
            color: var(--ds-color-primary, #445d84);
        }
        .exam-comment-role {
            background-color: #ffffff;
            border: 1px solid var(--ds-color-border, #e2d9c6);
            color: var(--ds-color-neutral-600, #4b5563);
            padding: 0.125rem 0.5rem;
            border-radius: 0.375rem;
        }
        .exam-comment-body {
            margin: 0;
            font-size: 0.875rem;
            color: #374151;
            line-height: 1.7;
        }
        .exam-footer {
            text-align: center;
            font-size: 0.75rem;
            color: var(--ds-color-muted-foreground, #6b7280);
            padding: 1.25rem 1rem;
        }
        @media (min-width: 640px) {
            .exam-brand-title { font-size: 1.125rem; }
            .exam-print-btn { font-size: 0.875rem; }
            .exam-main { padding: 2rem 1rem; }
            .exam-summary { padding: 1.5rem; }
            .exam-summary-inner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            .exam-student-name { font-size: 1.5rem; }
            .exam-badges { font-size: 0.875rem; }
            .exam-comments { padding: 1.5rem; }
            .exam-state-card { padding: 2rem; }
        }
        @media (min-width: 768px) {
            .exam-mobile-list { display: none; }
            .exam-table-wrap { display: block; }
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff; }
            .exam-header {
                box-shadow: none;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .exam-card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <header class="exam-header">
        <div class="exam-header-inner">
            <div class="exam-brand">
                <img src="<?= famo_e(famo_asset('images/logo.png', '/assets/images/logo.png')) ?>" alt="آموزشگاه فامو" width="40" height="40" class="exam-logo">
                <div>
                    <div class="exam-brand-title">آموزشگاه فامو</div>
                    <div class="exam-brand-sub">گزارش اختصاصی نتیجه آزمون</div>
                </div>
            </div>
            <?php if ($result !== null): ?>
                <button type="button" onclick="window.print()" class="no-print exam-print-btn">
                    چاپ کارنامه
                </button>
            <?php endif; ?>
        </div>
    </header>

    <main class="exam-main">
        <?php if ($errorState === 'not_found'): ?>
            <div class="exam-card exam-state-card">
                <h1 class="exam-state-title">لینک کارنامه در دسترس نیست</h1>
                <p class="exam-state-desc">
                    این لینک نامعتبر است، منقضی شده یا توسط پشتیبان غیرفعال گردیده است.
                    در صورت نیاز، برای دریافت لینک جدید با واحد پشتیبانی آموزشگاه فامو تماس بگیرید.
                </p>
            </div>
        <?php elseif ($errorState === 'rate_limited'): ?>
            <div class="exam-card exam-state-card">
                <h1 class="exam-state-title">تعداد درخواستها بیش از حد مجاز است</h1>
                <p class="exam-state-desc">لطفاً حدود یک دقیقه صبر کرده و دوباره صفحه را بارگذاری کنید.</p>
            </div>
        <?php elseif ($errorState === 'unavailable'): ?>
            <div class="exam-card exam-state-card">
                <h1 class="exam-state-title">سرویس موقتاً در دسترس نیست</h1>
                <p class="exam-state-desc">دریافت اطلاعات کارنامه با اختلال موقت مواجه شد. لطفاً دقایقی دیگر دوباره تلاش کنید.</p>
            </div>
        <?php elseif ($result !== null): ?>
            <!-- Student & Exam Summary -->
            <section class="exam-card exam-summary">
                <div class="exam-summary-inner">
                    <div>
                        <h1 class="exam-student-name"><?= famo_e($result['student_name']) ?></h1>
                        <div class="exam-badges">
                            <?php if ($result['grade'] !== null): ?>
                                <span class="exam-badge">پایه <?= famo_e(famo_fa_digits((string) $result['grade'])) ?></span>
                            <?php endif; ?>
                            <?php if ($result['field'] !== null): ?>
                                <span class="exam-badge">رشته <?= famo_e($result['field']) ?></span>
                            <?php endif; ?>
                            <span class="exam-badge">
                                تاریخ آزمون: <?= famo_e(famo_fa_digits(famo_gregorian_to_jalali_str($result['exam_date']))) ?>
                            </span>
                        </div>
                    </div>
                    <div class="exam-avg-box">
                        <div class="exam-avg-label">میانگین درصد کل</div>
                        <div class="exam-avg-value" dir="ltr"><?= famo_e(famo_fa_digits(number_format($result['avg_percentage'], 1, '.', ''))) ?>%</div>
                    </div>
                </div>
            </section>

            <!-- Subjects Mobile Cards -->
            <section class="exam-mobile-list">
                <?php foreach ($result['subjects'] as $subj): ?>
                    <div class="exam-card exam-subject-card">
                        <div class="exam-subject-top">
                            <div>
                                <div class="exam-subject-title"><?= famo_e($subj['subject']) ?></div>
                                <?php if ($subj['chapter'] !== null): ?>
                                    <div class="exam-subject-chapter"><?= famo_e($subj['chapter']) ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="exam-subject-pct" dir="ltr">
                                <?= famo_e(famo_fa_digits(number_format($subj['percentage'], 1, '.', ''))) ?>%
                            </span>
                        </div>
                        <div class="exam-stats-grid">
                            <div class="exam-stat-cell exam-stat-cell--total"><div>کل</div><div class="exam-stat-num"><?= famo_e(famo_fa_digits((string) $subj['total_q'])) ?></div></div>
                            <div class="exam-stat-cell exam-stat-cell--correct"><div>صحیح</div><div class="exam-stat-num"><?= famo_e(famo_fa_digits((string) $subj['correct'])) ?></div></div>
                            <div class="exam-stat-cell exam-stat-cell--wrong"><div>غلط</div><div class="exam-stat-num"><?= famo_e(famo_fa_digits((string) $subj['wrong'])) ?></div></div>
                            <div class="exam-stat-cell exam-stat-cell--skipped"><div>نزده</div><div class="exam-stat-num"><?= famo_e(famo_fa_digits((string) $subj['skipped'])) ?></div></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- Subjects Desktop Table -->
            <section class="exam-card exam-table-wrap">
                <table class="exam-table">
                    <thead>
                        <tr>
                            <th>درس / مبحث</th>
                            <th class="exam-text-center">تعداد سوال</th>
                            <th class="exam-text-center">صحیح</th>
                            <th class="exam-text-center">غلط</th>
                            <th class="exam-text-center">نزده</th>
                            <th class="exam-text-center">درصد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['subjects'] as $subj): ?>
                            <tr>
                                <td>
                                    <div class="exam-col-subject"><?= famo_e($subj['subject']) ?></div>
                                    <?php if ($subj['chapter'] !== null): ?>
                                        <div class="exam-subject-chapter"><?= famo_e($subj['chapter']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="exam-text-center"><?= famo_e(famo_fa_digits((string) $subj['total_q'])) ?></td>
                                <td class="exam-text-center exam-col-correct"><?= famo_e(famo_fa_digits((string) $subj['correct'])) ?></td>
                                <td class="exam-text-center exam-col-wrong"><?= famo_e(famo_fa_digits((string) $subj['wrong'])) ?></td>
                                <td class="exam-text-center exam-col-skipped"><?= famo_e(famo_fa_digits((string) $subj['skipped'])) ?></td>
                                <td class="exam-text-center exam-col-pct" dir="ltr"><?= famo_e(famo_fa_digits(number_format($subj['percentage'], 1, '.', ''))) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Comments -->
            <?php if (count($result['comments']) > 0): ?>
                <section class="exam-card exam-comments">
                    <h2 class="exam-comments-heading">تحلیل و توضیحات دبیر و پشتیبان</h2>
                    <div class="exam-comments-list">
                        <?php foreach ($result['comments'] as $comment): ?>
                            <article class="exam-comment-item">
                                <div class="exam-comment-meta">
                                    <span class="exam-comment-author"><?= famo_e($comment['author_name']) ?></span>
                                    <span class="exam-comment-role"><?= famo_e($comment['author_role']) ?></span>
                                </div>
                                <p class="exam-comment-body"><?= famo_nl2br_escaped($comment['body']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer class="exam-footer">
        آموزشگاه فامو — مشاوره تخصصی کنکور و تیزهوشان
    </footer>
</body>
</html>
