<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/**
 * FontAwesome class → legacy sprite icon mapping (mirrors FA_ICON_MAP in assets/js/main.js).
 */
const FAMO_FA_ICON_MAP = [
    'fa-home'            => 'icon-home',
    'fa-book'            => 'icon-book',
    'fa-book-open'       => 'icon-book',
    'fa-school'          => 'icon-school',
    'fa-graduation-cap'  => 'icon-school',
    'fa-user-graduate'   => 'icon-school',
    'fa-chalkboard-teacher' => 'icon-school',
    'fa-chart-line'      => 'icon-chart-bar',
    'fa-chart-bar'       => 'icon-chart-bar',
    'fa-user'            => 'icon-user',
    'fa-users'           => 'icon-users',
    'fa-user-friends'    => 'icon-users',
    'fa-user-plus'       => 'icon-user-plus',
    'fa-calendar'        => 'icon-calendar',
    'fa-calendar-alt'    => 'icon-calendar',
    'fa-star'            => 'icon-star',
    'fa-headset'         => 'icon-headset',
    'fa-phone'           => 'icon-phone',
    'fa-phone-alt'       => 'icon-phone',
    'fa-envelope'        => 'icon-mail',
    'fa-search'          => 'icon-search',
    'fa-edit'            => 'icon-edit',
    'fa-pen'             => 'icon-edit',
    'fa-pencil-alt'      => 'icon-edit',
    'fa-trash'           => 'icon-trash',
    'fa-trash-alt'       => 'icon-trash',
    'fa-download'        => 'icon-download',
    'fa-upload'          => 'icon-upload',
    'fa-file'            => 'icon-file',
    'fa-file-alt'        => 'icon-file',
    'fa-eye'             => 'icon-eye',
    'fa-check'           => 'icon-check',
    'fa-times'           => 'icon-x',
    'fa-plus'            => 'icon-plus',
    'fa-cog'             => 'icon-settings',
    'fa-cogs'            => 'icon-settings',
    'fa-calculator'      => 'icon-chart-bar',
    'fa-flask'           => 'icon-report',
    'fa-atom'            => 'icon-star',
    'fa-brain'           => 'icon-school',
    'fa-clipboard'       => 'icon-clipboard',
    'fa-clock'           => 'icon-clock',
    'fa-map-marker-alt'  => 'icon-map-pin',
    'fa-filter'          => 'icon-filter',
    'fa-bars'            => 'icon-menu',
    'fa-question-circle' => 'icon-help-circle',
    'fa-microscope'      => 'icon-search',
    'fa-square-root-alt' => 'icon-chart-bar',
    'fa-language'        => 'icon-book',
    'fa-paint-brush'     => 'icon-edit',
    'fa-laptop-code'     => 'icon-settings',
    'fa-dna'             => 'icon-report',
    'fa-globe'           => 'icon-external-link',
];

/**
 * Sprite icon → Lucide icon name mapping (mirrors window.famoLucideName in lucide-adapter.js).
 */
const FAMO_LUCIDE_ICON_MAP = [
    'icon-alert'           => 'triangle-alert',
    'icon-arrow-left'      => 'arrow-left',
    'icon-arrow-right'     => 'arrow-right',
    'icon-book'            => 'book-open',
    'icon-brand-instagram' => 'instagram',
    'icon-brand-whatsapp'  => 'message-circle',
    'icon-calendar'        => 'calendar-days',
    'icon-chart-bar'       => 'chart-column',
    'icon-check'           => 'check',
    'icon-chevron-down'    => 'chevron-down',
    'icon-chevron-left'    => 'chevron-left',
    'icon-chevron-right'   => 'chevron-right',
    'icon-close'           => 'x',
    'icon-clipboard'       => 'clipboard-list',
    'icon-clock'           => 'clock-3',
    'icon-device-floppy'   => 'save',
    'icon-download'        => 'download',
    'icon-edit'            => 'pencil',
    'icon-external-link'   => 'external-link',
    'icon-eye'             => 'eye',
    'icon-eye-closed'      => 'eye-off',
    'icon-eye-open'        => 'eye',
    'icon-file'            => 'file-text',
    'icon-filter'          => 'list-filter',
    'icon-headset'         => 'headset',
    'icon-help-circle'     => 'circle-help',
    'icon-home'            => 'house',
    'icon-id-card'         => 'id-card',
    'icon-info'            => 'info',
    'icon-key'             => 'key-round',
    'icon-keyboard'        => 'keyboard',
    'icon-logout'          => 'log-out',
    'icon-mail'            => 'mail',
    'icon-map-pin'         => 'map-pin',
    'icon-menu'            => 'menu',
    'icon-minus'           => 'minus',
    'icon-newspaper'       => 'newspaper',
    'icon-pause'           => 'pause',
    'icon-phone'           => 'phone',
    'icon-plus'            => 'plus',
    'icon-report'          => 'file-chart-column',
    'icon-save'            => 'save',
    'icon-school'          => 'school',
    'icon-search'          => 'search',
    'icon-settings'        => 'settings',
    'icon-star'            => 'star',
    'icon-telegram'        => 'send',
    'icon-trash'           => 'trash-2',
    'icon-upload'          => 'upload',
    'icon-user'            => 'user',
    'icon-user-plus'       => 'user-plus',
    'icon-users'           => 'users',
    'icon-x'               => 'x',
];

/**
 * Social platform → sprite icon mapping (mirrors SOCIAL_ICON_MAP in assets/js/main.js).
 */
const FAMO_SOCIAL_ICON_MAP = [
    'telegram'  => 'icon-telegram',
    'instagram' => 'icon-brand-instagram',
    'whatsapp'  => 'icon-brand-whatsapp',
];

function famo_esc(mixed $value): string
{
    if ($value === null || is_array($value) || is_object($value)) {
        return '';
    }
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function famo_str(mixed $value): string
{
    if ($value === null || is_array($value) || is_object($value) || is_bool($value)) {
        return '';
    }
    return (string) $value;
}

function famo_uploads_base(): string
{
    return rtrim(famo_env('UPLOADS_URL', 'https://api.famoacademy.ir/uploads'), '/');
}

/**
 * Allow only http(s):// or single-slash root-relative paths (/...).
 */
function famo_safe_url(mixed $value): string
{
    $s = trim(famo_str($value));
    if ($s === '' || preg_match('/[\x00-\x1F\x7F]/', $s) === 1) {
        return '';
    }
    if (preg_match('#^https?://#i', $s) === 1) {
        return $s;
    }
    if (str_starts_with($s, '/') && !str_starts_with($s, '//')) {
        return $s;
    }
    return '';
}

/**
 * Accept only strict 3- or 6-digit hex colours; otherwise return fallback.
 */
function famo_safe_hex(mixed $value, string $fallback): string
{
    $s = trim(famo_str($value));
    return preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $s) === 1 ? $s : $fallback;
}

/**
 * Resolve course/person image URL: http(s), root-relative (/...), or bare filename under UPLOADS_BASE.
 * Rejects javascript:, vbscript:, data:, protocol-relative //, and traversal/control chars.
 */
function famo_resolve_image_url(mixed $value): string
{
    $src = trim(famo_str($value));
    if ($src === '' || preg_match('/[\x00-\x1F\x7F]/', $src) === 1) {
        return '';
    }
    if (str_starts_with($src, '//')) {
        return '';
    }
    if (preg_match('#^https?://#i', $src) === 1) {
        return $src;
    }
    // Reject any other URI scheme (javascript:, vbscript:, data:, file:, etc.)
    if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $src) === 1) {
        return '';
    }
    if (str_starts_with($src, '/')) {
        return $src;
    }
    if (str_starts_with($src, 'uploads/') || str_starts_with($src, '../') || str_starts_with($src, 'assets/')) {
        return '';
    }
    return famo_uploads_base() . '/' . ltrim($src, '/');
}

function famo_fa_to_svg(mixed $faClass): string
{
    $raw = trim(famo_str($faClass));
    if ($raw === '') {
        return 'icon-book';
    }
    $parts = preg_split('/\s+/', $raw) ?: [];
    foreach ($parts as $part) {
        if (isset(FAMO_FA_ICON_MAP[$part])) {
            return FAMO_FA_ICON_MAP[$part];
        }
    }
    return 'icon-book';
}

function famo_lucide_name(string $name): string
{
    if (isset(FAMO_LUCIDE_ICON_MAP[$name])) {
        return FAMO_LUCIDE_ICON_MAP[$name];
    }
    $stripped = preg_replace('/^icon-/', '', $name);
    return is_string($stripped) && $stripped !== '' ? $stripped : 'circle-help';
}

function famo_svg_icon(string $name, string $cls = ''): string
{
    $c = $cls !== '' ? ' ' . $cls : '';
    $icon = famo_lucide_name($name);
    return '<i class="icon' . famo_esc($c) . '" data-lucide="' . famo_esc($icon) . '" aria-hidden="true"></i>';
}

/**
 * Render one course slide matching buildCourseSlide() in assets/js/main.js.
 *
 * @param array<string, mixed> $course
 */
function famo_render_course_slide(array $course, int $index): string
{
    $from = famo_safe_hex($course['gradient_color_from'] ?? null, '#445D84');
    $to = famo_safe_hex($course['gradient_color_to'] ?? null, '#6D8B9E');
    $gradient = 'linear-gradient(135deg, ' . $from . ', ' . $to . ')';

    $imageUrl = famo_safe_url(famo_resolve_image_url($course['background_image_url'] ?? null));
    $courseId = famo_str($course['id'] ?? '');
    $courseName = famo_str($course['name'] ?? '');
    $badgeLabel = famo_str($course['badge_label'] ?? '');
    $targetGrades = famo_str($course['target_grades'] ?? '');
    $format = famo_str($course['format'] ?? '');
    $description = famo_str($course['description'] ?? '');

    $badgeBlock = $badgeLabel !== ''
        ? '<span class="famo-course-badge">' . famo_esc($badgeLabel) . '</span>'
        : '';

    $metaItems = '';
    if ($targetGrades !== '') {
        $metaItems .= '<span>' . famo_esc($targetGrades) . '</span>';
    }
    if ($format !== '') {
        $metaItems .= '<span>' . famo_esc($format) . '</span>';
    }
    $metaBlock = $metaItems !== ''
        ? '<div class="famo-course-meta">' . $metaItems . '</div>'
        : '';

    $shouldLazyLoad = $index >= 3;
    $imgAttrs = 'width="320" height="200" decoding="async"' . ($shouldLazyLoad ? ' loading="lazy"' : '');

    $mediaInner = $imageUrl !== ''
        ? '<img src="' . famo_esc($imageUrl) . '" alt="' . famo_esc($courseName) . '" ' . $imgAttrs . ' class="famo-course-img">'
        : '<div class="course-image-fallback">' . famo_svg_icon(famo_fa_to_svg($course['icon'] ?? null), 'icon--3xl text-white/90') . '</div>';

    $descBlock = $description !== ''
        ? '<p class="famo-course-desc">' . famo_esc($description) . '</p>'
        : '';

    $courseHref = famo_course_clean_url($course);

    return '<div class="swiper-slide">'
        . '<a class="famo-course-card" href="' . famo_esc($courseHref) . '" aria-label="' . famo_esc($courseName) . ' - مشاهده جزئیات دوره">'
        . '<div class="famo-course-media" style="background-image: ' . $gradient . ';">'
        . $mediaInner
        . $badgeBlock
        . '</div>'
        . '<div class="famo-course-body">'
        . '<h3 class="famo-course-name">' . famo_esc($courseName) . '</h3>'
        . $metaBlock
        . $descBlock
        . '<div class="famo-course-footer">'
        . '<span class="famo-course-cta">جزئیات دوره '
        . '<i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>'
        . '</span>'
        . '</div>'
        . '</div>'
        . '</a>'
        . '</div>';
}

/**
 * Render social links block matching renderSocialLinks() in assets/js/main.js.
 *
 * @param mixed $links
 */
function famo_render_social_links(mixed $links): string
{
    if (!is_array($links) || count($links) === 0) {
        return '';
    }

    $items = '';
    foreach ($links as $link) {
        if (!is_array($link)) {
            continue;
        }
        $url = famo_safe_url($link['url'] ?? null);
        if ($url === '') {
            continue;
        }
        $platform = famo_str($link['platform'] ?? '');
        $icon = FAMO_SOCIAL_ICON_MAP[$platform] ?? 'icon-external-link';
        $items .= '<a href="' . famo_esc($url) . '" target="_blank" rel="noopener" class="inst-social" aria-label="' . famo_esc($platform) . '">'
            . famo_svg_icon($icon, 'icon--sm')
            . '</a>';
    }

    return $items !== '' ? '<div class="inst-socials">' . $items . '</div>' : '';
}

/**
 * Normalize an instructor API item matching normalizeInstructor() in assets/js/main.js.
 *
 * @param array<string, mixed> $instructor
 * @return array<string, mixed>
 */
function famo_normalize_instructor(array $instructor): array
{
    $name = famo_str($instructor['name'] ?? '');
    $title = famo_str($instructor['title'] ?? '');
    $desc = famo_str($instructor['description'] ?? '');
    if ($desc === '') {
        $desc = famo_str($instructor['full_bio'] ?? '');
    }
    $img = famo_str($instructor['image_url'] ?? '');
    if ($img === '') {
        $img = famo_str($instructor['avatar_url'] ?? '');
    }

    return [
        'role'           => 'instructor',
        'roleLabel'      => 'استاد',
        'name'           => $name,
        'title'          => $title !== '' ? $title : 'استاد تخصصی',
        'description'    => $desc,
        'image_url'      => $img,
        'initial_letter' => famo_str($instructor['initial_letter'] ?? ''),
        'social_links'   => is_array($instructor['social_links'] ?? null) ? $instructor['social_links'] : [],
        'tag'            => '',
    ];
}

/**
 * Normalize a supporter API item matching normalizeSupporter() in assets/js/main.js.
 *
 * @param array<string, mixed> $supporter
 * @return array<string, mixed>
 */
function famo_normalize_supporter(array $supporter): array
{
    $field = famo_str($supporter['field'] ?? '');
    $grade = famo_str($supporter['grade'] ?? '');
    $fieldLabel = $field !== ''
        ? 'رشته ' . $field
        : ($grade !== '' ? 'پایه ' . $grade : 'تحصیلی');

    $title = famo_str($supporter['title'] ?? '');
    $desc = famo_str($supporter['description'] ?? '');
    if ($desc === '') {
        $desc = famo_str($supporter['full_bio'] ?? '');
    }
    if ($desc === '') {
        $desc = famo_str($supporter['bio'] ?? '');
    }
    if ($desc === '') {
        $desc = 'پیگیری برنامه مطالعه، رفع اشکال درسی و همراهی مستمر دانش‌آموزان تا رسیدن به هدف.';
    }

    $img = famo_str($supporter['image_url'] ?? '');
    if ($img === '') {
        $img = famo_str($supporter['avatar_url'] ?? '');
    }
    if ($img === '') {
        $img = famo_str($supporter['image'] ?? '');
    }

    return [
        'role'           => 'supporter',
        'roleLabel'      => 'پشتیبان',
        'name'           => famo_str($supporter['name'] ?? ''),
        'title'          => $title !== '' ? $title : 'پشتیبان تخصصی ' . $fieldLabel,
        'description'    => $desc,
        'image_url'      => $img,
        'initial_letter' => famo_str($supporter['initial_letter'] ?? ''),
        'social_links'   => is_array($supporter['social_links'] ?? null) ? $supporter['social_links'] : [],
        'tag'            => '',
    ];
}

/**
 * Build combined normalized team list (instructors followed by supporters).
 *
 * @param array<int, mixed> $instructors
 * @param array<int, mixed> $supporters
 * @return array<int, array<string, mixed>>
 */
function famo_home_build_team_list(array $instructors, array $supporters): array
{
    $list = [];
    foreach ($instructors as $inst) {
        if (is_array($inst)) {
            $list[] = famo_normalize_instructor($inst);
        }
    }
    foreach ($supporters as $sup) {
        if (is_array($sup)) {
            $list[] = famo_normalize_supporter($sup);
        }
    }
    return $list;
}

/**
 * Extract first UTF-8 character safely.
 */
function famo_first_char(string $text): string
{
    $trimmed = trim($text);
    if ($trimmed === '') {
        return '';
    }
    if (function_exists('mb_substr')) {
        return mb_substr($trimmed, 0, 1, 'UTF-8');
    }
    if (preg_match('/^./u', $trimmed, $m) === 1) {
        return $m[0];
    }
    return substr($trimmed, 0, 1);
}

/**
 * Render one team member card matching personCardHtml() in assets/js/main.js.
 *
 * @param array<string, mixed> $person
 */
function famo_render_person_card(array $person): string
{
    $role = ($person['role'] ?? '') === 'supporter' ? 'supporter' : 'instructor';
    $name = famo_str($person['name'] ?? '');
    $title = famo_str($person['title'] ?? '');
    $description = famo_str($person['description'] ?? '');
    $roleLabel = famo_str($person['roleLabel'] ?? '');
    $tag = famo_str($person['tag'] ?? '');

    $initialLetter = famo_str($person['initial_letter'] ?? '');
    if ($initialLetter === '') {
        $initialLetter = famo_first_char($name);
    }

    $rawImg = famo_str($person['image_url'] ?? '');
    if ($rawImg === '') {
        $rawImg = famo_str($person['avatar_url'] ?? '');
    }
    if ($rawImg === '') {
        $rawImg = famo_str($person['image'] ?? '');
    }
    $avatarUrl = famo_safe_url(famo_resolve_image_url($rawImg));

    $socialsBlock = famo_render_social_links($person['social_links'] ?? []);
    $footerBlock = $socialsBlock !== ''
        ? $socialsBlock
        : ($tag !== ''
            ? '<div class="inst-socials person-card__footer"><span class="person-tag">' . famo_esc($tag) . '</span></div>'
            : '');

    $mediaInner = $avatarUrl !== ''
        ? '<img class="inst-avatar" src="' . famo_esc($avatarUrl) . '" alt="' . famo_esc($name) . '" width="640" height="450" loading="lazy" decoding="async">'
        : '<div class="inst-avatar inst-avatar-ph" aria-hidden="true">' . famo_esc($initialLetter) . '</div>';

    $bioBlock = $description !== ''
        ? '<p class="inst-bio">' . famo_esc($description) . '</p>'
        : '';

    return '<article class="inst-card person-card person-card--' . $role . '" data-person-role="' . $role . '">'
        . '<div class="person-media">'
        . $mediaInner
        . '<span class="person-role-badge">' . famo_esc($roleLabel) . '</span>'
        . '</div>'
        . '<h3 class="inst-name">' . famo_esc($name) . '</h3>'
        . '<p class="inst-title">' . famo_esc($title) . '</p>'
        . $bioBlock
        . $footerBlock
        . '</article>';
}

/**
 * Render team cards for carousel mode (total >= 3) or off-mode (total < 3).
 *
 * @param array<int, array<string, mixed>> $teamList
 */
function famo_render_team_cards(array $teamList): string
{
    $total = count($teamList);
    if ($total === 0) {
        return '';
    }

    if ($total < 3) {
        $html = '';
        foreach ($teamList as $person) {
            $role = ($person['role'] ?? '') === 'supporter' ? 'supporter' : 'instructor';
            $html .= '<div class="curve-carousel__card" data-person-role="' . $role . '">'
                . famo_render_person_card($person)
                . '</div>';
        }
        return $html;
    }

    $repeatMultiplier = $total < 5 ? (int) ceil(7 / $total) : 1;
    $renderCount = max($total * $repeatMultiplier, 7);

    $html = '';
    for ($j = 0; $j < $renderCount; $j++) {
        $idx = $j % $total;
        $person = $teamList[$idx];
        $role = ($person['role'] ?? '') === 'supporter' ? 'supporter' : 'instructor';
        $html .= '<div class="curve-carousel__card" data-virtual-idx="' . $j . '" data-item-idx="' . $idx . '" data-person-role="' . $role . '">'
            . famo_render_person_card($person)
            . '</div>';
    }
    return $html;
}

/**
 * Render carousel pagination dots (only when 1 < total <= 12).
 *
 * @param array<int, array<string, mixed>> $teamList
 */
function famo_render_team_dots(array $teamList): string
{
    $total = count($teamList);
    if ($total <= 1 || $total > 12) {
        return '';
    }

    $html = '';
    foreach ($teamList as $i => $person) {
        $label = 'اسلاید ' . ($i + 1) . ': ' . famo_str($person['roleLabel'] ?? '') . ' ' . famo_str($person['name'] ?? '');
        $html .= '<button type="button" class="curve-carousel__dot" data-curve-dot="' . $i . '" aria-label="' . famo_esc($label) . '"></button>';
    }
    return $html;
}

/**
 * Map a course API object to its dedicated clean-URL landing page (never hash fragments).
 *
 * @param array<string, mixed> $course
 */
function famo_course_clean_url(array $course): string
{
    $name = famo_str($course['name'] ?? '');
    $grades = famo_str($course['target_grades'] ?? '');
    $haystack = $name . ' ' . $grades;

    $map = [
        'رفع اشکال'  => '/courses/rafa-eshkal-babol/',
        'تیزهوشان'   => '/courses/tizhooshan-babol/',
        'مشاوره'     => '/counseling-babol/',
        'زیست'       => '/courses/biology-babol/',
        'تجربی'      => '/courses/biology-babol/',
        'شیمی'       => '/courses/chemistry-babol/',
        'فیزیک'      => '/courses/physics-babol/',
        'ریاضی'      => '/courses/math-babol/',
        'حسابان'     => '/courses/math-babol/',
        'ابتدایی'    => '/courses/elementary-babol/',
        'متوسطه اول' => '/courses/middle-school-babol/',
        'متوسطه دوم' => '/courses/high-school-babol/',
        'کنکور'      => '/courses/konkur-babol/',
    ];

    foreach ($map as $keyword => $url) {
        if ($keyword !== '' && str_contains($haystack, $keyword)) {
            return $url;
        }
    }

    return '/courses/';
}

/**
 * Render one detailed course grid card for /courses/ (matches renderCourseCard in assets/js/courses.js).
 *
 * @param array<string, mixed> $course
 */
function famo_render_course_grid_card(array $course): string
{
    $from = famo_safe_hex($course['gradient_color_from'] ?? null, '#445D84');
    $to = famo_safe_hex($course['gradient_color_to'] ?? null, '#6D8B9E');
    $rawImg = famo_str($course['background_image_url'] ?? '');
    if ($rawImg === '') {
        $rawImg = famo_str($course['image_url'] ?? '');
    }
    if ($rawImg === '') {
        $rawImg = famo_str($course['image'] ?? '');
    }
    $imageUrl = famo_safe_url(famo_resolve_image_url($rawImg));
    $courseId = famo_str($course['id'] ?? '');
    $courseName = famo_str($course['name'] ?? '');
    $badgeLabel = famo_str($course['badge_label'] ?? '');
    $targetGrades = famo_str($course['target_grades'] ?? '');
    $format = famo_str($course['format'] ?? '');
    $bodyText = famo_str($course['full_description'] ?? '');
    if ($bodyText === '') {
        $bodyText = famo_str($course['description'] ?? '');
    }
    $detailUrl = famo_course_clean_url($course);

    $badgeBlock = $badgeLabel !== ''
        ? '<span class="course-badge">' . famo_esc($badgeLabel) . '</span>'
        : '';

    $metaItems = '';
    if ($targetGrades !== '') {
        $metaItems .= '<span>' . famo_esc($targetGrades) . '</span>';
    }
    if ($format !== '') {
        $metaItems .= '<span>' . famo_esc($format) . '</span>';
    }
    $metaBlock = $metaItems !== '' ? '<div class="course-meta">' . $metaItems . '</div>' : '';

    $featuresHtml = '';
    if (is_array($course['features'] ?? null)) {
        foreach ($course['features'] as $feat) {
            $featStr = trim(famo_str($feat));
            if ($featStr !== '') {
                $featuresHtml .= '<li>' . famo_svg_icon('icon-check', 'icon--sm text-[#445D84]') . '<span>' . famo_esc($featStr) . '</span></li>';
            }
        }
    }
    $featuresBlock = $featuresHtml !== '' ? '<ul class="course-features">' . $featuresHtml . '</ul>' : '';

    $mediaInner = $imageUrl !== ''
        ? '<img src="' . famo_esc($imageUrl) . '" alt="' . famo_esc($courseName) . '" loading="lazy" decoding="async" class="course-image">'
        : '<div class="course-image-fallback">' . famo_svg_icon(famo_fa_to_svg($course['icon'] ?? null), 'icon--3xl text-white/90') . '</div>';

    $detailLink = $detailUrl !== '/courses/'
        ? '<a href="' . famo_esc($detailUrl) . '" class="text-sm font-bold text-[#445D84] hover:underline inline-flex items-center gap-1">جزئیات بیشتر ' . famo_svg_icon('icon-chevron-left', 'icon--sm') . '</a>'
        : '';

    return '<article id="course-' . famo_esc($courseId) . '" class="course-card">'
        . '<div class="course-image-wrapper" style="background: linear-gradient(135deg, ' . $from . ', ' . $to . ');">'
        . $mediaInner
        . $badgeBlock
        . '</div>'
        . '<div class="course-body">'
        . '<h3 class="course-name">' . famo_esc($courseName) . '</h3>'
        . $metaBlock
        . ($bodyText !== '' ? '<p class="course-desc">' . famo_esc($bodyText) . '</p>' : '')
        . $featuresBlock
        . '<div class="course-footer">'
        . '<a href="/contact/#consultation" class="course-cta">درخواست مشاوره</a>'
        . $detailLink
        . '</div>'
        . '</div>'
        . '</article>';
}

const FAMO_AUTHOR_ROLE_LABELS = [
    'student'  => 'دانش‌آموز',
    'parent'   => 'ولی دانش‌آموز',
    'graduate' => 'فارغ‌التحصیل',
    'other'    => 'سایر',
];

function famo_to_persian_digits(string $value): string
{
    return strtr($value, [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ]);
}

function famo_format_persian_rating(float $value): string
{
    if (!is_finite($value) || $value <= 0) {
        return famo_to_persian_digits('0');
    }
    $rounded = round($value, 1);
    $formatted = ((float) (int) $rounded === $rounded)
        ? (string) (int) $rounded
        : number_format($rounded, 1, '.', '');
    return famo_to_persian_digits(str_replace('.', '٫', $formatted));
}

function famo_format_author_role(mixed $role): string
{
    $key = trim(famo_str($role));
    return FAMO_AUTHOR_ROLE_LABELS[$key] ?? FAMO_AUTHOR_ROLE_LABELS['other'];
}

function famo_render_star_svg(bool $filled = true, int $size = 18): string
{
    $cls = $filled ? 'famo-star famo-star--filled' : 'famo-star famo-star--empty';
    $dim = max(10, min(32, $size));
    return '<svg viewBox="0 0 24 24" width="' . $dim . '" height="' . $dim . '" aria-hidden="true" class="' . $cls . '">'
        . '<path d="M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.53L12 17.46 6.14 20.55l1.12-6.53L2.51 9.39l6.56-.95L12 2.5z"></path>'
        . '</svg>';
}

function famo_render_stars_row(mixed $ratingValue, int $size = 18, string $extraClass = ''): string
{
    if (is_bool($ratingValue) || is_array($ratingValue) || is_object($ratingValue)) {
        return '';
    }
    $raw = trim((string) $ratingValue);
    if ($raw === '' || !is_numeric($raw)) {
        return '';
    }
    $rating = (int) $raw;
    if ($rating < 1 || $rating > 5) {
        return '';
    }
    $cls = $extraClass !== '' ? 'famo-stars ' . $extraClass : 'famo-stars';
    $label = 'امتیاز ' . famo_to_persian_digits((string) $rating) . ' از ' . famo_to_persian_digits('5');
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= famo_render_star_svg($i <= $rating, $size);
    }
    return '<div class="' . famo_esc($cls) . '" aria-label="' . famo_esc($label) . '">' . $stars . '</div>';
}

/**
 * Render one testimonial excerpt item for homepage #homeTestimonialsExcerpt.
 *
 * @param array<string, mixed> $item
 */
function famo_render_home_testimonial_excerpt(array $item): string
{
    $authorName = trim(famo_str($item['author_name'] ?? ''));
    $authorMeta = trim(famo_str($item['author_meta'] ?? ''));
    $roleLabel = famo_format_author_role($item['author_role'] ?? '');
    $content = trim(famo_str($item['content'] ?? ''));
    if ($authorName === '' && $content === '') {
        return '';
    }

    $subParts = [];
    if ($roleLabel !== '') {
        $subParts[] = $roleLabel;
    }
    if ($authorMeta !== '') {
        $subParts[] = famo_to_persian_digits($authorMeta);
    }
    $metaStr = count($subParts) > 0 ? ' — ' . implode('، ', $subParts) : '';
    $starsHtml = famo_render_stars_row($item['rating'] ?? null, 14);

    return '<div class="famo-excerpt-item">'
        . '<div class="flex items-center justify-between gap-2 flex-wrap mb-1">'
        . '<p class="famo-excerpt-title m-0">'
        . famo_esc($authorName)
        . ($metaStr !== '' ? '<span class="text-xs font-normal text-gray-500">' . famo_esc($metaStr) . '</span>' : '')
        . '</p>'
        . $starsHtml
        . '</div>'
        . '<p class="famo-excerpt-body">' . famo_esc($content) . '</p>'
        . '</div>';
}

/**
 * Render summary banner matching renderSummary() in assets/js/testimonials.js.
 *
 * @param array<string, mixed> $summary
 */
function famo_render_testimonial_summary(array $summary): string
{
    $totalApproved = max(0, (int) ($summary['total_approved'] ?? 0));
    $avgRating = (float) ($summary['average_rating'] ?? 0);
    $hasRating = $totalApproved > 0 && is_finite($avgRating) && $avgRating > 0;
    $roundedStars = $hasRating ? (int) round($avgRating) : 0;

    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= famo_render_star_svg($i <= $roundedStars, 18);
    }

    $avgDisplay = $hasRating ? famo_format_persian_rating($avgRating) : '—';
    $outOfDisplay = $totalApproved > 0 ? 'از ' . famo_to_persian_digits('5') : '';

    return '<div class="famo-testi-summary-grid">'
        . '<div class="famo-testi-stat">'
        . '<div class="famo-testi-stat-value">' . famo_esc(famo_to_persian_digits((string) $totalApproved)) . '</div>'
        . '<div class="famo-testi-stat-label">نظر تأییدشده</div>'
        . '</div>'
        . '<div class="famo-testi-stat">'
        . '<div class="famo-testi-stat-rating-row">'
        . '<span class="famo-testi-stat-value">' . famo_esc($avgDisplay) . '</span>'
        . '<span class="famo-testi-stat-outof">' . famo_esc($outOfDisplay) . '</span>'
        . '</div>'
        . '<div class="famo-stars famo-stars--summary" aria-hidden="true">' . $stars . '</div>'
        . '<div class="famo-testi-stat-label">میانگین امتیاز رضایت</div>'
        . '</div>'
        . '<div class="famo-testi-stat-cta">'
        . '<p class="famo-testi-stat-note">تجربه حضور در کلاس‌ها یا مشاوره فامو را داشته‌اید؟</p>'
        . '<a href="#submit-testimonial" class="famo-state-btn">ثبت نظر جدید</a>'
        . '</div>'
        . '</div>';
}

/**
 * Render one full testimonial card matching buildTestimonialCard() in assets/js/testimonials.js.
 *
 * @param array<string, mixed> $item
 */
function famo_render_testimonial_card(array $item, string $headingTag = 'h4'): string
{
    $tag = ($headingTag === 'h3') ? 'h3' : 'h4';
    $isFeatured = !empty($item['is_featured']);
    $cardClass = $isFeatured ? 'famo-testi-card hover-lift famo-testi-card--featured' : 'famo-testi-card hover-lift';

    $authorName = trim(famo_str($item['author_name'] ?? ''));
    $initial = famo_first_char($authorName);
    if ($initial === '') {
        $initial = 'ف';
    }
    $roleLabel = famo_format_author_role($item['author_role'] ?? '');
    $authorMeta = trim(famo_str($item['author_meta'] ?? ''));
    $content = famo_str($item['content'] ?? '');
    $courseName = trim(famo_str($item['course_name'] ?? ''));
    $instructorName = trim(famo_str($item['instructor_name'] ?? ''));

    $metaHtml = $authorMeta !== ''
        ? '<p class="famo-testi-meta">' . famo_esc(famo_to_persian_digits($authorMeta)) . '</p>'
        : '';
    $starsHtml = famo_render_stars_row($item['rating'] ?? null, 18);

    $tagsHtml = '';
    if ($courseName !== '' || $instructorName !== '') {
        $tagsHtml .= '<div class="famo-testi-tags">';
        if ($courseName !== '') {
            $tagsHtml .= '<span class="famo-adm-chip famo-adm-chip--exam">دوره: ' . famo_esc($courseName) . '</span>';
        }
        if ($instructorName !== '') {
            $tagsHtml .= '<span class="famo-adm-chip famo-adm-chip--field">استاد: ' . famo_esc($instructorName) . '</span>';
        }
        $tagsHtml .= '</div>';
    }

    return '<article class="' . $cardClass . '">'
        . '<div class="famo-testi-card-head">'
        . '<div class="famo-testi-author">'
        . '<div class="famo-testi-avatar" aria-hidden="true">' . famo_esc($initial) . '</div>'
        . '<div class="famo-testi-author-info">'
        . '<div class="famo-testi-name-row">'
        . '<' . $tag . ' class="famo-testi-name">' . famo_esc($authorName) . '</' . $tag . '>'
        . '<span class="famo-testi-role-badge">' . famo_esc($roleLabel) . '</span>'
        . '</div>'
        . $metaHtml
        . '</div>'
        . '</div>'
        . $starsHtml
        . '</div>'
        . '<p class="famo-testi-content">' . famo_esc($content) . '</p>'
        . $tagsHtml
        . '</article>';
}

/**
 * Render initial SSR pagination shell matching renderPagination() in assets/js/testimonials.js.
 */
function famo_render_testimonials_pagination(int $page, int $totalPages): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $safePage = max(1, min($page, $totalPages));
    $prevDisabled = $safePage <= 1 ? ' disabled' : '';
    $nextDisabled = $safePage >= $totalPages ? ' disabled' : '';
    $info = 'صفحه ' . famo_to_persian_digits((string) $safePage) . ' از ' . famo_to_persian_digits((string) $totalPages);

    return '<nav class="famo-pagination" aria-label="صفحه‌بندی نظرات">'
        . '<button type="button" class="famo-page-btn" data-page-dir="prev"' . $prevDisabled . '>صفحه قبل</button>'
        . '<span class="famo-page-info">' . famo_esc($info) . '</span>'
        . '<button type="button" class="famo-page-btn" data-page-dir="next"' . $nextDisabled . '>صفحه بعد</button>'
        . '</nav>';
}

/**
 * Build Schema.org AggregateRating + Review entries for EducationalOrganization
 * strictly from real approved API data (never fabricated).
 *
 * @param ?array<string, mixed> $summary
 * @param array<int, mixed> $items
 * @return array<string, mixed>
 */
function famo_build_testimonials_schema(?array $summary, array $items, int $maxReviews = 9): array
{
    $extra = [];

    if (is_array($summary)) {
        $totalApproved = (int) ($summary['total_approved'] ?? 0);
        $avgRating = (float) ($summary['average_rating'] ?? 0);
        if ($totalApproved > 0 && is_finite($avgRating) && $avgRating > 0 && $avgRating <= 5) {
            $extra['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => round($avgRating, 2),
                'reviewCount' => $totalApproved,
                'bestRating'  => 5,
                'worstRating' => 1,
            ];
        }
    }

    $reviews = [];
    foreach ($items as $item) {
        if (count($reviews) >= $maxReviews) {
            break;
        }
        if (!is_array($item)) {
            continue;
        }
        $authorName = trim(famo_str($item['author_name'] ?? ''));
        $body = trim(famo_str($item['content'] ?? ''));
        if ($authorName === '' || $body === '') {
            continue;
        }
        $review = [
            '@type'      => 'Review',
            'inLanguage' => 'fa-IR',
            'author'     => [
                '@type' => 'Person',
                'name'  => $authorName,
            ],
            'reviewBody' => $body,
        ];
        $ratingRaw = $item['rating'] ?? null;
        if (!is_bool($ratingRaw) && is_numeric($ratingRaw)) {
            $rating = (int) $ratingRaw;
            if ($rating >= 1 && $rating <= 5) {
                $review['reviewRating'] = [
                    '@type'       => 'Rating',
                    'ratingValue' => $rating,
                    'bestRating'  => 5,
                    'worstRating' => 1,
                ];
            }
        }
        $createdAt = trim(famo_str($item['created_at'] ?? ''));
        if ($createdAt !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $createdAt, $m) === 1) {
            $review['datePublished'] = $m[0];
        }
        $reviews[] = $review;
    }

    if (count($reviews) > 0) {
        $extra['review'] = $reviews;
    }

    return $extra;
}
