<?php
// src/lib/functions.php
// A collection of reusable functions for the application.

/**
 * A wrapper for htmlspecialchars to provide a consistent and safe way to output data.
 *
 * @param mixed $value The value to escape.
 * @param string $default The default value to return if the input is null.
 * @return string The escaped HTML string.
 */
function safe_html($value, $default = ''): string {
    return htmlspecialchars($value ?? $default, ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a user-friendly page title.
 *
 * @param string $title The title of the specific page.
 * @param string $lang The current language ('ar' or 'en').
 * @return string The full page title.
 */
function getPageTitle(string $title, string $lang): string {
    $site_name = $lang === 'ar' ? 'وصول' : 'Wosuol';
    return "{$title} - {$site_name}";
}

/**
 * Parses an event date string from various formats into a DateTime object.
 *
 * @param string|null $event_date_text The date string to parse.
 * @return DateTime|false A DateTime object on success, or false on failure.
 */
function parseEventDateTime(?string $event_date_text): DateTime|false {
    if (empty($event_date_text)) {
        return false;
    }

    $formats = [
        'Y-m-d H:i:s',
        'Y-m-d H:i',
    ];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $event_date_text);
        if ($date !== false) {
            return $date;
        }
    }

    // Attempt to parse Arabic format with day name, e.g., "الجمعة 03.10.2025"
    if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $event_date_text, $matches)) {
        $date_string = "{$matches[3]}-{$matches[2]}-{$matches[1]} 18:00:00"; // Assume 6 PM
        return new DateTime($date_string);
    }

    return false;
}

/**
 * Formats a DateTime object into a user-friendly Arabic string.
 *
 * @param DateTime $dateTime The DateTime object to format.
 * @return string The formatted Arabic date string.
 */
function formatDateArabic(DateTime $dateTime): string {
    $months = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
    $days = ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];

    $dayOfWeek = $days[(int)$dateTime->format('w')];
    $dayOfMonth = $dateTime->format('j');
    $month = $months[(int)$dateTime->format('n') - 1];
    $year = $dateTime->format('Y');
    $time = $dateTime->format('g:i');
    $am_pm = $dateTime->format('A') === 'AM' ? 'صباحًا' : 'مساءً';

    return "{$dayOfWeek}، {$dayOfMonth} {$month} {$year}، {$time} {$am_pm}";
}

/**
 * Handles file uploads securely.
 *
 * @param array $file The file array from $_FILES.
 * @param string $uploadDir The directory to upload the file to.
 * @param string $prefix A prefix for the new filename.
 * @param int $maxSize The maximum allowed file size in bytes.
 * @param array $allowedTypes An array of allowed MIME types.
 * @return string|null The new file path on success, or null on failure.
 */
function handle_upload(array $file, string $uploadDir, string $prefix, int $maxSize = 5000000, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
}

/**
 * Converts Arabic numerals to Latin numerals.
 * @param string|int $string The string or number to convert.
 * @return string The converted string.
 */
function convertToLatinNumerals($string) {
    $arabic_numerals = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    $latin_numerals = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    return str_replace($arabic_numerals, $latin_numerals, $string);
}

function generateDashboardHTML($event_name, $total, $confirmed, $checkedIn, $canceled, $pending, $guests, $t, $lang) {
    $dir = $lang === 'ar' ? 'rtl' : 'ltr';
    $font = $lang === 'ar' ? 'Cairo' : 'Inter';

    $html = "<!DOCTYPE html>
    <html lang='$lang' dir='$dir'>
    <head>
        <meta charset='UTF-8'>
        <title>{$t['dashboard']}: $event_name</title>
        <link href='https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap' rel='stylesheet'>
        <style>
            body { font-family: '$font', sans-serif; margin: 20px; direction: $dir; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #3b82f6; padding-bottom: 20px; }
            .logo { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; }
            .logo-icon { width: 40px; height: 40px; background: #4f46e5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; }
            .logo-text { font-size: 1.5rem; font-weight: bold; color: #1e40af; }
            .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 30px; }
            .stat-card { text-align: center; padding: 20px; border-radius: 10px; border: 2px solid #e5e7eb; }
            .stat-card.total { border-color: #6b7280; background-color: #f9fafb; }
            .stat-card.confirmed { border-color: #22c55e; background-color: #dcfce7; }
            .stat-card.checked-in { border-color: #3b82f6; background-color: #dbeafe; }
            .stat-card.canceled { border-color: #ef4444; background-color: #fee2e2; }
            .stat-card.pending { border-color: #f59e0b; background-color: #fef3c7; }
            .stat-value { font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; }
            .stat-label { font-size: 1rem; color: #6b7280; }
            .section-title { font-size: 1.5rem; font-weight: bold; margin: 30px 0 15px 0; color: #374151; }
            .guest-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
            .guest-column { border: 1px solid #e5e7eb; border-radius: 8px; }
            .column-header { padding: 15px; font-weight: bold; text-align: center; }
            .column-header.checked-in { background-color: #3b82f6; color: white; }
            .column-header.confirmed { background-color: #22c55e; color: white; }
            .column-header.canceled { background-color: #ef4444; color: white; }
            .column-header.pending { background-color: #f59e0b; color: white; }
            .guest-item { padding: 10px 15px; border-bottom: 1px solid #e5e7eb; font-size: 0.9rem; }
            .guest-item:last-child { border-bottom: none; }
            .guest-name { font-weight: 600; }
            .guest-details { color: #6b7280; font-size: 0.8rem; margin-top: 2px; }
            .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 0.9rem; }
            @media print {
                body { margin: 0; }
                .stats-grid { page-break-after: avoid; }
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <div class='logo'>
                <div class='logo-icon'>✓</div>
                <div class='logo-text'>وصول</div>
            </div>
            <h1>{$t['dashboard']}: $event_name</h1>
            <p style='color: #6b7280; margin: 10px 0;'>" . date('Y-m-d H:i') . "</p>
        </div>

        <div class='stats-grid'>
            <div class='stat-card total'>
                <div class='stat-value'>$total</div>
                <div class='stat-label'>{$t['total_invited']}</div>
            </div>
            <div class='stat-card confirmed'>
                <div class='stat-value'>$confirmed</div>
                <div class='stat-label'>{$t['confirmed_attendance']}</div>
            </div>
            <div class='stat-card checked-in'>
                <div class='stat-value'>$checkedIn</div>
                <div class='stat-label'>{$t['checked_in_hall']}</div>
            </div>
            <div class='stat-card canceled'>
                <div class='stat-value'>$canceled</div>
                <div class='stat-label'>{$t['declined_attendance']}</div>
            </div>
            <div class='stat-card pending'>
                <div class='stat-value'>$pending</div>
                <div class='stat-label'>{$t['awaiting_response']}</div>
            </div>
        </div>

        <h2 class='section-title'>{$t['guest_details']}</h2>
        <div class='guest-grid'>
            <div class='guest-column'>
                <div class='column-header checked-in'>{$t['checked_in_hall']}</div>";

    foreach ($guests as $guest) {
        if ($guest['checkin_status'] === 'checked_in') {
            $guestCount = $guest['guests_count'] ? "({$guest['guests_count']} " . ($guest['guests_count'] > 1 ? $t['people_plural'] : $t['people_singular']) . ")" : '';
            $tableNumber = $guest['table_number'] ? "{$t['table_number']}: {$guest['table_number']}" : '';
            $html .= "<div class='guest-item'>
                        <div class='guest-name'>{$guest['name_ar']}</div>
                        <div class='guest-details'>$guestCount $tableNumber</div>
                      </div>";
        }
    }

    $html .= "</div><div class='guest-column'>
                <div class='column-header confirmed'>{$t['confirmed_attendance']}</div>";

    foreach ($guests as $guest) {
        if ($guest['status'] === 'confirmed' && $guest['checkin_status'] !== 'checked_in') {
            $guestCount = $guest['guests_count'] ? "({$guest['guests_count']} " . ($guest['guests_count'] > 1 ? $t['people_plural'] : $t['people_singular']) . ")" : '';
            $tableNumber = $guest['table_number'] ? "{$t['table_number']}: {$guest['table_number']}" : '';
            $html .= "<div class='guest-item'>
                        <div class='guest-name'>{$guest['name_ar']}</div>
                        <div class='guest-details'>$guestCount $tableNumber</div>
                      </div>";
        }
    }

    $html .= "</div><div class='guest-column'>
                <div class='column-header canceled'>{$t['declined_attendance']}</div>";

    foreach ($guests as $guest) {
        if ($guest['status'] === 'canceled') {
            $guestCount = $guest['guests_count'] ? "({$guest['guests_count']} " . ($guest['guests_count'] > 1 ? $t['people_plural'] : $t['people_singular']) . ")" : '';
            $tableNumber = $guest['table_number'] ? "{$t['table_number']}: {$guest['table_number']}" : '';
            $html .= "<div class='guest-item'>
                        <div class='guest-name'>{$guest['name_ar']}</div>
                        <div class='guest-details'>$guestCount $tableNumber</div>
                      </div>";
        }
    }

    $html .= "</div><div class='guest-column'>
                <div class='column-header pending'>{$t['awaiting_response']}</div>";

    foreach ($guests as $guest) {
        if ($guest['status'] !== 'confirmed' && $guest['status'] !== 'canceled') {
            $guestCount = $guest['guests_count'] ? "({$guest['guests_count']} " . ($guest['guests_count'] > 1 ? $t['people_plural'] : $t['people_singular']) . ")" : '';
            $tableNumber = $guest['table_number'] ? "{$t['table_number']}: {$guest['table_number']}" : '';
            $html .= "<div class='guest-item'>
                        <div class='guest-name'>{$guest['name_ar']}</div>
                        <div class='guest-details'>$guestCount $tableNumber</div>
                      </div>";
        }
    }

    $html .= "</div></div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " <strong>وصول - Wosuol.com</strong> - جميع الحقوق محفوظة</p>
        </div>
        </body></html>";
    return $html;
}

function generate_slug($string) {
    $string = str_replace(
        ['أ', 'ا', 'إ', 'آ', 'ى', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'ء', 'ة', ' '],
        ['a', 'a', 'a', 'a', 'y', 'b', 't', 'th', 'j', 'h', 'kh', 'd', 'z', 'r', 'z', 's', 'sh', 's', 'd', 't', 'z', 'a', 'gh', 'f', 'q', 'k', 'l', 'm', 'n', 'h', 'w', 'y', 'a', 'h', '-'],
        $string
    );
    $string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);
    $string = strtolower($string);
    $string = preg_replace('/--+/', '-', $string);
    return trim($string, '-');
}

    if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
        return null;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = $prefix . '_' . time() . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        return 'uploads/' . $newFileName; // Return the relative path
    }

    return null;
}
?>
