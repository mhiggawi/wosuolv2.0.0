<?php
// public/admin.php - The new entry point for the admin page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the admin logic
require_once __DIR__ . '/../src/handlers/admin_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/admin_view.php';
?>
