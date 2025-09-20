<?php
// public/login.php - The new entry point for the login page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the login logic
require_once __DIR__ . '/../src/handlers/login_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/login_view.php';
?>
