<?php
// public/guests.php - The new entry point for the guests page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the guests logic
require_once __DIR__ . '/../src/handlers/guests_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/guests_view.php';
?>
