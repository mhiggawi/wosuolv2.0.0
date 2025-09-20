<?php
// src/bootstrap.php
// Centralized bootstrapping file for the application.

// Start the session if it's not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include essential files.
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/lib/functions.php'; // Will be created later

// --- Language System ---
// Determine the current language from session, cookie, or default to 'ar'.
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';

// Handle language switching via POST request.
if (isset($_POST['switch_language'])) {
    $new_lang = $_POST['switch_language'] === 'en' ? 'en' : 'ar';
    $_SESSION['language'] = $new_lang;
    setcookie('language', $new_lang, time() + (365 * 24 * 60 * 60), '/');
    $lang = $new_lang;

    // Redirect to the same page to prevent form resubmission.
    // This avoids re-triggering the language switch on refresh.
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// --- Load Language File ---
// This will be the new way of loading translations.
$lang_file = __DIR__ . '/../lang/' . $lang . '.php';

if (file_exists($lang_file)) {
    $t = require $lang_file;
} else {
    // Fallback to English if the language file doesn't exist.
    $t = require __DIR__ . '/../lang/en.php';
}

// --- CSRF Protection ---
// Generate a CSRF token if one doesn't exist in the session.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
