<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

// Security & Permission Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !in_array($_SESSION['role'], ['admin', 'checkin_user', 'viewer'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if (!$event_id) {
    // Try to get from session as a fallback for viewer
    $event_id = $_SESSION['event_id_access'] ?? $_SESSION['event_id'] ?? null;
}

$user_role = $_SESSION['role'];
$user_event_access = $_SESSION['event_id_access'] ?? $_SESSION['event_id'] ?? null;

if (!$event_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Event ID is required']);
    exit;
}

if ($user_role !== 'admin' && $event_id != $user_event_access) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied for this event']);
    exit;
}

// Handle Fetching the Message Template from DB
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_template') {
    $lang = $_SESSION['lang'] ?? 'ar';
    $column = ($lang === 'en') ? 'message_template_en' : 'message_template';
    
    $stmt = $mysqli->prepare("SELECT `$column` FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'template' => $row[$column]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Template not found or empty']);
    }
    $stmt->close();
    exit;
}

// Handle Saving the Message Template to DB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_template') {
    $template_content = $_POST['template_content'] ?? null;
    $language_code = $_POST['language'] ?? 'ar';
    
    if (is_null($template_content)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Template content is missing']);
        exit;
    }
    
    $column = ($language_code === 'en') ? 'message_template_en' : 'message_template';
    $update_query = "UPDATE events SET `$column` = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("si", $template_content, $event_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Template saved successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save template']);
    }
    $stmt->close();
    exit;
}


// Handle Quick Status Update from the main table dropdown
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_status_update'])) {
    $guest_id = $_POST['guest_id'] ?? null;
    $new_status = $_POST['new_status'] ?? null;

    if (!$guest_id || !in_array($new_status, ['pending', 'confirmed', 'canceled'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        exit;
    }

    $update_query = "UPDATE guests SET status = ? WHERE guest_id = ? AND event_id = ?";
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("ssi", $new_status, $guest_id, $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم تحديث الحالة بنجاح']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'فشل تحديث الحالة في قاعدة البيانات']);
    }
    $stmt->close();
    exit;
}


// Handle Fetching All Guests for Export functionality
if (isset($_GET['fetch_guests'])) {
    $guests = [];
    $stmt = $mysqli->prepare("SELECT guest_id, name_ar, phone_number, status, guests_count, checkin_status, table_number, assigned_location, notes FROM guests WHERE event_id = ? ORDER BY name_ar ASC");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $guests = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
    
    echo json_encode($guests);
    exit;
}

// If no specific action is matched, return an error
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid API request']);
$mysqli->close();
?>