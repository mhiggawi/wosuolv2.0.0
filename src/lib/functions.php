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
