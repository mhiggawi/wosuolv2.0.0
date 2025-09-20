<?php
// public/checkin.php - The new entry point for the checkin page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the checkin logic
require_once __DIR__ . '/../src/handlers/checkin_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/checkin_view.php';
?>
