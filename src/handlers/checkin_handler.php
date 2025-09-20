<?php
// src/handlers/checkin_handler.php

// This file is included by public/checkin.php and assumes bootstrap.php has been included.

// --- Security & Permission Check ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !in_array($_SESSION['role'], ['admin', 'checkin_user', 'viewer'])) {
    header('Location: login.php');
    exit;
}

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
$user_role = $_SESSION['role'];
$user_event_access = $_SESSION['event_id_access'] ?? null;

if (!$event_id) {
    if ($user_role === 'admin') { header('Location: events.php'); exit; }
    else { die('Access Denied: Event ID is required.'); }
}

if ($user_role !== 'admin' && $event_id != $user_event_access) {
    die('Access Denied: You do not have permission to access this check-in page.');
}

// Check if user is viewer (read-only mode)
$isViewerMode = ($user_role === 'viewer');

// --- API Logic ---
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    $api_event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
    $input = json_decode(file_get_contents('php://input'), true);

    // Security check inside API
    if ($_SESSION['role'] !== 'admin' && $api_event_id != ($_SESSION['event_id_access'] ?? null)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'وصول غير مصرح به.']);
        exit;
    }

    // --- Stats API ---
    if (isset($_GET['stats'])) {
        $today = date('Y-m-d');

        $stmt_today = $mysqli->prepare("SELECT COUNT(*) as today_count FROM guests WHERE event_id = ? AND checkin_status = 'checked_in' AND DATE(checkin_time) = ?");
        $stmt_today->bind_param("is", $api_event_id, $today);
        $stmt_today->execute();
        $today_checkins = $stmt_today->get_result()->fetch_assoc()['today_count'];
        $stmt_today->close();

        $stmt_confirmed = $mysqli->prepare("SELECT COUNT(*) as confirmed_count FROM guests WHERE event_id = ? AND status = 'confirmed'");
        $stmt_confirmed->bind_param("i", $api_event_id);
        $stmt_confirmed->execute();
        $total_confirmed = $stmt_confirmed->get_result()->fetch_assoc()['confirmed_count'];
        $stmt_confirmed->close();

        $stmt_pending = $mysqli->prepare("SELECT COUNT(*) as pending_count FROM guests WHERE event_id = ? AND status = 'pending'");
        $stmt_pending->bind_param("i", $api_event_id);
        $stmt_pending->execute();
        $total_pending = $stmt_pending->get_result()->fetch_assoc()['pending_count'];
        $stmt_pending->close();

        $stmt_remaining = $mysqli->prepare("SELECT COUNT(*) as remaining_count FROM guests WHERE event_id = ? AND status = 'confirmed' AND checkin_status != 'checked_in'");
        $stmt_remaining->bind_param("i", $api_event_id);
        $stmt_remaining->execute();
        $remaining_guests = $stmt_remaining->get_result()->fetch_assoc()['remaining_count'];
        $stmt_remaining->close();

        echo json_encode([
            'today_checkins' => $today_checkins,
            'total_confirmed' => $total_confirmed,
            'total_pending' => $total_pending,
            'remaining_guests' => $remaining_guests
        ]);
        exit;
    }

    // --- Recent Check-ins API ---
    if (isset($_GET['recent'])) {
        $stmt = $mysqli->prepare("SELECT name_ar, checkin_time, notes FROM guests WHERE event_id = ? AND checkin_status = 'checked_in' ORDER BY checkin_time DESC LIMIT 10");
        $stmt->bind_param("i", $api_event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode($recent);
        exit;
    }

    // --- Suggestion Mode ---
    if (isset($_GET['suggest'])) {
        $searchTerm = trim($input['searchTerm'] ?? '');
        if (empty($searchTerm) || !$api_event_id) {
            echo json_encode([]);
            exit;
        }

        $searchTermLike = "%" . $searchTerm . "%";
        $stmt = $mysqli->prepare("SELECT guest_id, name_ar, phone_number, status, checkin_status, table_number, guests_count, notes FROM guests WHERE (name_ar LIKE ? OR phone_number LIKE ? OR table_number LIKE ?) AND event_id = ? LIMIT 10");
        $stmt->bind_param("sssi", $searchTermLike, $searchTermLike, $searchTermLike, $api_event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $guests = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        echo json_encode($guests);
        exit;
    }

    // --- Add Note API ---
    if (isset($_GET['add_note'])) {
        $guest_id = trim($input['guest_id'] ?? '');
        $note = trim($input['note'] ?? '');

        if (empty($guest_id) || empty($note)) {
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            exit;
        }

        $stmt = $mysqli->prepare("SELECT notes FROM guests WHERE guest_id = ? AND event_id = ?");
        $stmt->bind_param("si", $guest_id, $api_event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $guest = $result->fetch_assoc();
        $stmt->close();

        if (!$guest) {
            echo json_encode(['success' => false, 'message' => 'Guest not found']);
            exit;
        }

        $current_notes = $guest['notes'] ?? '';
        $timestamp = date('Y-m-d H:i:s');
        $new_note = "[{$timestamp}] {$note}";
        $updated_notes = empty($current_notes) ? $new_note : $current_notes . "\n" . $new_note;

        $stmt = $mysqli->prepare("UPDATE guests SET notes = ? WHERE guest_id = ? AND event_id = ?");
        $stmt->bind_param("ssi", $updated_notes, $guest_id, $api_event_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => $t['note_added']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add note']);
        }
        $stmt->close();
        exit;
    }

    // --- Check-in Logic ---
    $response = ['success' => false, 'message' => 'حدث خطأ غير متوقع.'];
    $searchTerm = trim($input['searchTerm'] ?? '');
    $confirmAndCheckin = $input['confirmAndCheckin'] ?? false;

    if (empty($searchTerm) || !$api_event_id) {
        $response['message'] = 'بيانات ناقصة (مصطلح البحث مطلوب).';
        echo json_encode($response);
        exit;
    }

    $searchTermLike = "%" . $searchTerm . "%";
    $stmt = $mysqli->prepare("SELECT * FROM guests WHERE (guest_id = ? OR name_ar LIKE ? OR phone_number LIKE ?) AND event_id = ?");
    $stmt->bind_param("sssi", $searchTerm, $searchTermLike, $searchTermLike, $api_event_id);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        $response['message'] = $t['guest_not_found'];
    } elseif ($result->num_rows === 1) {
        $guest = $result->fetch_assoc();

        if ($guest['status'] === 'confirmed') {
            if ($guest['checkin_status'] === 'checked_in') {
                $response['success'] = true;
                $response['message'] = str_replace('{name}', htmlspecialchars($guest['name_ar']), $t['guest_already_checked_in']);
                $response['type'] = 'warning';
                $response['guestDetails'] = $guest;
            } else {
                $update_stmt = $mysqli->prepare("UPDATE guests SET checkin_status = 'checked_in', checkin_time = NOW() WHERE guest_id = ?");
                $update_stmt->bind_param("s", $guest['guest_id']);
                if ($update_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = str_replace('{name}', htmlspecialchars($guest['name_ar']), $t['guest_checked_in_success']);
                    $response['type'] = 'success';
                    $guest['checkin_status'] = 'checked_in';
                    $response['guestDetails'] = $guest;
                }
                $update_stmt->close();
            }
        } elseif ($guest['status'] === 'canceled') {
            $response['message'] = str_replace('{name}', htmlspecialchars($guest['name_ar']), $t['guest_declined']);
            $response['type'] = 'error';
            $response['guestDetails'] = $guest;
        } elseif ($guest['status'] === 'pending') {
            if ($confirmAndCheckin) {
                $update_stmt = $mysqli->prepare("UPDATE guests SET status = 'confirmed', checkin_status = 'checked_in', checkin_time = NOW() WHERE guest_id = ?");
                $update_stmt->bind_param("s", $guest['guest_id']);
                if ($update_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = str_replace('{name}', htmlspecialchars($guest['name_ar']), $t['guest_confirmed_and_checked_in']);
                    $response['type'] = 'success';
                    $guest['status'] = 'confirmed';
                    $guest['checkin_status'] = 'checked_in';
                    $response['guestDetails'] = $guest;
                }
                $update_stmt->close();
            } else {
                $response['message'] = str_replace('{name}', htmlspecialchars($guest['name_ar']), $t['guest_pending_options']);
                $response['type'] = 'pending';
                $response['showConfirmOption'] = true;
                $response['guestDetails'] = $guest;
            }
        }
    } else {
        $response['message'] = $t['multiple_guests_found'];
        $response['type'] = 'warning';
        $response['multipleResults'] = true;
    }

    echo json_encode($response);
    $mysqli->close();
    exit;
}

// --- Fetch Event Name for Display ---
$event_name = 'تسجيل دخول الضيوف';
$stmt_event = $mysqli->prepare("SELECT event_name FROM events WHERE id = ?");
$stmt_event->bind_param("i", $event_id);
if ($stmt_event->execute()) {
    $result = $stmt_event->get_result();
    if ($row = $result->fetch_assoc()) { $event_name = $row['event_name']; }
}
$stmt_event->close();
?>
