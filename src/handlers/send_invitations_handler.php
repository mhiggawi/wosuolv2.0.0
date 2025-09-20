<?php
// src/handlers/send_invitations_handler.php

// This file is included by public/send_invitations.php and assumes bootstrap.php has been included.

// Language texts
$texts = [
    'ar' => [
        'reminder_management' => 'إدارة الرسائل التذكيرية',
        'logout' => 'تسجيل الخروج',
        'back_to_events' => 'العودة للحفلات',
        'event_reminders' => 'تذكيرات الحفل',
        'reminder_settings' => 'إعدادات التذكير',
        'days_before_event' => 'عدد الأيام قبل الحفل',
        'reminder_message' => 'رسالة التذكير',
        'send_reminders' => 'إرسال التذكيرات',
        'auto_reminder_title' => 'تذكير تلقائي',
        'enable_auto_reminder' => 'تفعيل التذكير التلقائي',
        'reminder_schedule' => 'جدولة التذكير',
        'reminder_image' => 'صورة التذكير',
        'use_event_image' => 'استخدام صورة الحفل',
        'upload_reminder_image' => 'رفع صورة تذكير جديدة',
        'reminder_stats' => 'إحصائيات التذكير',
        'last_reminder_sent' => 'آخر تذكير مُرسل',
        'reminder_sent_count' => 'عدد التذكيرات المرسلة',
        'confirmed_after_reminder' => 'تأكيدات بعد التذكير',
        'pending_guests' => 'ضيوف لم يؤكدوا',
        'send_to_pending_only' => 'إرسال للمعلقين فقط',
        'send_to_all_guests' => 'إرسال لجميع الضيوف',
        'reminder_type' => 'نوع التذكير',
        'quick_reminder' => 'تذكير سريع',
        'scheduled_reminder' => 'تذكير مجدول',
        'reminder_success' => 'تم إرسال التذكيرات بنجاح!',
        'reminder_error' => 'حدث خطأ في إرسال التذكيرات',
        'no_webhook_configured' => 'لم يتم تكوين webhook للحفل',
        'reminder_history' => 'تاريخ التذكيرات',
        'view_details' => 'عرض التفاصيل',
        'guest_name' => 'اسم الضيف',
        'phone_number' => 'رقم الهاتف',
        'status' => 'الحالة',
        'reminder_sent_at' => 'وقت الإرسال',
        'processing' => 'جاري المعالجة...',
        'select_reminder_image' => 'اختيار صورة التذكير',
        'current_reminder_image' => 'صورة التذكير الحالية',
        'remove_reminder_image' => 'حذف صورة التذكير',
        'image_preview' => 'معاينة الصورة',
        'save_settings' => 'حفظ الإعدادات',
        'event_date_passed' => 'تاريخ الحفل قد مضى',
        'days_until_event' => 'أيام متبقية للحفل',
        'event_is_today' => 'الحفل اليوم!',
        'custom_message_placeholder' => 'اكتب رسالة التذكير هنا... (اختياري - سيتم استخدام الرسالة الافتراضية إذا تُركت فارغة)'
    ],
    'en' => [
        'reminder_management' => 'Reminder Management',
        'logout' => 'Logout',
        'back_to_events' => 'Back to Events',
        'event_reminders' => 'Event Reminders',
        'reminder_settings' => 'Reminder Settings',
        'days_before_event' => 'Days Before Event',
        'reminder_message' => 'Reminder Message',
        'send_reminders' => 'Send Reminders',
        'auto_reminder_title' => 'Auto Reminder',
        'enable_auto_reminder' => 'Enable Auto Reminder',
        'reminder_schedule' => 'Reminder Schedule',
        'reminder_image' => 'Reminder Image',
        'use_event_image' => 'Use Event Image',
        'upload_reminder_image' => 'Upload New Reminder Image',
        'reminder_stats' => 'Reminder Statistics',
        'last_reminder_sent' => 'Last Reminder Sent',
        'reminder_sent_count' => 'Reminders Sent Count',
        'confirmed_after_reminder' => 'Confirmations After Reminder',
        'pending_guests' => 'Pending Guests',
        'send_to_pending_only' => 'Send to Pending Only',
        'send_to_all_guests' => 'Send to All Guests',
        'reminder_type' => 'Reminder Type',
        'quick_reminder' => 'Quick Reminder',
        'scheduled_reminder' => 'Scheduled Reminder',
        'reminder_success' => 'Reminders sent successfully!',
        'reminder_error' => 'Error sending reminders',
        'no_webhook_configured' => 'No webhook configured for event',
        'reminder_history' => 'Reminder History',
        'view_details' => 'View Details',
        'guest_name' => 'Guest Name',
        'phone_number' => 'Phone Number',
        'status' => 'Status',
        'reminder_sent_at' => 'Sent At',
        'processing' => 'Processing...',
        'select_reminder_image' => 'Select Reminder Image',
        'current_reminder_image' => 'Current Reminder Image',
        'remove_reminder_image' => 'Remove Reminder Image',
        'image_preview' => 'Image Preview',
        'save_settings' => 'Save Settings',
        'event_date_passed' => 'Event date has passed',
        'days_until_event' => 'days until event',
        'event_is_today' => 'Event is today!',
        'custom_message_placeholder' => 'Write reminder message here... (optional - default message will be used if left empty)'
    ]
];
$t = array_merge($t, $texts[$lang]);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reminder']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $reminder_type = $_POST['reminder_type'] ?? 'pending_only';
        $custom_message = trim($_POST['custom_message'] ?? '');
        $reminder_image = $_POST['reminder_image_option'] ?? 'event_image';

        $stmt = $mysqli->prepare("SELECT n8n_initial_invite_webhook FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();

        if ($event && !empty($event['n8n_initial_invite_webhook'])) {
            $webhook_url = $event['n8n_initial_invite_webhook'];

            $payload = [
                'action' => 'send_reminder',
                'event_id' => (int)$event_id,
                'reminder_type' => $reminder_type,
                'custom_message' => $custom_message,
                'reminder_image' => $reminder_image,
                'timestamp' => time()
            ];

            $ch = curl_init($webhook_url);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($payload))
                ],
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $stmt = $mysqli->prepare("INSERT INTO reminder_logs (event_id, reminder_type, custom_message, response_data, http_code, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssi", $event_id, $reminder_type, $custom_message, $response, $httpCode);
            $stmt->execute();
            $stmt->close();

            if ($httpCode >= 200 && $httpCode < 300) {
                $message = $t['reminder_success']; $messageType = 'success';
            } else {
                $message = $t['reminder_error']; $messageType = 'error';
            }
        } else {
            $message = $t['no_webhook_configured']; $messageType = 'error';
        }

        header('Location: send_invitations.php?event_id=' . $event_id . '&message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_reminder_settings']) && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch.'; $messageType = 'error';
    } else {
        $current_reminder_image = $_POST['current_reminder_image'] ?? '';

        if (isset($_FILES['reminder_image_upload']) && $_FILES['reminder_image_upload']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['reminder_image_upload']['type'];
            $file_size = $_FILES['reminder_image_upload']['size'];

            if (in_array($file_type, $allowed_types) && $file_size <= 5000000) {
                $upload_dir = './uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $fileTmpPath = $_FILES['reminder_image_upload']['tmp_name'];
                $fileName = $_FILES['reminder_image_upload']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newFileName = 'reminder_event_' . $event_id . '_' . time() . '.' . $fileExtension;
                $destPath = $upload_dir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $destPath)) {
                    if (!empty($current_reminder_image) && file_exists($current_reminder_image)) {
                        unlink($current_reminder_image);
                    }
                    $current_reminder_image = $destPath;
                }
            }
        } elseif (isset($_POST['remove_reminder_image']) && $_POST['remove_reminder_image'] === '1') {
            if (!empty($current_reminder_image) && file_exists($current_reminder_image)) {
                unlink($current_reminder_image);
            }
            $current_reminder_image = '';
        }

        $stmt = $mysqli->prepare("UPDATE events SET reminder_image_url = ? WHERE id = ?");
        $stmt->bind_param("si", $current_reminder_image, $event_id);
        $stmt->execute();
        $stmt->close();

        $message = 'تم حفظ إعدادات التذكير بنجاح'; $messageType = 'success';
        header('Location: send_invitations.php?event_id=' . $event_id . '&message=' . urlencode($message) . '&messageType=' . $messageType);
        exit;
    }
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
    $messageType = $_GET['messageType'] ?? 'success';
}

$event = null;
$stmt = $mysqli->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    header('Location: events.php');
    exit;
}
$stmt->close();

$days_until_event = null;
$event_date_parsed = null;
if (!empty($event['event_date_ar'])) {
    if (preg_match('/(\d{1,2})\s*\/\s*(\d{1,2})\s*\/\s*(\d{4})/', $event['event_date_ar'], $matches)) {
        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];
        $event_date_parsed = "$year-$month-$day";
        $event_timestamp = strtotime($event_date_parsed);
        $today_timestamp = strtotime(date('Y-m-d'));
        $days_until_event = ceil(($event_timestamp - $today_timestamp) / 86400);
    }
}

$stmt = $mysqli->prepare("SELECT
    COUNT(*) as total_guests,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_guests,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_guests,
    SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_guests
    FROM guests WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$stmt->close();

$mysqli->query("
    CREATE TABLE IF NOT EXISTS reminder_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        reminder_type VARCHAR(50) NOT NULL,
        custom_message TEXT,
        response_data TEXT,
        http_code INT DEFAULT 0,
        created_at DATETIME NOT NULL,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )
");

$mysqli->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS reminder_image_url VARCHAR(1024) DEFAULT NULL COMMENT 'رابط صورة التذكير'");

$stmt = $mysqli->prepare("SELECT * FROM reminder_logs WHERE event_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$reminder_logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
