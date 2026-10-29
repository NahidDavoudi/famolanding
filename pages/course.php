<?php $base = '../'; require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دوره‌های تخصصی کنکور و تیزهوشان بابل | آموزشگاه فامو</title>
    <meta name="description"
        content="دوره‌های جامع آمادهگی کنکور تجربی، ریاضی، انسانی و آزمون‌های تیزهوشان در بابل. کلاس‌های مفهومی و تست‌زنی با اساتید مدال‌آور المپیاد و رتبه‌های برتر.">
    <meta name="keywords"
        content="کلاس کنکور بابل, دوره تیزهوشان بابل, کنکور تجربی بابل, مشاوره کنکور ریاضی, آموزشگاه کنکور فامو">

    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="دوره‌های تخصصی کنکور و تیزهوشان | آموزشگاه فامو بابل">
    <meta property="og:description" content="برنامه‌ریزی، آموزش مفهومی و آزمون‌های تخصصی کنکور و تیزهوشان در بابل.">
    <meta property="og:image" content="<?php echo famo_asset('images/logo.png', $base . '../shared/images/logo.png'); ?>">

    <!-- Course Schema Markup -->
    <script type="application/ld+json" id="courseSchema">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "دوره‌های آموزشی فامو بابل",
      "itemListElement": []
    }
    </script>

    <?php include '../layouts/head-common.php'; ?>

    <!-- استایل کارت‌های دوره: تصویر در بالا، نام و قیمت زیر آن، چیدمان گرید در لپ‌تاپ -->
    <style>
        /* گرید دوره‌ها: موبایل ۱ ستون، تبلت ۲ ستون، لپ‌تاپ ۳ ستون */
        #courses .courses-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            align-items: stretch;
        }

        @media (min-width: 640px) {
            #courses .courses-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
            }
        }

        @media (min-width: 1024px) {
            #courses .courses-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 28px;
            }
        }

        /* ---------- کارت دوره ---------- */
        #courses .course-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            background: #ffffff;
            border: 1px solid #E2D9C6;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -12px rgba(68, 93, 132, 0.35);
            transition: transform .3s ease, box-shadow .3s ease;
            scroll-margin-top: 100px;
        }

        #courses .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -16px rgba(68, 93, 132, 0.5);
        }

        /* تصویر دوره: نسبت ثابت 16/10 (بدون overlay) */
        #courses .course-image-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            flex: 0 0 auto;
            overflow: hidden;
        }

        #courses .course-image-wrapper img,
        #courses .course-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform .5s ease;
        }

        #courses .course-card:hover .course-image {
            transform: scale(1.05);
        }

        #courses .course-image-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #courses .course-badge {
            position: absolute;
            top: 12px;
            inset-inline-start: 12px;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            color: #445D84;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.9;
            padding: 2px 12px;
            border-radius: 9999px;
            box-shadow: 0 4px 10px rgba(68, 93, 132, 0.15);
        }

        /* بدنه کارت */
        #courses .course-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-width: 0;
            padding: 16px 18px 18px;
        }

        #courses .course-name {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #445D84;
            line-height: 1.8;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #courses .course-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 10px;
            margin-top: 8px;
            font-size: 12px;
            color: #736158;
        }

        #courses .course-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        #courses .course-desc {
            margin: 10px 0 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.9;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #courses .course-features {
            margin: 12px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            font-size: 12.5px;
            color: #4b5563;
        }

        #courses .course-features li {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        #courses .course-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #E2D9C6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        #courses .course-price-box {
            display: flex;
            flex-direction: column;
            line-height: 1.4;
        }

        #courses .course-price-label {
            font-size: 11px;
            color: #6b7280;
        }

        #courses .course-price-value {
            font-size: 16px;
            font-weight: 700;
            color: #445D84;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        #courses .course-price-unit {
            font-size: 11px;
            font-weight: 500;
            color: #736158;
            margin-inline-start: 4px;
        }

        #courses .course-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #445D84;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 16px;
            border-radius: 10px;
            text-decoration: none;
            white-space: nowrap;
            transition: background .25s ease;
        }

        #courses .course-cta:hover {
            background: #344868;
        }

        #courses .course-cta:focus-visible {
            outline: 2px solid #445D84;
            outline-offset: 2px;
        }

        /* ---------- اسکلتون لودینگ هم‌شکل با کارت ---------- */
        #courses .course-card-skeleton {
            background: var(--skeleton-bg, #f5f5f5);
            border: 1px solid var(--skeleton-border, #e0e0e0);
            border-radius: 16px;
            box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.08);
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
        }

        #courses .course-card-skeleton .skeleton-img {
            aspect-ratio: 16 / 10;
            margin: -16px -18px 6px;
            border-radius: 0;
        }

        #courses .course-card-skeleton .skeleton-line-lg {
            height: 15px;
            width: 75%;
        }

        #courses .course-card-skeleton .skeleton-line {
            height: 11px;
            width: 100%;
        }

        #courses .course-card-skeleton .skeleton-line-sm {
            height: 11px;
            width: 60%;
        }

        #courses .course-card-skeleton .skeleton-price {
            height: 16px;
            width: 45%;
            margin-top: 6px;
            align-self: flex-end;
        }
    </style>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<!-- Header -->
    <?php include '../layouts/header.php' ?>

<!-- Page Title Banner -->
<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <div class="container mx-auto px-4 max-w-5xl">
        <h1 class="text-3xl sm:text-4xl font-bold mb-6 text-white">دوره‌های تخصصی کنکور و تیزهوشان بابل</h1>

        <!-- بنر یا پوستر با ارتفاع کنترل‌شده و گوشه‌های گرد -->
        <div class="relative w-full h-56 sm:h-72 md:h-96 rounded-2xl overflow-hidden shadow-2xl border border-white/20 mb-8">
            <img src="<?php echo $base; ?>assets/images/courses_banner.webp" alt="دوره‌های تخصصی کنکور و تیزهوشان بابل"
                width="2752" height="1536" decoding="async" class="w-full h-full object-cover">
        </div>

        <p class="text-white/90 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">برنامه‌ریزی، کادر مجرب، آزمون‌های استاندارد
            و تحلیل پیشرفت فردی برای تضمین موفقیت تحصیلی</p>
    </div>
</section>

<!-- Detailed Courses Grid -->
<section id="courses" class="py-16 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="courses-grid" id="coursesContainer">
            <!-- Skeleton loaders shown until the API responds -->
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
    <?php include '../layouts/footer.php'?>


<script type="module" src="<?php echo $base; ?>assets/js/courses.js"></script>
</body>
</html>
