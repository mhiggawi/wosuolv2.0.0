<?php
// src/handlers/guests_handler.php

// This file is included by public/guests.php and assumes bootstrap.php has been included.

// Helper function to get translation
function translate($key) {
    global $t;
    return $t[$key] ?? $key;
}

// Security & Permission Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !in_array($_SESSION['role'], ['admin', 'checkin_user', 'viewer'])) {
    header("location: login.php");
    exit;
}

$user_role = $_SESSION['role'];
$user_event_access = $_SESSION['event_id_access'] ?? $_SESSION['event_id'] ?? null;

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT) ?: $user_event_access;

if (!$event_id) {
    echo "<script>alert('" . translate('no_event_specified') . "'); window.location.href='dashboard.php';</script>";
    exit;
}

if ($user_role !== 'admin' && $event_id != $user_event_access) {
    echo "<script>alert('" . translate('access_denied') . "'); window.location.href='dashboard.php';</script>";
    exit;
}

$event_query = "SELECT event_name, Maps_link, venue_ar, venue_en FROM events WHERE id = ?";
$event_stmt = $mysqli->prepare($event_query);
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();
$event = $event_result->fetch_assoc();
$event_stmt->close();

if (!$event) {
    echo "<script>alert('" . translate('not_found_error') . "'); window.location.href='dashboard.php';</script>";
    exit;
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_guest'])) {
            $name_ar = trim($_POST['name_ar']);
            if (empty($name_ar)) throw new Exception(translate('guest_name') . ' ' . translate('required'));

            do {
                $guest_id = substr(str_shuffle('0123456789abcdef'), 0, 4);
                $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM guests WHERE guest_id = ?");
                $check_stmt->bind_param("s", $guest_id);
                $check_stmt->execute();
                $exists = $check_stmt->get_result()->fetch_row()[0];
                $check_stmt->close();
            } while ($exists > 0);

            $insert_query = "INSERT INTO guests (event_id, guest_id, name_ar, phone_number, guests_count, table_number, assigned_location, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $mysqli->prepare($insert_query);
            $insert_stmt->bind_param("isssissss", $event_id, $guest_id, $name_ar, $_POST['phone_number'], $_POST['guests_count'], $_POST['table_number'], $_POST['assigned_location'], $_POST['notes'], $_POST['status']);

            if ($insert_stmt->execute()) {
                $success_message = translate('add_guest_success');
            } else {
                throw new Exception(translate('add_guest_failed'));
            }
            $insert_stmt->close();
        }

        if (isset($_POST['update_guest'])) {
            $update_query = "UPDATE guests SET name_ar = ?, phone_number = ?, guests_count = ?, table_number = ?, assigned_location = ?, notes = ?, status = ? WHERE guest_id = ? AND event_id = ?";
            $update_stmt = $mysqli->prepare($update_query);
            $update_stmt->bind_param("ssisssssi", $_POST['name_ar'], $_POST['phone_number'], $_POST['guests_count'], $_POST['table_number'], $_POST['assigned_location'], $_POST['notes'], $_POST['status'], $_POST['guest_id'], $event_id);

            if ($update_stmt->execute()) {
                $success_message = translate('update_guest_success');
            } else {
                throw new Exception(translate('update_guest_failed'));
            }
            $update_stmt->close();
        }

        if (isset($_POST['delete_guest'])) {
            $delete_query = "DELETE FROM guests WHERE guest_id = ? AND event_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param("si", $_POST['guest_id'], $event_id);
            if ($delete_stmt->execute()) {
                $success_message = translate('delete_guest_success');
            } else {
                throw new Exception(translate('delete_guest_failed'));
            }
            $delete_stmt->close();
        }

        if (isset($_POST['bulk_action'])) {
            $action = $_POST['bulk_action'];
            $selected_guests = $_POST['selected_guests'] ?? [];
            if (empty($selected_guests)) throw new Exception(translate('no_selection_error'));

            $placeholders = str_repeat('?,', count($selected_guests) - 1) . '?';
            $params = [];
            $types = '';

            switch ($action) {
                case 'assign_table':
                    $table_number = trim($_POST['bulk_table_number'] ?? '');
                    if (empty($table_number)) throw new Exception(translate('no_table_error'));
                    $bulk_query = "UPDATE guests SET table_number = ? WHERE guest_id IN ($placeholders) AND event_id = ?";
                    $params = array_merge([$table_number], $selected_guests, [$event_id]);
                    $types = 's' . str_repeat('s', count($selected_guests)) . 'i';
                    break;
                case 'delete':
                    $bulk_query = "DELETE FROM guests WHERE guest_id IN ($placeholders) AND event_id = ?";
                    break;
                default:
                    $status_map = ['confirm' => 'confirmed', 'cancel' => 'canceled'];
                    if (isset($status_map[$action])) {
                        $bulk_query = "UPDATE guests SET status = ? WHERE guest_id IN ($placeholders) AND event_id = ?";
                        $params = array_merge([$status_map[$action]], $selected_guests, [$event_id]);
                        $types = 's' . str_repeat('s', count($selected_guests)) . 'i';
                    } elseif ($action === 'checkin') {
                        $bulk_query = "UPDATE guests SET checkin_status = 'checked_in', checkin_time = NOW() WHERE guest_id IN ($placeholders) AND event_id = ?";
                    } else {
                        throw new Exception('Invalid action');
                    }
            }

            if (empty($params)) {
                $params = array_merge($selected_guests, [$event_id]);
                $types = str_repeat('s', count($selected_guests)) . 'i';
            }

            $bulk_stmt = $mysqli->prepare($bulk_query);
            $bulk_stmt->bind_param($types, ...$params);

            if ($bulk_stmt->execute()) {
                $success_message = translate('action_success') . " " . $bulk_stmt->affected_rows . " " . ( $bulk_stmt->affected_rows === 1 ? translate('guest_singular') : translate('guests_plural') );
            } else {
                throw new Exception(translate('action_failed') . ': ' . $mysqli->error);
            }
            $bulk_stmt->close();
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$table_filter = $_GET['table'] ?? '';
$where_conditions = ["event_id = ?"];
$params = [$event_id];
$types = "i";

if (!empty($search)) {
    $where_conditions[] = "(name_ar LIKE ? OR phone_number LIKE ? OR guest_id LIKE ?)";
    $search_term = "%$search%";
    array_push($params, $search_term, $search_term, $search_term);
    $types .= "sss";
}
if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if (!empty($table_filter)) {
    $where_conditions[] = "table_number = ?";
    $params[] = $table_filter;
    $types .= "s";
}

$where_clause = implode(" AND ", $where_conditions);
$guests_query = "SELECT * FROM guests WHERE $where_clause ORDER BY name_ar ASC";
$guests_stmt = $mysqli->prepare($guests_query);
$guests_stmt->bind_param($types, ...$params);
$guests_stmt->execute();
$guests = $guests_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$guests_stmt->close();

$stats_query = "SELECT COUNT(*) as total_guests, SUM(guests_count) as total_people, SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_guests, SUM(CASE WHEN status = 'confirmed' THEN guests_count ELSE 0 END) as confirmed_people, SUM(CASE WHEN checkin_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in_guests, SUM(CASE WHEN checkin_status = 'checked_in' THEN guests_count ELSE 0 END) as checked_in_people, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_guests, SUM(CASE WHEN status = 'pending' THEN guests_count ELSE 0 END) as pending_people FROM guests WHERE event_id = ?";
$stats_stmt = $mysqli->prepare($stats_query);
$stats_stmt->bind_param("i", $event_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
$stats_stmt->close();

$tables_query = "SELECT DISTINCT table_number FROM guests WHERE event_id = ? AND table_number IS NOT NULL AND table_number != '' ORDER BY table_number";
$tables_stmt = $mysqli->prepare($tables_query);
$tables_stmt->bind_param("i", $event_id);
$tables_stmt->execute();
$tables = $tables_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$tables_stmt->close();
?>
