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

