<?php
// src/handlers/events_handler.php

// This file is included by public/events.php and assumes bootstrap.php has been included.

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_backup']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id) {
            require_once 'auto_backup.php';
            $backup = new AutoBackup($mysqli);
            $result = $backup->backupSingleEvent($event_id);

            if ($result['success']) {
                $message = $t['backup_created'] . ' - ' . $result['filename'];
                $messageType = 'success';
            } else {
                $message = $t['backup_failed'] . ': ' . $result['message'];
                $messageType = 'error';
            }
        }
        header('Location: events.php?message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['messaging_action']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $action = $_POST['messaging_action'];

        switch ($action) {
            case 'send_to_all':
                $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
                if ($event_id) {
                    $result = sendToAllGuestsForEvent($event_id, $mysqli);

                    saveSendResults($event_id, 'send_event_all', $result, $mysqli);

                    $message = $result['success'] ? $t['messages_sent_success'] : $t['messaging_error'];
                    $messageType = $result['success'] ? 'success' : 'error';

                    if ($result['success'] && isset($result['response'])) {
                        $response_data = json_decode($result['response'], true);
                        if ($response_data && isset($response_data['summary'])) {
                            $summary = $response_data['summary'];
                            $success_text = $lang === 'ar' ? 'نجح' : 'Success';
                            $failed_text = $lang === 'ar' ? 'فشل' : 'Failed';
                            $message .= " ($success_text: {$summary['successCount']}, $failed_text: {$summary['failureCount']})";
                        }
                    }
                }
                break;

            case 'send_to_selected':
                $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
                $selected_guests = json_decode($_POST['selected_guests'] ?? '[]', true);
                if ($event_id && !empty($selected_guests)) {
                    $result = sendToSelectedGuests($event_id, $selected_guests, $mysqli);

                    saveSendResults($event_id, 'send_selected', $result, $mysqli, count($selected_guests));

                    $message = $result['success'] ? $t['messages_sent_success'] : $t['messaging_error'];
                    $messageType = $result['success'] ? 'success' : 'error';
                }
                break;

            case 'global_send_all':
                $result = sendGlobalMessages($mysqli);

                saveSendResults(null, 'send_global_all', $result, $mysqli);

                $message = $result['success'] ? $t['global_messages_sent'] : $t['messaging_error'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
        }
        header('Location: events.php?message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    if (isset($_GET['get_guests'])) {
        $event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id) {
            $stmt = $mysqli->prepare("SELECT id, guest_id, name_ar, phone_number, status FROM guests WHERE event_id = ? ORDER BY name_ar ASC");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $guests = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            echo json_encode($guests);
        }
        exit;
    }

    if (isset($_GET['get_stats'])) {
        $event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id) {
            $stmt = $mysqli->prepare("SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status NOT IN ('confirmed', 'canceled') THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN checkin_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in
                FROM guests WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stats = $result->fetch_assoc();
            $stmt->close();
            echo json_encode($stats);
        }
        exit;
    }

    if (isset($_GET['get_send_results'])) {
        $event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id) {
            $results = getLastSendResults($event_id, $mysqli);
            echo json_encode($results);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        if ($delete_id) {
            $stmt = $mysqli->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $message = $t['event_deleted_success']; $messageType = 'success';
            } else {
                $message = $t['event_deletion_error']; $messageType = 'error';
            }
            $stmt->close();
        }
        header('Location: events.php?message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $eventName = trim($_POST['event_name']);
        if (!empty($eventName)) {
            $event_slug = generate_slug($eventName);

            $original_slug = $event_slug;
            $counter = 1;

            $slug_check = $mysqli->prepare("SELECT COUNT(*) as count FROM events WHERE event_slug = ?");
            while (true) {
                $slug_check->bind_param("s", $event_slug);
                $slug_check->execute();
                $result_count = $slug_check->get_result();
                $count_row = $result_count->fetch_assoc();

                if ($count_row['count'] == 0) {
                    break;
                }

                $event_slug = $original_slug . '-' . $counter;
                $counter++;
            }
            $slug_check->close();

            $default_ar = $lang === 'ar' ? 'يرجى التحديث من لوحة التحكم' : 'Please update from control panel';
            $default_settings = [
                'qr_card_title_ar' => 'دعوة حفل زفاف',
                'qr_card_title_en' => 'Wedding Invitation',
                'qr_show_code_instruction_ar' => 'يرجى إظهار هذا الرمز عند الدخول',
                'qr_show_code_instruction_en' => 'Please show this code at entrance',
                'qr_brand_text_ar' => 'wosuol | وصول',
                'qr_brand_text_en' => 'wosuol | وصول',
                'qr_website' => 'wosuol.com',
                'n8n_confirm_webhook' => '',
                'n8n_initial_invite_webhook' => ''
            ];

            $stmt = $mysqli->prepare("INSERT INTO events (event_name, event_slug, bride_name_ar, groom_name_ar, event_date_ar, venue_ar, qr_card_title_ar, qr_card_title_en, qr_show_code_instruction_ar, qr_show_code_instruction_en, qr_brand_text_ar, qr_brand_text_en, qr_website, n8n_confirm_webhook, n8n_initial_invite_webhook) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt === false) {
                error_log("MySQL prepare error: " . $mysqli->error);
                $message = $t['event_creation_error'] . " (Prepare failed)";
                $messageType = 'error';
            } else {
                $stmt->bind_param("sssssssssssssss",
                    $eventName, $event_slug, $default_ar, $default_ar, $default_ar, $default_ar,
                    $default_settings['qr_card_title_ar'], $default_settings['qr_card_title_en'],
                    $default_settings['qr_show_code_instruction_ar'], $default_settings['qr_show_code_instruction_en'],
                    $default_settings['qr_brand_text_ar'], $default_settings['qr_brand_text_en'],
                    $default_settings['qr_website'], $default_settings['n8n_confirm_webhook'], $default_settings['n8n_initial_invite_webhook']
                );

                if ($stmt->execute()) {
                    $message = $t['event_created_success'] . " (Slug: $event_slug)";
                    $messageType = 'success';
                } else {
                    error_log("MySQL execute error: " . $stmt->error);
                    $message = $t['event_creation_error'] . " (Execute failed: " . $stmt->error . ")";
                    $messageType = 'error';
                }
                $stmt->close();
            }
        } else {
            $message = $t['enter_event_name']; $messageType = 'error';
        }
        header('Location: events.php?message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
    $messageType = $_GET['messageType'] ?? 'success';
}

$events = [];
$result = $mysqli->query("
    SELECT e.id, e.event_name, e.event_slug, e.event_date_ar, e.created_at,
           e.n8n_initial_invite_webhook,
           COUNT(g.id) as guest_count,
           SUM(CASE WHEN g.status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count
    FROM events e
    LEFT JOIN guests g ON e.id = g.event_id
    GROUP BY e.id
    ORDER BY e.created_at DESC
");
if ($result) {
    $events = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

$mysqli->query("
    CREATE TABLE IF NOT EXISTS send_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NULL,
        action_type VARCHAR(50) NOT NULL,
        success_count INT DEFAULT 0,
        failed_count INT DEFAULT 0,
        total_processed INT DEFAULT 0,
        target_count INT NULL,
        response_data TEXT NULL,
        http_code INT DEFAULT 0,
        created_at DATETIME NOT NULL,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )
");
?>
