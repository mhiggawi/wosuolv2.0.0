<?php
// src/handlers/admin_handler.php

// This file is included by public/admin.php and assumes bootstrap.php has been included.
// It handles all the business logic for the admin page.

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Security Check & Get Event ID ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    // Redirect to the new login page location
    header('Location: login.php');
    exit;
}

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if (!$event_id) {
    header('Location: events.php');
    exit;
}

$message = '';
$messageType = '';

// --- Default QR and Webhook Settings ---
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

// --- Handle Form Submission ---
// User Management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_action']) && !isset($_POST['switch_language'])) {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $action = $_POST['user_action'];
        $username = trim($_POST['username'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $user_event_id = !empty($_POST['user_event_id']) ? filter_input(INPUT_POST, 'user_event_id', FILTER_VALIDATE_INT) : NULL;
            if ($role === 'admin') { $user_event_id = NULL; }
        }

        switch ($action) {
            case 'add':
                if (!empty($username) && !empty($password) && !empty($role)) {
                    $stmt_check = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt_check->bind_param("s", $username);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    if ($stmt_check->num_rows > 0) {
                        $message = $t['user_exists']; $messageType = 'error';
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt_insert = $mysqli->prepare("INSERT INTO users (username, password_hash, role, event_id) VALUES (?, ?, ?, ?)");
                        $stmt_insert->bind_param("sssi", $username, $hashedPassword, $role, $user_event_id);
                        if ($stmt_insert->execute()) {
                            $message = $t['user_added_success']; $messageType = 'success';
                        } else {
                            $message = $t['user_add_error']; $messageType = 'error';
                        }
                        $stmt_insert->close();
                    }
                    $stmt_check->close();
                } else {
                    $message = $t['all_fields_required']; $messageType = 'error';
                }
                break;

            case 'edit':
                $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
                if (!empty($user_id) && !empty($username) && !empty($role)) {
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $mysqli->prepare("UPDATE users SET username = ?, password_hash = ?, role = ?, event_id = ? WHERE id = ?");
                        $stmt->bind_param("sssii", $username, $hashedPassword, $role, $user_event_id, $user_id);
                    } else {
                        $stmt = $mysqli->prepare("UPDATE users SET username = ?, role = ?, event_id = ? WHERE id = ?");
                        $stmt->bind_param("ssii", $username, $role, $user_event_id, $user_id);
                    }
                    if ($stmt->execute()) {
                        $message = $t['user_updated_success']; $messageType = 'success';
                    } else {
                        $message = $t['user_update_error']; $messageType = 'error';
                    }
                    $stmt->close();
                }
                break;

            case 'delete':
                if (!empty($username)) {
                    $stmt = $mysqli->prepare("DELETE FROM users WHERE username = ? AND username != ?");
                    $stmt->bind_param("ss", $username, $_SESSION['username']);
                    if ($stmt->execute()) {
                        $message = $t['user_deleted_success']; $messageType = 'success';
                    } else {
                        $message = $t['user_delete_error']; $messageType = 'error';
                    }
                    $stmt->close();
                }
                break;
        }
        header('Location: admin.php?event_id=' . $event_id . '&message=' . urlencode($message) . '&messageType=' . $messageType . '&tab=user-management');
        exit;
    }
}

// Event Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event_settings']) && !isset($_POST['switch_language'])) {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        // --- Stage 1: Collect Data into an Array ---
        $update_data = [];

        $update_data['event_name'] = trim($_POST['event_name'] ?? '');
        $update_data['event_name_en'] = trim($_POST['event_name_en'] ?? '');
        $update_data['event_slug'] = trim($_POST['event_slug'] ?? '');

        $event_datetime = trim($_POST['event_datetime'] ?? '');
        if (!empty($event_datetime)) {
            $dateTimeObj = new DateTime($event_datetime);
            $update_data['event_date_ar'] = $dateTimeObj->format('Y-m-d H:i:s');
            $update_data['event_date_en'] = $dateTimeObj->format('l, F j, Y h:i A');
        } else {
            $update_data['event_date_ar'] = null;
            $update_data['event_date_en'] = null;
        }

        $update_data['venue_ar'] = trim($_POST['venue_ar'] ?? '');
        $update_data['venue_en'] = trim($_POST['venue_en'] ?? '');
        $update_data['Maps_link'] = trim($_POST['maps_link'] ?? '');
        $update_data['event_paragraph_ar'] = trim($_POST['event_paragraph_ar'] ?? '');
        $update_data['event_paragraph_en'] = trim($_POST['event_paragraph_en'] ?? '');

        // Image Handling
        $upload_dir = __DIR__ . '/../../public/uploads/';

        $current_display_image = $_POST['current_display_image'] ?? '';
        if (isset($_FILES['display_image_upload']) && $_FILES['display_image_upload']['error'] === UPLOAD_ERR_OK) {
            if ($new_path = handle_upload($_FILES['display_image_upload'], $upload_dir, 'display_event_' . $event_id)) {
                if (!empty($current_display_image) && file_exists(__DIR__ . '/../../public/' . $current_display_image)) { unlink(__DIR__ . '/../../public/' . $current_display_image); }
                $current_display_image = $new_path;
                $message = $t['image_saved_success']; $messageType = 'success';
            }
        } elseif (isset($_POST['remove_display_image']) && $_POST['remove_display_image'] === '1') {
            if (!empty($current_display_image) && file_exists(__DIR__ . '/../../public/' . $current_display_image)) { unlink(__DIR__ . '/../../public/' . $current_display_image); }
            $current_display_image = '';
            $message = $t['image_removed_success']; $messageType = 'success';
        }

        $current_whatsapp_image = $_POST['current_whatsapp_image'] ?? '';
        if (isset($_FILES['whatsapp_image_upload']) && $_FILES['whatsapp_image_upload']['error'] === UPLOAD_ERR_OK) {
            if ($new_path = handle_upload($_FILES['whatsapp_image_upload'], $upload_dir, 'whatsapp_event_' . $event_id)) {
                if (!empty($current_whatsapp_image) && file_exists(__DIR__ . '/../../public/' . $current_whatsapp_image)) { unlink(__DIR__ . '/../../public/' . $current_whatsapp_image); }
                $current_whatsapp_image = $new_path;
                if(empty($message)) { $message = $t['image_saved_success']; $messageType = 'success'; }
            }
        } elseif (isset($_POST['remove_whatsapp_image']) && $_POST['remove_whatsapp_image'] === '1') {
            if (!empty($current_whatsapp_image) && file_exists(__DIR__ . '/../../public/' . $current_whatsapp_image)) { unlink(__DIR__ . '/../../public/' . $current_whatsapp_image); }
            $current_whatsapp_image = '';
            if(empty($message)) { $message = $t['image_removed_success']; $messageType = 'success'; }
        }

        $update_data['background_image_url'] = $current_display_image;
        $update_data['whatsapp_image_url'] = $current_whatsapp_image;

        // Other Settings
        $update_data['qr_card_title_ar'] = trim($_POST['qr_card_title_ar'] ?? '') ?: $default_settings['qr_card_title_ar'];
        $update_data['qr_card_title_en'] = trim($_POST['qr_card_title_en'] ?? '') ?: $default_settings['qr_card_title_en'];
        $update_data['qr_show_code_instruction_ar'] = trim($_POST['qr_show_code_instruction_ar'] ?? '') ?: $default_settings['qr_show_code_instruction_ar'];
        $update_data['qr_show_code_instruction_en'] = trim($_POST['qr_show_code_instruction_en'] ?? '') ?: $default_settings['qr_show_code_instruction_en'];
        $update_data['qr_brand_text_ar'] = trim($_POST['qr_brand_text_ar'] ?? '') ?: $default_settings['qr_brand_text_ar'];
        $update_data['qr_brand_text_en'] = trim($_POST['qr_brand_text_en'] ?? '') ?: $default_settings['qr_brand_text_en'];
        $update_data['qr_website'] = trim($_POST['qr_website'] ?? '') ?: $default_settings['qr_website'];
        $update_data['n8n_confirm_webhook'] = trim($_POST['n8n_confirm_webhook'] ?? '') ?: $default_settings['n8n_confirm_webhook'];
        $update_data['n8n_initial_invite_webhook'] = trim($_POST['n8n_initial_invite_webhook'] ?? '') ?: $default_settings['n8n_initial_invite_webhook'];
        $update_data['registration_show_phone'] = isset($_POST['registration_show_phone']) ? 1 : 0;
        $update_data['registration_require_phone'] = isset($_POST['registration_require_phone']) ? 1 : 0;
        $update_data['registration_show_guest_count'] = isset($_POST['registration_show_guest_count']) ? 1 : 0;
        $update_data['registration_show_countdown'] = isset($_POST['registration_show_countdown']) ? 1 : 0;
        $update_data['registration_show_location'] = isset($_POST['registration_show_location']) ? 1 : 0;
        $update_data['registration_mode'] = in_array($_POST['registration_mode'] ?? '', ['simple', 'full']) ? $_POST['registration_mode'] : 'full';
        $update_data['rsvp_show_guest_count'] = isset($_POST['rsvp_show_guest_count']) ? 1 : 0;
        $update_data['rsvp_show_qr_code'] = isset($_POST['rsvp_show_qr_code']) ? 1 : 0;

        $sql_parts = [];
        $types = '';
        $values = [];

        foreach ($update_data as $key => $value) {
            $sql_parts[] = "`{$key}` = ?";
            $values[] = $value;
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        $sql = "UPDATE `events` SET " . implode(', ', $sql_parts) . " WHERE `id` = ?";
        $values[] = $event_id;
        $types .= 'i';

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            if (empty($message)) {
                $message = 'تم حفظ الإعدادات بنجاح.'; $messageType = 'success';
            }
        } else {
            $message = 'حدث خطأ أثناء حفظ الإعدادات: ' . $stmt->error; $messageType = 'error';
        }
        $stmt->close();
        header('Location: admin.php?event_id=' . $event_id . '&message=' . urlencode($message) . '&messageType=' . $messageType . '&tab=general-settings');
        exit;
    }
}

// --- Data Fetching ---
$users = [];
$result_users = $mysqli->query("SELECT id, username, role, event_id FROM users ORDER BY username ASC");
if ($result_users) { $users = $result_users->fetch_all(MYSQLI_ASSOC); }

$all_events = [];
$result_events_list = $mysqli->query("SELECT id, event_name FROM events ORDER BY event_name ASC");
if ($result_events_list) { $all_events = $result_events_list->fetch_all(MYSQLI_ASSOC); }

$event = [];
$stmt_event = $mysqli->prepare("SELECT * FROM events WHERE id = ?");
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$result_event = $stmt_event->get_result();
if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();

    foreach ($default_settings as $key => $default_value) {
        if (empty($event[$key])) {
            $event[$key] = $default_value;
        }
    }
} else {
    header('Location: events.php'); exit;
}
$stmt_event->close();

if (isset($_GET['message'])) {
    $message = htmlspecialchars(urldecode($_GET['message']));
    $messageType = htmlspecialchars($_GET['messageType']);
}

$datetime_value = '';
if (!empty($event['event_date_ar'])) {
    $datetime_obj = parseEventDateTime($event['event_date_ar']);
    if ($datetime_obj) {
        $datetime_value = $datetime_obj->format('Y-m-d\TH:i');
    }
}
?>
