<?php
// public/dashboard.php - The new entry point for the dashboard page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the dashboard logic
require_once __DIR__ . '/../src/handlers/dashboard_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/dashboard_view.php';
?>
