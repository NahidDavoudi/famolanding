<?php $base='../'; require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مقالات وبلاگ | آموزشگاه فامو</title>
    <meta name="description" content="مطالب آموزشی وبلاگ آموزشگاه فامو بابل">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="مقالات وبلاگ | آموزشگاه فامو">
    <meta property="og:description" content="مطالب آموزشی وبلاگ آموزشگاه فامو بابل">
    <meta property="og:image" content="<?php echo famo_asset('images/logo.png', $base . '../shared/images/logo.png'); ?>">

    <!-- Structured Data - updated dynamically per post by blog.js -->
    <script type="application/ld+json" id="blogPostingSchema"></script>

    <?php include '../layouts/head-common.php'; ?>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3] min-h-screen flex flex-col" data-page="blog-post">

    <!-- Header -->
<?php include '../layouts/header.php' ?>

    <!-- Article Container -->
    <section class="pt-28 sm:pt-32 pb-16 px-4">
        <div class="container mx-auto max-w-4xl">
            <!-- Skeleton -->
            <div id="postContainer">
                <div class="blog-skeleton animate-pulse">
                    <div class="bg-white rounded-3xl shadow-xl border border-[#E2D9C6] overflow-hidden">
                        <div class="h-56 sm:h-80 bg-gray-200"></div>
                        <div class="p-6 sm:p-10 space-y-4">
                            <div class="h-4 w-48 bg-gray-200 rounded"></div>
                            <div class="h-8 w-3/4 bg-gray-200 rounded"></div>
                            <div class="h-4 w-full bg-gray-200 rounded"></div>
                            <div class="h-4 w-full bg-gray-200 rounded"></div>
                            <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
                            <div class="h-4 w-2/3 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Footer -->
    <?php include '../layouts/footer.php' ?>


    <script type="module" src="../assets/js/blog.js"></script>
</body>
</html>
