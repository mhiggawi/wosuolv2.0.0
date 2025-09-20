<?php
// src/handlers/rsvp_handler.php

// This file is included by public/rsvp.php and assumes bootstrap.php has been included.

// Enable output compression
if (extension_loaded('zlib') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}

// Performance headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Language strings are now loaded from the language files in bootstrap.php.
// The $t variable is already available.


// Rate Limiting
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rate_limit_key = 'rsvp_rate_limit_' . md5($client_ip);
$current_time = time();

if (!isset($_SESSION[$rate_limit_key])) {
    $_SESSION[$rate_limit_key] = ['count' => 0, 'first_attempt' => $current_time];
}

if ($current_time - $_SESSION[$rate_limit_key]['first_attempt'] > 300) {
    $_SESSION[$rate_limit_key] = ['count' => 0, 'first_attempt' => $current_time];
}

// Data Initialization
$guest_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$event_data = null;
$guest_data = null;
$error_message = '';
$is_rate_limited = $_SESSION[$rate_limit_key]['count'] >= 10;

if (empty($guest_id)) {
    $error_message = $t['invalid_link'];
} else {
    $sql_guest = "SELECT g.*, e.* FROM guests g
                  JOIN events e ON g.event_id = e.id
                  WHERE g.guest_id = ? LIMIT 1";

    if ($stmt_guest = $mysqli->prepare($sql_guest)) {
        $stmt_guest->bind_param("s", $guest_id);
        $stmt_guest->execute();
        $result_guest = $stmt_guest->get_result();

        if ($result_guest->num_rows === 1) {
            $combined_data = $result_guest->fetch_assoc();

            $guest_data = [
                'id' => $combined_data['id'],
                'guest_id' => $combined_data['guest_id'],
                'name_ar' => $combined_data['name_ar'],
                'phone_number' => $combined_data['phone_number'],
                'guests_count' => $combined_data['guests_count'],
                'table_number' => $combined_data['table_number'],
                'status' => $combined_data['status'],
                'checkin_status' => $combined_data['checkin_status']
            ];

            $event_data = [
                'id' => $combined_data['event_id'],
                'event_name' => $combined_data['event_name'],
                'bride_name_ar' => $combined_data['bride_name_ar'],
                'groom_name_ar' => $combined_data['groom_name_ar'],
                'event_date_ar' => $combined_data['event_date_ar'],
                'event_date_en' => $combined_data['event_date_en'],
                'venue_ar' => $combined_data['venue_ar'],
                'Maps_link' => $combined_data['Maps_link'],
                'event_paragraph_ar' => $combined_data['event_paragraph_ar'],
                'background_image_url' => $combined_data['background_image_url'],
                'qr_card_title_ar' => $combined_data['qr_card_title_ar'],
                'qr_show_code_instruction_ar' => $combined_data['qr_show_code_instruction_ar'],
                'qr_brand_text_ar' => $combined_data['qr_brand_text_ar'],
                'qr_website' => $combined_data['qr_website'],
                'n8n_confirm_webhook' => $combined_data['n8n_confirm_webhook'],
                'rsvp_show_guest_count' => $combined_data['rsvp_show_guest_count'],
                'rsvp_show_qr_code' => $combined_data['rsvp_show_qr_code'],
                'rsvp_show_countdown' => $combined_data['rsvp_show_countdown'] ?? 1
            ];
        } else {
            $error_message = $t['invalid_link'];
        }
        $stmt_guest->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_rsvp']) && !isset($_POST['switch_language'])) {
    header('Content-Type: application/json');

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => $t['csrf_error']]);
        exit;
    }

    if ($is_rate_limited) {
        echo json_encode(['success' => false, 'message' => $t['rate_limit_error']]);
        exit;
    }

    $_SESSION[$rate_limit_key]['count']++;

    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
    $guest_id_post = filter_input(INPUT_POST, 'guest_id', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!in_array($status, ['confirmed', 'canceled']) || empty($guest_id_post)) {
        echo json_encode(['success' => false, 'message' => $t['error_occurred']]);
        exit;
    }

    $sql_update = "UPDATE guests SET status = ?, checkin_time = CASE WHEN ? = 'confirmed' THEN NOW() ELSE checkin_time END WHERE guest_id = ?";

    if ($stmt_update = $mysqli->prepare($sql_update)) {
        $stmt_update->bind_param("sss", $status, $status, $guest_id_post);

        if ($stmt_update->execute() && $stmt_update->affected_rows > 0) {
            if ($status === 'confirmed' && !empty($event_data['n8n_confirm_webhook'])) {
                $webhook_url = filter_var($event_data['n8n_confirm_webhook'], FILTER_VALIDATE_URL);
                if ($webhook_url) {
                    $webhook_payload = json_encode([
                        'guest_id' => $guest_id_post,
                        'phone_number' => $guest_data['phone_number'] ?? '',
                        'timestamp' => time()
                    ]);

                    $ch = curl_init($webhook_url);
                    curl_setopt_array($ch, [
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $webhook_payload,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($webhook_payload)
                        ]
                    ]);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $message = $status === 'confirmed' ? $t['success_confirmed'] : $t['success_declined'];
            echo json_encode(['success' => true, 'message' => $message, 'status' => $status]);
        } else {
            echo json_encode(['success' => false, 'message' => $t['error_occurred']]);
        }
        $stmt_update->close();
    } else {
        echo json_encode(['success' => false, 'message' => $t['error_occurred']]);
    }

    $mysqli->close();
    exit;
}

$event_datetime_iso = '';
$event_date_for_countdown = date('Y-m-d');
if (isset($event_data['event_date_ar'])) {
    try {
        $date = new DateTime($event_data['event_date_ar']);
        $event_datetime_iso = $date->format('Y-m-d\TH:i:s');
        $event_date_for_countdown = $date->format('Y-m-d');
    } catch (Exception $e) {
        $event_datetime_iso = date('Y-m-d\TH:i:s');
    }
}
?>
