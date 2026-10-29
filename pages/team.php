<?php $base = '../'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اساتید | آموزشگاه فامو بابل</title>
    <meta name="description"
        content="معرفی اساتید برجسته کنکور، رتبه‌های برتر المپیاد و تخصص مشاوران و پشتیبانان آموزشی موسسه فامو در بابل.">
    <?php include '../layouts/head-common.php'; ?>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<!-- Header -->
    <?php include '../layouts/header.php' ?>

<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">کادر آموزشی و مشاوران فامو بابل</h1>
        <p class="text-white/80 max-w-2xl mx-auto">همراهی اساتید مجرب و پشتیبانان رتبه برتر کنکور در تمامی مراحل
            تحصیلی</p>
    </div>
</section>

<!-- Instructors Section -->
<section class="py-16 px-4">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">اساتید تخصصی کنکور و
            تیزهوشان</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="instructorsContainer">          
        </div>
    </div>
</section>

<!-- Support Team Section -->
<section id="support" class="py-16 px-4 bg-white">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">تیم پشتیبانی ۲۴/۷</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="supportersContainer">
            <!-- Skeleton loaders shown until the API responds -->
            <div class="bg-[#f9f7f3] rounded-2xl p-6 border border-[#E2D9C6] flex items-center gap-4 animate-pulse">
                <div class="w-16 h-16 bg-gray-200 rounded-full shrink-0"></div>
                <div class="space-y-2 w-full">
                    <div class="h-4 w-2/3 bg-gray-200 rounded"></div>
                    <div class="h-3 w-1/2 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- footer -->
    <?php include '../layouts/footer.php'?>

<script type="module" src="<?php echo $base; ?>assets/js/team.js"></script>
</body>
</html>