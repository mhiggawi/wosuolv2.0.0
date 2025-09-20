<?php
// backup_event.php - نظام النسخ الاحتياطي للأحداث
session_start();
require_once 'db_config.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// CSRF token generation and validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed.');
}

// Language system
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
$texts = [
    'ar' => [
        'event_backup' => 'النسخ الاحتياطي للحفلات',
        'backup_event' => 'أخذ نسخة احتياطية',
        'restore_event' => 'استرجاع حفل',
        'backup_list' => 'قائمة النسخ الاحتياطية',
        'select_event' => 'اختر الحفل للنسخ الاحتياطي',
        'event_name' => 'اسم الحفل',
        'guests_count' => 'عدد الضيوف',
        'create_backup' => 'إنشاء نسخة احتياطية',
        'backup_created' => 'تم إنشاء النسخة الاحتياطية بنجاح',
        'backup_failed' => 'فشل في إنشاء النسخة الاحتياطية',
        'restore_success' => 'تم استرجاع الحفل بنجاح',
        'restore_failed' => 'فشل في استرجاع الحفل',
        'backup_file' => 'ملف النسخة الاحتياطية',
        'created_at' => 'تاريخ الإنشاء',
        'file_size' => 'حجم الملف',
        'actions' => 'الإجراءات',
        'download' => 'تحميل',
        'restore' => 'استرجاع',
        'delete_backup' => 'حذف النسخة',
        'confirm_restore' => 'هل أنت متأكد من استرجاع هذا الحفل؟ سيتم إنشاء حفل جديد.',
        'confirm_delete' => 'هل أنت متأكد من حذف هذه النسخة الاحتياطية؟',
        'upload_backup' => 'رفع نسخة احتياطية',
        'select_file' => 'اختر ملف النسخة الاحتياطية',
        'upload_restore' => 'رفع واسترجاع',
        'no_events' => 'لا توجد أحداث متاحة',
        'no_backups' => 'لا توجد نسخ احتياطية',
        'back_to_events' => 'العودة للأحداث',
        'backup_info' => 'معلومات النسخة الاحتياطية',
        'original_event' => 'الحفل الأصلي',
        'includes_data' => 'تشمل البيانات: الحفل، الضيوف، الصور، الإعدادات',
        'invalid_file' => 'ملف غير صالح',
        'upload_error' => 'خطأ في الرفع'
    ],
    'en' => [
        'event_backup' => 'Event Backup System',
        'backup_event' => 'Create Backup',
        'restore_event' => 'Restore Event',
        'backup_list' => 'Backup List',
        'select_event' => 'Select Event for Backup',
        'event_name' => 'Event Name',
        'guests_count' => 'Guests Count',
        'create_backup' => 'Create Backup',
        'backup_created' => 'Backup created successfully',
        'backup_failed' => 'Failed to create backup',
        'restore_success' => 'Event restored successfully',
        'restore_failed' => 'Failed to restore event',
        'backup_file' => 'Backup File',
        'created_at' => 'Created At',
        'file_size' => 'File Size',
        'actions' => 'Actions',
        'download' => 'Download',
        'restore' => 'Restore',
        'delete_backup' => 'Delete Backup',
        'confirm_restore' => 'Are you sure you want to restore this event? A new event will be created.',
        'confirm_delete' => 'Are you sure you want to delete this backup?',
        'upload_backup' => 'Upload Backup',
        'select_file' => 'Select backup file',
        'upload_restore' => 'Upload & Restore',
        'no_events' => 'No events available',
        'no_backups' => 'No backups available',
        'back_to_events' => 'Back to Events',
        'backup_info' => 'Backup Information',
        'original_event' => 'Original Event',
        'includes_data' => 'Includes: Event, Guests, Images, Settings',
        'invalid_file' => 'Invalid file',
        'upload_error' => 'Upload error'
    ]
];
$t = $texts[$lang];

// Create backup directory if not exists
$backup_dir = 'backups';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$message = '';
$messageType = '';

// --- Functions Section ---

function arabicToEnglish($text) {
    $arabic = ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'ة', 'ى', 'ء'];
    $english = ['a', 'b', 't', 'th', 'j', 'h', 'kh', 'd', 'th', 'r', 'z', 's', 'sh', 's', 'd', 't', 'th', 'a', 'gh', 'f', 'q', 'k', 'l', 'm', 'n', 'h', 'w', 'y', 'h', 'a', 'a'];
    
    $text = str_replace($arabic, $english, $text);
    $text = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $text);
    $text = preg_replace('/_+/', '_', $text);
    $text = trim($text, '_');
    
    return $text;
}

function createSafeFilename($event_name, $event_id) {
    $short_name = mb_substr($event_name, 0, 20, 'UTF-8');
    $safe_name = arabicToEnglish($short_name);
    
    if (empty($safe_name) || $safe_name === '_') {
        $safe_name = 'event';
    }
    
    return 'backup_' . $safe_name . '_id' . $event_id . '_' . date('Y-m-d_H-i-s');
}

function createEventBackup($event_id, $mysqli) {
    global $backup_dir;

    // Get event data
    $stmt = $mysqli->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$event) {
        return ['success' => false, 'message' => 'Event not found.'];
    }

    // Get guests data
    $stmt = $mysqli->prepare("SELECT * FROM guests WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $guests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get send results
    $stmt = $mysqli->prepare("SELECT * FROM send_results WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $send_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get reminder logs
    $stmt = $mysqli->prepare("SELECT * FROM reminder_logs WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $reminder_logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $backup_data = [
        'backup_info' => [
            'created_at' => date('Y-m-d H:i:s'),
            'original_event_id' => $event_id,
            'version' => '1.0',
            'system' => 'WOSUOL Events'
        ],
        'event' => $event,
        'guests' => $guests,
        'send_results' => $send_results,
        'reminder_logs' => $reminder_logs,
        'images' => []
    ];
    
    $image_fields = ['background_image_url', 'whatsapp_image_url', 'reminder_image_url'];
    foreach ($image_fields as $field) {
        if (!empty($event[$field]) && file_exists($event[$field])) {
            $image_data = base64_encode(file_get_contents($event[$field]));
            $backup_data['images'][$field] = [
                'filename' => basename($event[$field]),
                'path' => $event[$field],
                'data' => $image_data
            ];
        }
    }
    
    $safe_filename = createSafeFilename($event['event_name'], $event_id);
    $filename = $safe_filename . '.json';
    $filepath = $backup_dir . '/' . $filename;
    
    if (file_put_contents($filepath, json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save backup file'];
    }
}

function restoreEventFromBackup($filename, $mysqli) {
    global $backup_dir;
    
    $filepath = $backup_dir . '/' . $filename;
    if (!file_exists($filepath)) {
        return ['success' => false, 'message' => 'Backup file not found'];
    }
    
    $backup_data = json_decode(file_get_contents($filepath), true);
    if (!$backup_data) {
        return ['success' => false, 'message' => 'Invalid backup file'];
    }
    
    $mysqli->autocommit(false);
    
    try {
        $event = $backup_data['event'];
        unset($event['id']);
        
        $fields = array_keys($event);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        $sql = "INSERT INTO events (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
        
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        
        $types = str_repeat('s', count($fields));
        $stmt->bind_param($types, ...array_values($event));
        $stmt->execute();
        $new_event_id = $mysqli->insert_id;
        $stmt->close();
        
        if (isset($backup_data['images'])) {
            foreach ($backup_data['images'] as $field => $image_info) {
                $new_path = 'uploads/' . $image_info['filename'];
                file_put_contents($new_path, base64_decode($image_info['data']));
                
                $stmt = $mysqli->prepare("UPDATE events SET $field = ? WHERE id = ?");
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                $stmt->bind_param("si", $new_path, $new_event_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        if (isset($backup_data['guests'])) {
            foreach ($backup_data['guests'] as $guest) {
                unset($guest['id']);
                $guest['event_id'] = $new_event_id;
                
                $fields = array_keys($guest);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO guests (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                
                $types = '';
                foreach ($guest as $value) {
                    $types .= is_int($value) ? 'i' : 's';
                }
                $stmt->bind_param($types, ...array_values($guest));
                $stmt->execute();
                $stmt->close();
            }
        }
        
        if (isset($backup_data['send_results'])) {
            foreach ($backup_data['send_results'] as $result) {
                unset($result['id']);
                $result['event_id'] = $new_event_id;
                
                $fields = array_keys($result);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO send_results (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                
                $types = '';
                foreach ($result as $value) {
                    $types .= is_int($value) ? 'i' : 's';
                }
                $stmt->bind_param($types, ...array_values($result));
                $stmt->execute();
                $stmt->close();
            }
        }

        if (isset($backup_data['reminder_logs'])) {
            foreach ($backup_data['reminder_logs'] as $log) {
                unset($log['id']);
                $log['event_id'] = $new_event_id;
                
                $fields = array_keys($log);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO reminder_logs (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                
                $types = '';
                foreach ($log as $value) {
                    $types .= is_int($value) ? 'i' : 's';
                }
                $stmt->bind_param($types, ...array_values($log));
                $stmt->execute();
                $stmt->close();
            }
        }
        
        $mysqli->commit();
        return ['success' => true, 'event_name' => $event['event_name'], 'event_id' => $new_event_id];
        
    } catch (Exception $e) {
        $mysqli->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    } finally {
        $mysqli->autocommit(true);
    }
}

function handleBackupUpload($file) {
    global $backup_dir;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '_' . basename($file['name']);
    $filepath = $backup_dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save uploaded file'];
    }
}

function getBackupInfo($filepath) {
    if (!file_exists($filepath)) return null;
    $content = file_get_contents($filepath);
    $data = json_decode($content, true);
    return $data['backup_info'] ?? null;
}

function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $unit = 0;
    while ($size >= 1024 && $unit < 3) {
        $size /= 1024;
        $unit++;
    }
    return round($size, 2) . ' ' . $units[$unit];
}

// --- End of Functions Section ---

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_backup'])) {
        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($event_id) {
            $result = createEventBackup($event_id, $mysqli);
            $message = $result['success'] ? $t['backup_created'] : $t['backup_failed'];
            $messageType = $result['success'] ? 'success' : 'error';
            if ($result['success']) {
                $message .= " - " . $result['filename'];
            }
        }
    } elseif (isset($_POST['restore_backup'])) {
        $filename = $_POST['backup_filename'];
        $result = restoreEventFromBackup($filename, $mysqli);
        $message = $result['success'] ? $t['restore_success'] : $t['restore_failed'];
        $messageType = $result['success'] ? 'success' : 'error';
        if ($result['success']) {
            $message .= " - " . $result['event_name'];
        }
    } elseif (isset($_POST['upload_restore']) && isset($_FILES['backup_file'])) {
        $upload_result = handleBackupUpload($_FILES['backup_file']);
        if ($upload_result['success']) {
            $result = restoreEventFromBackup($upload_result['filename'], $mysqli);
            $message = $result['success'] ? $t['restore_success'] : $t['restore_failed'];
            $messageType = $result['success'] ? 'success' : 'error';
            if ($result['success']) {
                $message .= " - " . $result['event_name'];
            }
        } else {
            $message = $upload_result['message'];
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_backup'])) {
        $filename = $_POST['backup_filename'];
        $filepath = $backup_dir . '/' . $filename;
        if (file_exists($filepath) && !is_dir($filepath)) {
            unlink($filepath);
            $message = 'تم حذف النسخة الاحتياطية بنجاح';
            $messageType = 'success';
        } else {
            $message = 'ملف النسخة الاحتياطية غير موجود';
            $messageType = 'error';
        }
    } elseif (isset($_POST['switch_language'])) {
        $new_lang = $_POST['switch_language'];
        if (array_key_exists($new_lang, $texts)) {
            $_SESSION['language'] = $new_lang;
            setcookie('language', $new_lang, time() + (86400 * 30), "/"); // 30 days
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Get available events
$events = [];
$result = $mysqli->query("SELECT id, event_name, (SELECT COUNT(*) FROM guests WHERE event_id = events.id) as guests_count FROM events ORDER BY created_at DESC");
if ($result) {
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

// Get backup files
$backups = [];
if (is_dir($backup_dir)) {
    $files = glob($backup_dir . '/*.json');
    foreach ($files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'size' => filesize($file),
            'created' => filemtime($file),
            'info' => getBackupInfo($file)
        ];
    }
    usort($backups, function($a, $b) {
        return $b['created'] - $a['created'];
    });
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['event_backup'] ?> - WOSUOL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: <?= $lang === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" ?>; 
            background: #f8f8f8; 
            padding: 20px; 
            color: #2d4a22;
        }
        .container { 
            max-width: 1200px; 
            margin: 20px auto; 
            background: white;
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 30px; 
        }
        .wosuol-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #2d4a22;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .wosuol-icon {
            width: 35px;
            height: 35px;
            background: rgba(45, 74, 34, 0.9);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        .wosuol-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d4a22;
        }
        .page-header, .card { 
            padding: 20px 25px;
            border-radius: 20px;
            font-weight: 600;
            color: #2d4a22;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            margin-bottom: 30px;
        }
        .page-header::before, .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.1), transparent);
            transition: left 0.6s ease;
        }
        .page-header:hover::before, .card:hover::before {
            left: 100%;
        }
        .page-header:hover, .card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 8px 25px rgba(45, 74, 34, 0.2);
            border-color: rgba(45, 74, 34, 0.5);
            color: #1a2f15;
            background: rgba(255, 255, 255, 0.95);
        }
        .page-header {
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header-buttons { 
            display: flex; 
            gap: 12px; 
            align-items: center; 
        }
        .btn { 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            font-weight: 600;
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.1), transparent);
            transition: left 0.6s ease;
        }
        .btn:hover::before {
            left: 100%;
        }
        .btn:hover { 
            transform: translateY(-2px) scale(1.02); 
            box-shadow: 0 6px 20px rgba(45, 74, 34, 0.2);
            border-color: rgba(45, 74, 34, 0.5);
            color: #1a2f15;
            background: rgba(255, 255, 255, 0.95);
        }
        .btn-primary { 
            background: linear-gradient(135deg, rgba(45, 74, 34, 0.9), rgba(26, 47, 21, 0.9));
            color: white; 
        }
        .btn-success { 
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9)); 
            color: white; 
        }
        .btn-warning { 
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(217, 119, 6, 0.9)); 
            color: white; 
        }
        .btn-danger { 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9)); 
            color: white; 
        }
        .btn-secondary { 
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.9), rgba(75, 85, 99, 0.9)); 
            color: white; 
        }
        .btn-sm { 
            padding: 6px 12px; 
            font-size: 0.875rem; 
        }
        .upload-zone {
            border: 2px dashed rgba(45, 74, 34, 0.3);
            border-radius: 25px;
            padding: 40px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .upload-zone::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.05), transparent);
            transition: left 0.4s ease;
        }
        .upload-zone:hover::before {
            left: 100%;
        }
        .upload-zone:hover {
            border-color: rgba(45, 74, 34, 0.6);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 74, 34, 0.1);
        }
        input, select {
            transition: all 0.3s ease; 
            border: 2px solid rgba(45, 74, 34, 0.3);
            border-radius: 15px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            font-weight: 600;
        }
        input:focus, select:focus { 
            border-color: rgba(45, 74, 34, 0.6);
            box-shadow: 0 0 0 3px rgba(45, 74, 34, 0.1); 
            background: rgba(255, 255, 255, 0.95);
            outline: none;
        }
        .backup-item {
            border: 2px solid rgba(45, 74, 34, 0.2);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .backup-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.03), transparent);
            transition: left 0.3s ease;
        }
        .backup-item:hover::before {
            left: 100%;
        }
        .backup-item:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(45, 74, 34, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            border: 2px solid rgba(45, 74, 34, 0.2);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            border-color: rgba(45, 74, 34, 0.4);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2d4a22;
        }
        code {
            font-family: 'Courier New', monospace;
            background: rgba(45, 74, 34, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            color: #2d4a22;
        }
        .cron-command {
            background: rgba(45, 74, 34, 0.1);
            padding: 15px;
            border-radius: 15px;
            border: 2px solid rgba(45, 74, 34, 0.2);
            font-family: 'Courier New', monospace;
            word-break: break-all;
            color: #2d4a22;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .cron-command:hover {
            background: rgba(45, 74, 34, 0.15);
            border-color: rgba(45, 74, 34, 0.4);
        }
        .log-container {
            background: #1a2f15;
            color: #e5e7eb;
            padding: 20px;
            border-radius: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            border: 2px solid rgba(45, 74, 34, 0.3);
        }
        @media (max-width: 768px) {
            .page-header { 
                flex-direction: column; 
                gap: 15px; 
                text-align: center;
                border-radius: 30px;
                padding: 15px 20px;
            }
            .card { 
                border-radius: 30px;
                padding: 15px 20px;
            }
            .btn { 
                border-radius: 20px;
                padding: 10px 15px;
            }
            .upload-zone { 
                border-radius: 20px;
                padding: 30px 20px;
            }
            .backup-item { 
                border-radius: 15px;
                padding: 15px;
            }
            .cron-command { 
                border-radius: 10px; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wosuol-logo">
            <div class="wosuol-icon">
                <i class="fas fa-archive"></i>
            </div>
            <div class="wosuol-text">وصول</div>
        </div>

        <div class="page-header">
            <h1 class="text-3xl font-bold"><?= $t['event_backup'] ?></h1>
            <div class="header-buttons">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>" 
                            class="btn">
                        <i class="fas fa-language"></i>
                        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                    </button>
                </form>
                <a href="events.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <?= $t['back_to_events'] ?>
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="card mb-6">
                <div class="p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['backup_event'] ?></h2>
            <?php if (empty($events)): ?>
                <p class="text-gray-500"><?= $t['no_events'] ?></p>
            <?php else: ?>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?= $t['select_event'] ?>
                        </label>
                        <select name="event_id" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- <?= $t['select_event'] ?> --</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>">
                                    <?= htmlspecialchars($event['event_name']) ?> (<?= $event['guests_count'] ?> <?= $t['guests_count'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="create_backup" class="btn btn-primary">
                        📦 <?= $t['create_backup'] ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['upload_backup'] ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="upload-zone">
                    <div class="text-6xl mb-4">📁</div>
                    <p class="text-lg mb-4"><?= $t['select_file'] ?></p>
                    <input type="file" name="backup_file" accept=".json" required class="mb-4">
                    <div>
                        <button type="submit" name="upload_restore" class="btn btn-success">
                            ⬆️ <?= $t['upload_restore'] ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['backup_list'] ?></h2>
            <?php if (empty($backups)): ?>
                <p class="text-gray-500"><?= $t['no_backups'] ?></p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($backups as $backup): ?>
                        <div class="backup-item">
                            <div class="flex justify-between items-start flex-wrap gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg mb-2">📦 <?= htmlspecialchars($backup['filename']) ?></h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                        <div>
                                            <strong><?= $t['created_at'] ?>:</strong><br>
                                            <?= date('Y-m-d H:i:s', $backup['created']) ?>
                                        </div>
                                        <div>
                                            <strong><?= $t['file_size'] ?>:</strong><br>
                                            <?= formatFileSize($backup['size']) ?>
                                        </div>
                                        <?php if ($backup['info'] && isset($backup['info']['original_event_id'])): ?>
                                            <div>
                                                <strong><?= $t['original_event'] ?>:</strong><br>
                                                ID: <?= htmlspecialchars($backup['info']['original_event_id']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2"><?= $t['includes_data'] ?></p>
                                </div>
                                <div class="flex gap-2 <?= $lang === 'ar' ? 'mr-auto md:mr-4' : 'ml-auto md:ml-4' ?> mt-2 md:mt-0">
                                    <a href="<?= htmlspecialchars($backup_dir . '/' . $backup['filename']) ?>" download class="btn btn-secondary btn-sm">
                                        ⬇️ <?= $t['download'] ?>
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_restore'] ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="backup_filename" value="<?= htmlspecialchars($backup['filename']) ?>">
                                        <button type="submit" name="restore_backup" class="btn btn-success btn-sm">
                                            🔄 <?= $t['restore'] ?>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_delete'] ?>')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="backup_filename" value="<?= htmlspecialchars($backup['filename']) ?>">
                                        <button type="submit" name="delete_backup" class="btn btn-danger btn-sm">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>