<?php
// public/events.php - The new entry point for the events page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the events logic
require_once __DIR__ . '/../src/handlers/events_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/events_view.php';
?>
