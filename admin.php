<?php
/**
 * admin.php - إدارة الأحداث والإعدادات
 * @package Wosuol
 * @author Wosuol.com
 * @copyright 2025 Wosuol.com - جميع الحقوق محفوظة
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_config.php';

// --- Language System ---
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
if (isset($_POST['switch_language'])) {
    $lang = $_POST['switch_language'] === 'en' ? 'en' : 'ar';
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    // Redirect to avoid re-posting
    $redirect_url = $_SERVER['REQUEST_URI'];
    header("Location: $redirect_url");
    exit;
}

// Language texts
$texts = [
    'ar' => [
        'administration' => 'الإدارة',
        'logout' => 'تسجيل الخروج',
        'event_settings' => 'إعدادات الحفل',
        'user_management' => 'إدارة المستخدمين',
        'event_details' => 'تفاصيل الحفل',
        'event_name' => 'اسم الحفل (عربي)',
        'event_name_en' => 'اسم الحفل (إنجليزي)',
        'event_slug' => 'رابط الحفل المخصص (Slug)',
        'google_maps_link' => 'رابط خرائط جوجل',
        'event_date_time' => 'تاريخ ووقت الحفل',
        'venue_ar' => 'مكان الحفل (عربي)',
        'venue_en' => 'مكان الحفل (إنجليزي)',
        'event_description_ar' => 'وصف الحفل (عربي)',
        'event_description_en' => 'وصف الحفل (إنجليزي)',
        'image_settings' => 'إعدادات الصور',
        'display_image' => 'صورة العرض',
        'whatsapp_image' => 'صورة الواتساب',
        'current_display_image' => 'الصورة الحالية للعرض',
        'current_whatsapp_image' => 'الصورة الحالية للواتساب',
        'upload_display_image' => 'رفع صورة العرض',
        'upload_whatsapp_image' => 'رفع صورة الواتساب',
        'remove_current_image' => 'حذف الصورة الحالية',
        'image_preview' => 'معاينة الصورة',
        'cancel_selection' => 'إلغاء الاختيار',
        'qr_settings' => 'إعدادات QR',
        'webhook_settings' => 'إعدادات Webhook',
        'registration_settings' => 'إعدادات صفحة التسجيل',
        'registration_show_phone' => 'إظهار حقل الهاتف',
        'registration_require_phone' => 'جعل الهاتف مطلوباً',
        'registration_show_guest_count' => 'إظهار عدد الضيوف',
        'registration_show_countdown' => 'إظهار العد التنازلي',
        'registration_show_location' => 'إظهار معلومات الموقع',
        'registration_mode' => 'نمط التسجيل',
        'registration_mode_simple' => 'مبسط',
        'registration_mode_full' => 'كامل',
        'rsvp_settings' => 'إعدادات صفحة تأكيد الحضور (RSVP)',
        'rsvp_show_guest_count' => 'إظهار حقل عدد الضيوف',
        'rsvp_show_qr_code' => 'إظهار حقل رمز QR',
        'save_all_settings' => 'حفظ جميع الإعدادات',
        'image_saved_success' => 'تم حفظ الصورة بنجاح',
        'image_removed_success' => 'تم حذف الصورة بنجاح',
        'users' => 'المستخدمين',
        'add_user' => 'إضافة مستخدم جديد',
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور',
        'role' => 'الدور',
        'custom_event' => 'الحفل المخصص',
        'add' => 'إضافة',
        'current_users' => 'المستخدمون الحاليون',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm_delete_user' => 'هل أنت متأكد من حذف هذا المستخدم؟',
        'user_added_success' => 'تم إضافة المستخدم بنجاح.',
        'user_add_error' => 'حدث خطأ في إضافة المستخدم.',
        'user_updated_success' => 'تم تحديث المستخدم بنجاح.',
        'user_update_error' => 'حدث خطأ في التحديث.',
        'user_deleted_success' => 'تم حذف المستخدم بنجاح.',
        'user_delete_error' => 'حدث خطأ في الحذف.',
        'user_exists' => 'اسم المستخدم موجود بالفعل.',
        'all_fields_required' => 'الرجاء إدخال كل الحقول.',
        'leave_empty' => 'اتركه فارغاً لعدم التغيير',
        'save_changes' => 'حفظ التعديلات',
        'cancel' => 'إلغاء'
    ],
    'en' => [
        'administration' => 'Administration',
        'logout' => 'Logout',
        'event_settings' => 'Event Settings',
        'user_management' => 'User Management',
        'event_details' => 'Event Details',
        'event_name' => 'Event Name (Arabic)',
        'event_name_en' => 'Event Name (English)',
        'event_slug' => 'Custom Event Link (Slug)',
        'google_maps_link' => 'Google Maps Link',
        'event_date_time' => 'Event Date & Time',
        'venue_ar' => 'Venue (Arabic)',
        'venue_en' => 'Venue (English)',
        'event_description_ar' => 'Event Description (Arabic)',
        'event_description_en' => 'Event Description (English)',
        'image_settings' => 'Image Settings',
        'display_image' => 'Display Image',
        'whatsapp_image' => 'WhatsApp Image',
        'current_display_image' => 'Current Display Image',
        'current_whatsapp_image' => 'Current WhatsApp Image',
        'upload_display_image' => 'Upload Display Image',
        'upload_whatsapp_image' => 'Upload WhatsApp Image',
        'remove_current_image' => 'Remove Current Image',
        'image_preview' => 'Image Preview',
        'cancel_selection' => 'Cancel Selection',
        'qr_settings' => 'QR Settings',
        'webhook_settings' => 'Webhook Settings',
        'registration_settings' => 'Registration Page Settings',
        'registration_show_phone' => 'Show Phone Field',
        'registration_require_phone' => 'Make Phone Required',
        'registration_show_guest_count' => 'Show Guest Count Field',
        'registration_show_countdown' => 'Show Countdown Timer',
        'registration_show_location' => 'Show Location Info',
        'registration_mode' => 'Registration Mode',
        'registration_mode_simple' => 'Simple',
        'registration_mode_full' => 'Full',
        'rsvp_settings' => 'RSVP Page Settings',
        'rsvp_show_guest_count' => 'Display Guest Count',
        'rsvp_show_qr_code' => 'Display QR Code Field',
        'save_all_settings' => 'Save All Settings',
        'image_saved_success' => 'Image saved successfully',
        'image_removed_success' => 'Image removed successfully',
        'users' => 'Users',
        'add_user' => 'Add New User',
        'username' => 'Username',
        'password' => 'Password',
        'role' => 'Role',
        'custom_event' => 'Custom Event',
        'add' => 'Add',
        'current_users' => 'Current Users',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete_user' => 'Are you sure you want to delete this user?',
        'user_added_success' => 'User added successfully.',
        'user_add_error' => 'Error occurred while adding user.',
        'user_updated_success' => 'User updated successfully.',
        'user_update_error' => 'Error occurred while updating.',
        'user_deleted_success' => 'User deleted successfully.',
        'user_delete_error' => 'Error occurred while deleting.',
        'user_exists' => 'Username already exists.',
        'all_fields_required' => 'Please enter all fields.',
        'leave_empty' => 'Leave empty to not change',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel'
    ]
];

$t = $texts[$lang];

// --- Security Check & Get Event ID ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// --- CSRF Protection ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
        
        // Handle date and time
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
        $current_display_image = $_POST['current_display_image'] ?? '';
        $current_whatsapp_image = $_POST['current_whatsapp_image'] ?? '';

        if (isset($_FILES['display_image_upload']) && $_FILES['display_image_upload']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['display_image_upload']['type'], $allowed_types) && $_FILES['display_image_upload']['size'] <= 5000000) {
                $upload_dir = './uploads/';
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
                $fileExtension = strtolower(pathinfo($_FILES['display_image_upload']['name'], PATHINFO_EXTENSION));
                $newFileName = 'display_event_' . $event_id . '_' . time() . '.' . $fileExtension;
                $destPath = $upload_dir . $newFileName;
                if(move_uploaded_file($_FILES['display_image_upload']['tmp_name'], $destPath)) {
                    if (!empty($current_display_image) && file_exists($current_display_image)) { unlink($current_display_image); }
                    $current_display_image = $destPath;
                    $message = $t['image_saved_success']; $messageType = 'success';
                }
            }
        } elseif (isset($_POST['remove_display_image']) && $_POST['remove_display_image'] === '1') {
            if (!empty($current_display_image) && file_exists($current_display_image)) { unlink($current_display_image); }
            $current_display_image = '';
            $message = $t['image_removed_success']; $messageType = 'success';
        }

        if (isset($_FILES['whatsapp_image_upload']) && $_FILES['whatsapp_image_upload']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['whatsapp_image_upload']['type'], $allowed_types) && $_FILES['whatsapp_image_upload']['size'] <= 5000000) {
                $upload_dir = './uploads/';
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
                $fileExtension = strtolower(pathinfo($_FILES['whatsapp_image_upload']['name'], PATHINFO_EXTENSION));
                $newFileName = 'whatsapp_event_' . $event_id . '_' . time() . '.' . $fileExtension;
                $destPath = $upload_dir . $newFileName;
                if(move_uploaded_file($_FILES['whatsapp_image_upload']['tmp_name'], $destPath)) {
                    if (!empty($current_whatsapp_image) && file_exists($current_whatsapp_image)) { unlink($current_whatsapp_image); }
                    $current_whatsapp_image = $destPath;
                    if(empty($message)) { $message = $t['image_saved_success']; $messageType = 'success'; }
                }
            }
        } elseif (isset($_POST['remove_whatsapp_image']) && $_POST['remove_whatsapp_image'] === '1') {
            if (!empty($current_whatsapp_image) && file_exists($current_whatsapp_image)) { unlink($current_whatsapp_image); }
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

        // --- Stage 2: Build Query and Types Dynamically ---
        $sql_parts = [];
        $types = '';
        $values = [];

        foreach ($update_data as $key => $value) {
            $sql_parts[] = "`{$key}` = ?";
            $values[] = $value;
            // Determine type
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_double($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        
        $sql = "UPDATE `events` SET " . implode(', ', $sql_parts) . " WHERE `id` = ?";
        $values[] = $event_id; // Add event_id for the WHERE clause
        $types .= 'i';

        // --- Stage 3: Prepare and Execute ---
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values); // Use the splat operator

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
    
    // Apply defaults for QR and Webhook settings if empty
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

function safe_html($value, $default = '') {
    return htmlspecialchars($value ?? $default, ENT_QUOTES, 'UTF-8');
}

function getPageTitle($title, $lang) {
    $site_name = $lang === 'ar' ? 'وصول' : 'Wosuol';
    return "{$title} - {$site_name}";
}

/**
 * Parses an event date string into a DateTime object, handling various formats.
 * @param string $event_date_text The date string to parse.
 * @return DateTime|false Returns a DateTime object on success, or false on failure.
 */
function parseEventDateTime($event_date_text) {
    // Try to parse standard MySQL format (e.g., '2025-09-01 17:03:00')
    $formats = [
        'Y-m-d H:i:s', 
        'Y-m-d H:i',
    ];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $event_date_text);
        if ($date !== false) {
            return $date;
        }
    }

    // Attempt to parse Arabic format with day name.
    // This part handles the "الجمعة 03.10.2025" format.
    if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $event_date_text, $matches)) {
        $date_string = "{$matches[3]}-{$matches[2]}-{$matches[1]} 18:00:00"; // Assuming 6 PM
        $date = new DateTime($date_string);
        return $date;
    }
    
    return false;
}

// Convert event date from DB to datetime-local format for the input field
$datetime_value = '';
if (!empty($event['event_date_ar'])) {
    $datetime_obj = parseEventDateTime($event['event_date_ar']);
    if ($datetime_obj) {
        $datetime_value = $datetime_obj->format('Y-m-d\TH:i');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getPageTitle($t['administration'] . ': ' . safe_html($event['event_name'] ?? 'حفل'), $lang) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: <?= $lang === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" ?>; 
            background: white; 
            padding: 20px; 
            color: #2d4a22;
        }
        
        .container {
            max-width: 1400px;
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
        
        .page-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 2rem; 
            padding: 20px 25px;
            border-radius: 50px;
            font-weight: 600;
            color: #2d4a22;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.1), transparent);
            transition: left 0.6s ease;
        }
        
        .page-header:hover::before {
            left: 100%;
        }
        
        .page-header:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(45, 74, 34, 0.2);
            border-color: rgba(45, 74, 34, 0.5);
            color: #1a2f15;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .header-buttons { display: flex; gap: 12px; align-items: center; }

        .btn {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            font-weight: 600;
            border-radius: 15px;
            padding: 8px 12px;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            border: none;
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
        
        .btn-small { 
            padding: 6px 10px; 
            font-size: 0.75rem; 
            border-radius: 12px;
        }
        
        .btn-danger { 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.8));
            color: white;
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:hover {
            color: white;
            opacity: 0.9;
        }

        .btn-green {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.8));
            color: white;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .btn-green:hover {
            color: white;
            opacity: 0.9;
        }

        .btn-yellow {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(217, 119, 6, 0.8));
            color: white;
            border-color: rgba(245, 158, 11, 0.3);
        }
        
        .btn-yellow:hover {
            color: white;
            opacity: 0.9;
        }

        .btn-blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.8));
            color: white;
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .btn-blue:hover {
            color: white;
            opacity: 0.9;
        }

        .main-nav { 
            background: none; 
            padding: 0; 
            border-radius: 0; 
            margin-bottom: 2rem; 
            border: none;
            justify-content: center;
        }
        
        .main-nav a {
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 12px;
            color: #2d4a22;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .main-nav a:hover {
            background-color: rgba(45, 74, 34, 0.1);
            border-color: rgba(45, 74, 34, 0.3);
            color: #1a2f15;
        }
        
        .tabs { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px;
            border-bottom: none;
            margin-bottom: 20px;
        }
        
        .tab-button { 
            padding: 12px 25px; 
            cursor: pointer; 
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50px;
            font-size: 1rem; 
            font-weight: 600; 
            color: #2d4a22;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .tab-button.active { 
            background: linear-gradient(135deg, rgba(45, 74, 34, 0.9), rgba(26, 47, 21, 0.8));
            color: white;
            border-color: rgba(45, 74, 34, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(45, 74, 34, 0.3);
        }
        .tab-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(45, 74, 34, 0.2);
        }
        
        .tab-content { 
            display: none; 
            padding-top: 20px; 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(45, 74, 34, 0.3);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            padding: 30px;
        }
        
        .tab-content.active { 
            display: block; 
        }
        
        .accordion-header { 
            cursor: pointer; 
            padding: 15px; 
            background: rgba(45, 74, 34, 0.1); 
            border-radius: 15px; 
            font-weight: bold; 
            margin-top: 15px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: #1a2f15;
            transition: background 0.3s ease;
        }
        .accordion-header:hover {
            background: rgba(45, 74, 34, 0.15);
        }
        .accordion-content { 
            display: none; 
            padding: 20px; 
            border: 2px solid rgba(45, 74, 34, 0.2); 
            border-top: none; 
            border-radius: 0 0 15px 15px; 
            background: rgba(255, 255, 255, 0.8);
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 600; 
            color: #2d4a22;
        }
        input[type="text"], input[type="url"], input[type="password"], input[type="datetime-local"], textarea, select {
            border: 2px solid rgba(45, 74, 34, 0.3);
            border-radius: 15px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        input[type="text"]:focus, input[type="url"]:focus, input[type="password"]:focus, input[type="datetime-local"]:focus, textarea:focus, select:focus {
            border-color: rgba(45, 74, 34, 0.6);
            box-shadow: 0 0 0 3px rgba(45, 74, 34, 0.1);
            background: rgba(255, 255, 255, 0.95);
            outline: none;
        }
        .user-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px;
            margin-top: 20px; 
        }
        .user-table th, .user-table td { 
            padding: 15px; 
            text-align: <?= $lang === 'ar' ? 'right' : 'left' ?>;
            background: rgba(45, 74, 34, 0.05);
            border: 1px solid rgba(45, 74, 34, 0.1);
        }
        .user-table th {
            background: rgba(45, 74, 34, 0.1);
            font-weight: bold;
            color: #1a2f15;
        }
        .user-table tr:first-child th:first-child { border-top-left-radius: 15px; }
        .user-table tr:first-child th:last-child { border-top-right-radius: 15px; }
        .user-table tr:last-child td:first-child { border-bottom-left-radius: 15px; }
        .user-table tr:last-child td:last-child { border-bottom-right-radius: 15px; }
        
        .user-table .actions-cell { display: flex; gap: 8px; }
        .image-preview { max-width: 200px; max-height: 200px; border-radius: 12px; border: 2px solid #ddd; margin-top: 10px; }
        .image-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 15px; 
            margin: 15px 0; 
            border: 2px dashed #e9ecef;
        }
        .image-section.has-image {
            border-style: solid;
            border-color: #28a745;
            background: #f8fff9;
        }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal.active { display: flex; justify-content: center; align-items: center; }
        .modal-content { 
            background-color: white; 
            padding: 30px; 
            border-radius: 20px; 
            width: 90%; 
            max-width: 500px;
            color: #2d4a22;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .message-box {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .message-box.success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .message-box.error {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        @media (max-width: 768px) {
            .page-header { 
                flex-direction: column; 
                gap: 15px; 
                text-align: center;
                border-radius: 30px;
                padding: 15px 20px;
            }
            .header-buttons {
                flex-direction: column;
                width: 100%;
            }
            .header-buttons .btn, .main-nav a {
                width: 100%;
            }
            .tabs {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="p-5">
    <div class="container">
        <div class="wosuol-logo">
            <div class="wosuol-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="wosuol-text">وصول</div>
        </div>

        <div class="page-header">
             <h1 class="text-3xl font-bold"><?= $t['administration'] ?>: "<?= safe_html($event['event_name']) ?>"</h1>
             <div class="header-buttons">
                 <form method="POST" style="display: inline;">
                     <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>" 
                             class="btn">
                         <i class="fas fa-language"></i>
                         <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                     </button>
                 </form>
                 <a href="events.php" class="btn">
                    <i class="fas fa-arrow-right"></i>
                     <?= $lang === 'ar' ? 'العودة' : 'Back' ?>
                 </a>
                 <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                     <?= $t['logout'] ?>
                 </a>
             </div>
        </div>
        
        <nav class="main-nav">
            <a href="events.php" class="text-blue-600"><i class="fas fa-home"></i> <?= $lang === 'ar' ? 'كل الحفلات' : 'All Events' ?></a>
            <a href="dashboard.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-chart-bar"></i> <?= $lang === 'ar' ? 'متابعة' : 'Dashboard' ?></a>
            <a href="guests.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-users"></i> <?= $lang === 'ar' ? 'إدارة الضيوف' : 'Manage Guests' ?></a>
            <a href="send_invitations.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-paper-plane"></i> <?= $lang === 'ar' ? 'إرسال الدعوات' : 'Send Invitations' ?></a>
            <a href="checkin.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-check-circle"></i> <?= $lang === 'ar' ? 'تسجيل الدخول' : 'Check-in' ?></a>
            <a href="register.php?event_id=<?= $event_id ?>" target="_blank" class="text-green-600 font-bold"><i class="fas fa-link"></i> <?= $lang === 'ar' ? 'عرض صفحة التسجيل' : 'View Registration Page' ?></a>
            <a href="rsvp.php?id=GUEST_ID_HERE" target="_blank" class="text-purple-600 font-bold"><i class="fas fa-qrcode"></i> <?= $lang === 'ar' ? 'عرض صفحة تأكيد الحضور' : 'View RSVP Page' ?></a>
        </nav>
        
        <?php if ($message): ?>
            <div class="message-box <?= $messageType === 'success' ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-button active" data-tab="general-settings"><i class="fas fa-cog"></i> <?= $t['event_settings'] ?></button>
            <button class="tab-button" data-tab="user-management"><i class="fas fa-user-shield"></i> <?= $t['user_management'] ?></button>
        </div>
        
        <div id="general-settings" class="tab-content active">
            <form id="event-settings-form" method="POST" action="admin.php?event_id=<?= $event_id ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="update_event_settings" value="1">
                
                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['event_details'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content" style="display: block;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label><?= $t['event_name'] ?>:</label>
                            <input type="text" name="event_name" value="<?= safe_html($event['event_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['event_name_en'] ?>:</label>
                            <input type="text" name="event_name_en" value="<?= safe_html($event['event_name_en']) ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $t['event_slug'] ?>:</label>
                            <input type="text" name="event_slug" value="<?= safe_html($event['event_slug']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['google_maps_link'] ?>:</label>
                            <input type="url" name="maps_link" value="<?= safe_html($event['Maps_link']) ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $t['event_date_time'] ?>:</label>
                            <?php
                            $datetime_value = '';
                            if (!empty($event['event_date_ar'])) {
                                $datetime_obj = parseEventDateTime($event['event_date_ar']);
                                if ($datetime_obj) {
                                    $datetime_value = $datetime_obj->format('Y-m-d\TH:i');
                                }
                            }
                            ?>
                            <input type="datetime-local" name="event_datetime" value="<?= htmlspecialchars($datetime_value) ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?= $t['venue_ar'] ?>:</label>
                            <input type="text" name="venue_ar" value="<?= safe_html($event['venue_ar']) ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $t['venue_en'] ?>:</label>
                            <input type="text" name="venue_en" value="<?= safe_html($event['venue_en']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?= $t['event_description_ar'] ?>:</label>
                        <textarea name="event_paragraph_ar" rows="4"><?= safe_html($event['event_paragraph_ar']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label><?= $t['event_description_en'] ?>:</label>
                        <textarea name="event_paragraph_en" rows="4"><?= safe_html($event['event_paragraph_en']) ?></textarea>
                    </div>
                </div>

                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['image_settings'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content">
                    <div class="image-section <?= !empty($event['background_image_url']) ? 'has-image' : '' ?>">
                        <h4 class="font-bold text-lg mb-4 text-blue-800">
                            <i class="fas fa-desktop"></i>
                            <?= $t['display_image'] ?>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">هذه الصورة ستظهر في صفحة RSVP وصفحة التسجيل على الموقع</p>
                        
                        <?php if(!empty($event['background_image_url'])): ?>
                            <div class="my-4 p-4 border rounded-lg bg-gray-50">
                                <p class="font-semibold mb-2"><?= $t['current_display_image'] ?>:</p>
                                <img src="<?= safe_html($event['background_image_url']) ?>" alt="<?= $t['current_display_image'] ?>" class="image-preview">
                                <div class="mt-3">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="remove_display_image" value="1" class="mx-2" onchange="toggleImageUpload(this, 'display')">
                                        <?= $t['remove_current_image'] ?>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div id="display-image-upload-section" class="mt-2">
                             <label class="block font-medium"><?= $t['upload_display_image'] ?>:</label>
                             <input type="file" id="display_image_upload" name="display_image_upload" accept="image/*" class="mt-1" onchange="previewNewImage(this, 'display')">
                             <p class="text-sm text-gray-600 mt-1">حد أقصى: 5MB، الأنواع المدعومة: JPG, PNG, GIF, WebP</p>
                        </div>

                        <div id="display-image-preview-container" class="my-2" style="display: none;">
                             <p class="font-semibold"><?= $t['image_preview'] ?>:</p>
                             <img id="display-image-preview" src="#" alt="<?= $t['image_preview'] ?>" class="image-preview">
                             <button type="button" class="mt-2 text-sm text-red-600 hover:underline" onclick="cancelImageSelection('display')"><?= $t['cancel_selection'] ?></button>
                        </div>
                        <input type="hidden" name="current_display_image" value="<?= safe_html($event['background_image_url']) ?>">
                    </div>

                    <div class="image-section <?= !empty($event['whatsapp_image_url']) ? 'has-image' : '' ?>">
                        <h4 class="font-bold text-lg mb-4 text-green-800">
                            <i class="fab fa-whatsapp"></i>
                            <?= $t['whatsapp_image'] ?>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">هذه الصورة ستُرسل مع رسائل الدعوة عبر الواتساب</p>
                        
                        <?php if(!empty($event['whatsapp_image_url'])): ?>
                            <div class="my-4 p-4 border rounded-lg bg-gray-50">
                                <p class="font-semibold mb-2"><?= $t['current_whatsapp_image'] ?>:</p>
                                <img src="<?= safe_html($event['whatsapp_image_url']) ?>" alt="<?= $t['current_whatsapp_image'] ?>" class="image-preview">
                                <div class="mt-3">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="remove_whatsapp_image" value="1" class="mx-2" onchange="toggleImageUpload(this, 'whatsapp')">
                                        <?= $t['remove_current_image'] ?>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div id="whatsapp-image-upload-section" class="mt-2">
                             <label class="block font-medium"><?= $t['upload_whatsapp_image'] ?>:</label>
                             <input type="file" id="whatsapp_image_upload" name="whatsapp_image_upload" accept="image/*" class="mt-1" onchange="previewNewImage(this, 'whatsapp')">
                             <p class="text-sm text-gray-600 mt-1">حد أقصى: 5MB، الأنواع المدعومة: JPG, PNG, GIF, WebP</p>
                        </div>

                        <div id="whatsapp-image-preview-container" class="my-2" style="display: none;">
                             <p class="font-semibold"><?= $t['image_preview'] ?>:</p>
                             <img id="whatsapp-image-preview" src="#" alt="<?= $t['image_preview'] ?>" class="image-preview">
                             <button type="button" class="mt-2 text-sm text-red-600 hover:underline" onclick="cancelImageSelection('whatsapp')"><?= $t['cancel_selection'] ?></button>
                        </div>
                        <input type="hidden" name="current_whatsapp_image" value="<?= safe_html($event['whatsapp_image_url']) ?>">
                    </div>
                </div>

                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['qr_settings'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label>عنوان بطاقة QR (عربي):</label>
                            <input type="text" name="qr_card_title_ar" value="<?= safe_html($event['qr_card_title_ar']) ?>">
                        </div>
                        <div class="form-group">
                            <label>عنوان بطاقة QR (إنجليزي):</label>
                            <input type="text" name="qr_card_title_en" value="<?= safe_html($event['qr_card_title_en']) ?>">
                        </div>
                        <div class="form-group">
                            <label>تعليمات إظهار الكود (عربي):</label>
                            <input type="text" name="qr_show_code_instruction_ar" value="<?= safe_html($event['qr_show_code_instruction_ar']) ?>">
                        </div>
                        <div class="form-group">
                            <label>تعليمات إظهار الكود (إنجليزي):</label>
                            <input type="text" name="qr_show_code_instruction_en" value="<?= safe_html($event['qr_show_code_instruction_en']) ?>">
                        </div>
                        <div class="form-group">
                            <label>نص العلامة التجارية (عربي):</label>
                            <input type="text" name="qr_brand_text_ar" value="<?= safe_html($event['qr_brand_text_ar']) ?>">
                        </div>
                        <div class="form-group">
                            <label>نص العلامة التجارية (إنجليزي):</label>
                            <input type="text" name="qr_brand_text_en" value="<?= safe_html($event['qr_brand_text_en']) ?>">
                        </div>
                        <div class="form-group">
                            <label>موقع الويب على البطاقة:</label>
                            <input type="text" name="qr_website" value="<?= safe_html($event['qr_website']) ?>">
                        </div>
                    </div>
                </div>

                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['webhook_settings'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label>Webhook لتأكيد الحضور:</label>
                            <input type="url" name="n8n_confirm_webhook" value="<?= safe_html($event['n8n_confirm_webhook']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Webhook للدعوات الأولية:</label>
                            <input type="url" name="n8n_initial_invite_webhook" value="<?= safe_html($event['n8n_initial_invite_webhook']) ?>">
                        </div>
                    </div>
                </div>

                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['registration_settings'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="registration_show_phone" value="1" 
                                       <?= ($event['registration_show_phone'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['registration_show_phone'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="registration_require_phone" value="1" 
                                       <?= ($event['registration_require_phone'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['registration_require_phone'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="registration_show_guest_count" value="1" 
                                       <?= ($event['registration_show_guest_count'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['registration_show_guest_count'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="registration_show_countdown" value="1" 
                                       <?= ($event['registration_show_countdown'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['registration_show_countdown'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="registration_show_location" value="1" 
                                       <?= ($event['registration_show_location'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['registration_show_location'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label><?= $t['registration_mode'] ?>:</label>
                            <select name="registration_mode">
                                <option value="full" <?= ($event['registration_mode'] ?? 'full') === 'full' ? 'selected' : '' ?>><?= $t['registration_mode_full'] ?></option>
                                <option value="simple" <?= ($event['registration_mode'] ?? 'full') === 'simple' ? 'selected' : '' ?>><?= $t['registration_mode_simple'] ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['rsvp_settings'] ?> <span class="toggle-icon">▼</span></div>
                <div class="accordion-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="rsvp_show_guest_count" value="1" 
                                       <?= ($event['rsvp_show_guest_count'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['rsvp_show_guest_count'] ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="rsvp_show_qr_code" value="1" 
                                       <?= ($event['rsvp_show_qr_code'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                                <?= $t['rsvp_show_qr_code'] ?>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-blue mt-6">
                    <i class="fas fa-save"></i>
                    <?= $t['save_all_settings'] ?>
                </button>
            </form>
        </div>

        <div id="user-management" class="tab-content">
            <h3 class="text-xl font-bold mb-4"><?= $t['add_user'] ?></h3>
            <form method="POST" action="admin.php?event_id=<?= $event_id ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="user_action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="form-group">
                        <label><?= $t['username'] ?>:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label><?= $t['password'] ?>:</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label><?= $t['role'] ?>:</label>
                        <select name="role" required onchange="toggleEventSelect(this.value, 'add_event_select_container')">
                            <option value="admin">مدير</option>
                            <option value="viewer">مشاهد</option>
                            <option value="checkin_user">مسجل دخول</option>
                        </select>
                    </div>
                    <div class="form-group" id="add_event_select_container" style="display:none;">
                        <label><?= $t['custom_event'] ?>:</label>
                        <select name="user_event_id">
                            <option value="">-- اختر الحفل --</option>
                            <?php foreach($all_events as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['event_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-green mt-4">
                    <i class="fas fa-user-plus"></i>
                    <?= $t['add'] ?>
                </button>
            </form>
            
            <hr class="my-8 border-t border-gray-300">
            
            <h3 class="text-xl font-bold mt-8 mb-4"><?= $t['current_users'] ?></h3>
            <div class="overflow-x-auto">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th><?= $t['username'] ?></th>
                            <th><?= $t['role'] ?></th>
                            <th><?= $t['custom_event'] ?></th>
                            <th><?= $t['actions'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <em>(كل الحفلات)</em>
                                <?php else: ?>
                                    <?php 
                                    $event_name = 'غير محدد';
                                    foreach ($all_events as $e) {
                                        if ($e['id'] == $user['event_id']) {
                                            $event_name = $e['event_name'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars($event_name);
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td class="actions-cell">
                                <button type="button" class="btn btn-yellow btn-small" onclick='openEditModal(<?= json_encode($user) ?>)'>
                                    <i class="fas fa-edit"></i> <?= $t['edit'] ?>
                                </button>
                                <?php if ($_SESSION['username'] !== $user['username']): ?>
                                <form method="POST" action="admin.php?event_id=<?= $event_id ?>" onsubmit="return confirm('<?= $t['confirm_delete_user'] ?>');" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="user_action" value="delete">
                                    <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                                    <button type="submit" class="btn btn-danger btn-small">
                                        <i class="fas fa-trash"></i> <?= $t['delete'] ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4"><?= $t['edit'] ?> <?= $t['username'] ?></h2>
            <form method="POST" action="admin.php?event_id=<?= $event_id ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="user_action" value="edit">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="form-group">
                    <label><?= $t['username'] ?>:</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>
                <div class="form-group">
                    <label><?= $t['password'] ?> (<?= $t['leave_empty'] ?>):</label>
                    <input type="password" name="password" id="edit_password">
                </div>
                <div class="form-group">
                    <label><?= $t['role'] ?>:</label>
                    <select name="role" id="edit_role" required onchange="toggleEventSelect(this.value, 'edit_event_select_container')">
                        <option value="admin">مدير</option>
                        <option value="viewer">مشاهد</option>
                        <option value="checkin_user">مسجل دخول</option>
                    </select>
                </div>
                <div class="form-group" id="edit_event_select_container" style="display:none;">
                    <label><?= $t['custom_event'] ?>:</label>
                    <select name="user_event_id" id="edit_user_event_id">
                        <option value="">-- اختر الحفل --</option>
                        <?php foreach($all_events as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['event_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeEditModal()" class="btn">
                        <?= $t['cancel'] ?>
                    </button>
                    <button type="submit" class="btn btn-blue">
                        <?= $t['save_changes'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class='text-center text-gray-600 text-sm mt-12 pt-8 border-t border-gray-200'>
        <p><?= $lang === 'ar' ? 'جميع الحقوق محفوظة &copy; 2025 وصول' : 'All Rights Reserved &copy; 2025 Wosuol' ?></p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tab Logic
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'general-settings';

            function switchTab(tabId) {
                contents.forEach(content => content.classList.remove('active'));
                tabs.forEach(tab => tab.classList.remove('active'));
                const contentToShow = document.getElementById(tabId);
                const tabToActivate = document.querySelector(`[data-tab='${tabId}']`);
                if(contentToShow) contentToShow.classList.add('active');
                if(tabToActivate) tabToActivate.classList.add('active');
            }
            
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabId = tab.dataset.tab;
                    switchTab(tabId);
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabId);
                    window.history.pushState({}, '', url);
                });
            });
            switchTab(activeTab);
        });

        // Accordion Logic
        function toggleAccordion(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.toggle-icon');
            const isOpen = content.style.display === 'block';
            
            content.style.display = isOpen ? 'none' : 'block';
            icon.textContent = isOpen ? '▼' : '▲';
        }

        // Image Management Functions
        function previewNewImage(input, type) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(type + '-image-preview').src = e.target.result;
                    document.getElementById(type + '-image-preview-container').style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function cancelImageSelection(type) {
            document.getElementById(type + '_image_upload').value = '';
            document.getElementById(type + '-image-preview-container').style.display = 'none';
        }

        function toggleImageUpload(checkbox, type) {
            const uploadSection = document.getElementById(type + '-image-upload-section');
            if (checkbox.checked) {
                uploadSection.style.display = 'none';
                cancelImageSelection(type);
            } else {
                uploadSection.style.display = 'block';
            }
        }

        // User Management Functions
        function toggleEventSelect(role, containerId) { 
            const container = document.getElementById(containerId);
            if(container) {
                container.style.display = (role === 'viewer' || role === 'checkin_user') ? 'block' : 'none';
            }
        }

        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_user_event_id').value = user.event_id || '';
            
            toggleEventSelect(user.role, 'edit_event_select_container');
            document.getElementById('editUserModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editUserModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('editUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>