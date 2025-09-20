<?php
// public/send_invitations.php - The new entry point for the send invitations page.

// 1. Bootstrap the application
require_once __DIR__ . '/../src/bootstrap.php';

// 2. Handle the send invitations logic
require_once __DIR__ . '/../src/handlers/send_invitations_handler.php';

// 3. Render the view
require_once __DIR__ . '/../views/send_invitations_view.php';
?>
