<?php $base = '../'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وبلاگ | آموزشگاه فامو بابل</title>
    <meta name="description" content="مطالب آموزشی وبلاگ آموزشگاه فامو بابل">
    <?php include '../layouts/head-common.php'; ?>
</head>
<body class="font-family text-gray-800" data-page="blog-index">

<!-- Header -->
<?php include '../layouts/header.php' ?>
<!-- Blog Index -->
<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-white">وبلاگ ها و مقالات آموزشگاه فامو</h1>
    <div class="pt-10 lg:pt-20 container mx-auto px-4">
        <p class="text-white/80 max-w-2xl mx-auto text-base sm:text-lg">برنامه‌ریزی، کادر مجرب، آزمون‌های استاندارد
            و تحلیل پیشرفت فردی برای تضمین موفقیت تحصیلی</p>
    </div>
</section>

<!-- Category Badges -->
<section class="py-8 px-4">
    <div class="container mx-auto max-w-6xl">
        <div id="categoryFilters" class="flex flex-wrap justify-center gap-2"></div>
    </div>
</section>
<!-- Detailed Courses List -->
<section class="py-8 px-4">
    <div class="container mx-auto max-w-6xl space-y-12" id="postsContainer">
        <!-- Skeleton loaders shown until the API responds -->
        <div
            class="course-skeleton blog-skeleton bg-white rounded-2xl shadow-xl border border-[#E2D9C6] p-6 sm:p-8 flex flex-col md:flex-row gap-8 items-center animate-pulse">
            <div class="md:w-2/3 space-y-4 w-full">
                <div class="h-5 w-32 bg-gray-200 rounded-full"></div>
                <div class="h-7 w-2/3 bg-gray-200 rounded"></div>
                <div class="h-4 w-full bg-gray-200 rounded"></div>
                <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
            </div>
            <div class="md:w-1/3 w-full h-40 bg-gray-100 rounded-xl"></div>
        </div>
        <div
            class="course-skeleton blog-skeleton bg-white rounded-2xl shadow-xl border border-[#E2D9C6] p-6 sm:p-8 flex flex-col md:flex-row gap-8 items-center animate-pulse">
            <div class="md:w-2/3 space-y-4 w-full">
                <div class="h-5 w-32 bg-gray-200 rounded-full"></div>
                <div class="h-7 w-2/3 bg-gray-200 rounded"></div>
                <div class="h-4 w-full bg-gray-200 rounded"></div>
                <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
            </div>
            <div class="md:w-1/3 w-full h-40 bg-gray-100 rounded-xl"></div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include '../layouts/footer.php' ?>

<script type="module" src="<?php echo $base; ?>assets/js/blog.js"></script>
</body>
</html>