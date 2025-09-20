<?php
// public/rsvp.php - The new entry point for the rsvp page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the rsvp logic
require_once __DIR__ . '/../src/handlers/rsvp_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/rsvp_view.php';
?>
