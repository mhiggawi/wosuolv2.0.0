<?php
// public/register.php - The new entry point for the register page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the register logic
require_once __DIR__ . '/../src/handlers/register_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/register_view.php';
?>
