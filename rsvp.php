<?php
// rsvp.php - Optimized for mobile performance
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

if (!session_id()) {
    session_start();
}

// Enable output compression
if (extension_loaded('zlib') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}

require_once 'db_config.php';

// Performance headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Language System
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
if (isset($_POST['switch_language'])) {
    $lang = $_POST['switch_language'] === 'en' ? 'en' : 'ar';
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Language texts (optimized)
$texts = [
    'ar' => [
        'welcome_guest' => 'مرحباً بكم',
        'dear_guest' => 'ضيفنا الكريم',
        'guest_count' => 'عدد الضيوف',
        'table_number' => 'رقم الطاولة',
        'confirm_attendance' => 'تأكيد الحضور',
        'decline_attendance' => 'الاعتذار عن الحضور',
        'add_to_calendar' => 'إضافة للتقويم',
        'share_invitation' => 'مشاركة الدعوة',
        'get_directions' => 'الحصول على الاتجاهات',
        'download_qr' => 'تحميل QR',
        'entry_card' => 'بطاقة الدخول',
        'qr_code' => 'رمز الاستجابة السريعة',
        'show_at_entrance' => 'أظهر هذا الرمز عند الدخول',
        'already_confirmed' => 'تم تأكيد حضورك بنجاح!',
        'already_declined' => 'تم تسجيل اعتذارك',
        'success_confirmed' => 'تم تأكيد حضورك بنجاح!',
        'success_declined' => 'شكراً لك، تم تسجيل اعتذارك عن الحضور.',
        'error_occurred' => 'حدث خطأ، يرجى المحاولة مرة أخرى',
        'invalid_link' => 'رابط الدعوة غير صالح',
        'csrf_error' => 'خطأ في الحماية، يرجى إعادة تحميل الصفحة',
        'rate_limit_error' => 'تم إرسال طلبات كثيرة، يرجى الانتظار',
        'connection_error' => 'خطأ في الاتصال',
        'countdown_title' => 'العد التنازلي للحفل',
        'days' => 'يوم',
        'hours' => 'ساعة',
        'minutes' => 'دقيقة',
        'seconds' => 'ثانية',
        'event_time_reached' => '🎉 حان وقت الحفل! 🎉',
        'enjoy_time' => 'نتمنى لكم وقتاً ممتعاً'
    ],
    'en' => [
        'welcome_guest' => 'Welcome',
        'dear_guest' => 'Dear Guest',
        'guest_count' => 'Guest Count',
        'table_number' => 'Table Number',
        'confirm_attendance' => 'Confirm Attendance',
        'decline_attendance' => 'Decline Attendance',
        'add_to_calendar' => 'Add to Calendar',
        'share_invitation' => 'Share Invitation',
        'get_directions' => 'Get Directions',
        'download_qr' => 'Download QR',
        'entry_card' => 'Entry Card',
        'qr_code' => 'QR Code',
        'show_at_entrance' => 'Show this code at entrance',
        'already_confirmed' => 'Your attendance has been confirmed!',
        'already_declined' => 'Your decline has been recorded',
        'success_confirmed' => 'Your attendance has been confirmed successfully!',
        'success_declined' => 'Thank you, your decline has been recorded.',
        'error_occurred' => 'An error occurred, please try again',
        'invalid_link' => 'Invalid invitation link',
        'csrf_error' => 'Security error, please reload the page',
        'rate_limit_error' => 'Too many requests, please wait',
        'connection_error' => 'Connection error',
        'countdown_title' => 'Event Countdown',
        'days' => 'Days',
        'hours' => 'Hours',
        'minutes' => 'Minutes',
        'seconds' => 'Seconds',
        'event_time_reached' => '🎉 Event Time! 🎉',
        'enjoy_time' => 'Have a wonderful time!'
    ]
];

$t = $texts[$lang];

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

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
    // Fetch guest data with optimized query
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
                // Added the new setting for the countdown
                'rsvp_show_countdown' => $combined_data['rsvp_show_countdown'] ?? 1
            ];
        } else {
            $error_message = $t['invalid_link'];
        }
        $stmt_guest->close();
    }
}

// Handle AJAX RSVP Response
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

/**
 * Formats a DateTime object into a user-friendly Arabic string.
 * @param DateTime $dateTime The DateTime object to format.
 * @return string The formatted Arabic date string.
 */
function formatDateArabic(DateTime $dateTime) {
    $months = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
    $days = ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];

    $dayOfWeek = $days[(int)$dateTime->format('w')];
    $dayOfMonth = $dateTime->format('j');
    $month = $months[(int)$dateTime->format('n') - 1];
    $year = $dateTime->format('Y');
    $time = $dateTime->format('g:i');
    $am_pm = $dateTime->format('A') === 'AM' ? 'صباحًا' : 'مساءً';

    return "{$dayOfWeek}، {$dayOfMonth} {$month} {$year}، {$time} {$am_pm}";
}

$event_datetime_iso = '';
$event_date_for_countdown = date('Y-m-d'); // Fallback
if (isset($event_data['event_date_ar'])) {
    try {
        $date = new DateTime($event_data['event_date_ar']);
        // ISO 8601 format for JavaScript and calendar functions
        $event_datetime_iso = $date->format('Y-m-d\TH:i:s');
        // Y-m-d format for the simple countdown parser
        $event_date_for_countdown = $date->format('Y-m-d');
    } catch (Exception $e) {
        $event_datetime_iso = date('Y-m-d\TH:i:s');
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title><?= $event_data ? htmlspecialchars($event_data['event_name']) : 'دعوة' ?></title>
    
    <meta name="description" content="<?= htmlspecialchars($event_data['event_paragraph_ar'] ?? 'دعوة خاصة') ?>">
    <meta name="keywords" content="دعوة,حفل,زفاف,invitation,wedding">
    
    <meta property="og:title" content="<?= htmlspecialchars($event_data['event_name'] ?? 'دعوة') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($event_data['event_paragraph_ar'] ?? 'دعوة خاصة') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($event_data['background_image_url'] ?? '') ?>">
    
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    
    <style>
        /* Critical styles only - optimized for mobile performance */
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family:<?= $lang === 'ar' ? "'Tahoma','Segoe UI',system-ui,sans-serif" : "system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif" ?>;
            background:#fff;padding:20px;color:#000;min-height:100vh;
            display:flex;align-items:center;justify-content:center;line-height:1.5;
        }
        
        .card-container{
            max-width:500px;width:100%;background:#fff;border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);border:1px solid #e5e7eb;
            position:relative;overflow:hidden;
        }
        
        .language-toggle{
            position:absolute;top:15px;<?= $lang === 'ar' ? 'left:15px' : 'right:15px' ?>;z-index:10;
        }
        
        .language-toggle button{
            background:rgba(255,255,255,0.9);border:2px solid rgba(45,74,34,0.3);
            padding:8px 16px;border-radius:20px;font-size:12px;font-weight:600;
            cursor:pointer;color:#2d4a22;backdrop-filter:blur(10px);
            transition:all 0.3s ease;
        }
        
        .language-toggle button:hover{
            background:rgba(255,255,255,0.95);transform:translateY(-1px);
            border-color:rgba(45,74,34,0.5);
        }
        
        .description-box{
            padding:40px 25px;background:#f8f9fa;text-align:center;
            color:#000;font-size:1.1rem;line-height:1.8;
        }
        
        .card-content{padding:30px;background:#fff}
        
        .guest-welcome,.countdown-section,.location-card,.qr-code-section,.guest-details{
            padding:18px 25px;border-radius:50px;font-weight:600;color:#2d4a22;
            border:2px solid rgba(45,74,34,0.3);background:rgba(255,255,255,0.9);
            backdrop-filter:blur(10px);cursor:pointer;transition:all 0.4s ease;
            position:relative;overflow:hidden;box-shadow:0 4px 15px rgba(45,74,34,0.1);
            margin:20px 0;
        }
        
        .guest-welcome::before,.countdown-section::before,.location-card::before,
        .qr-code-section::before,.guest-details::before{
            content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;
            background:linear-gradient(90deg,transparent,rgba(45,74,34,0.1),transparent);
            transition:left 0.6s ease;
        }
        
        .guest-welcome:hover::before,.countdown-section:hover::before,
        .location-card:hover::before,.qr-code-section:hover::before,.guest-details:hover::before{
            left:100%;
        }
        
        .guest-welcome:hover,.countdown-section:hover,.location-card:hover,
        .qr-code-section:hover,.guest-details:hover{
            transform:translateY(-3px) scale(1.02);box-shadow:0 8px 25px rgba(45,74,34,0.2);
            border-color:rgba(45,74,34,0.5);color:#1a2f15;background:rgba(255,255,255,0.95);
        }
        
        .guest-welcome{text-align:center;margin-bottom:25px}
        .guest-welcome h2{font-size:1.25rem;margin-bottom:10px}
        
        .countdown-section{text-align:center}
        .countdown-timer{
            display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));
            gap:12px;margin-top:20px;max-width:400px;margin-left:auto;margin-right:auto;
        }
        
        .countdown-item{
            background:rgba(255,255,255,0.8);padding:15px 8px;border-radius:25px;
            backdrop-filter:blur(5px);border:2px solid rgba(45,74,34,0.3);
            transition:all 0.3s ease;min-height:80px;display:flex;
            flex-direction:column;justify-content:center;align-items:center;
            position:relative;overflow:hidden;color:#2d4a22;
        }
        
        .countdown-item:hover{
            transform:translateY(-2px) scale(1.02);box-shadow:0 4px 12px rgba(45,74,34,0.15);
            border-color:rgba(45,74,34,0.5);background:rgba(255,255,255,0.95);
        }
        
        .countdown-number{
            font-size:clamp(1.5rem,4vw,2.2rem);font-weight:bold;
            line-height:1;margin-bottom:5px;
        }
        
        .countdown-label{
            font-size:clamp(0.7rem,2.5vw,0.85rem);opacity:0.8;font-weight:600;
        }
        
        .guest-details{
            display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:15px;
        }
        
        .detail-item{
            text-align:center;padding:15px 10px;background:rgba(255,255,255,0.7);
            border-radius:20px;border:2px solid rgba(45,74,34,0.2);
            transition:all 0.3s ease;position:relative;overflow:hidden;color:#2d4a22;
        }
        
        .detail-item:hover{
            transform:translateY(-2px) scale(1.02);box-shadow:0 4px 12px rgba(45,74,34,0.1);
            background:rgba(255,255,255,0.9);border-color:rgba(45,74,34,0.4);
        }
        
        .detail-label{font-size:0.8rem;margin-bottom:8px;font-weight:600;opacity:0.8}
        .detail-value{font-weight:bold;font-size:1.1rem}
        
        .qr-code-section{text-align:center;display:none}
        .qr-code-section.active{display:block;animation:slideDown 0.5s ease-out}
        
        .qr-grid{
            display:grid;grid-template-columns:1fr auto 1fr;grid-template-rows:auto auto auto;
            gap:15px;align-items:center;max-width:400px;margin:0 auto;
        }
        
        .qr-title-box{
            grid-column:1/4;background:rgba(255,255,255,0.8);padding:15px;
            border-radius:25px;text-align:center;backdrop-filter:blur(10px);
            color:#2d4a22;border:2px solid rgba(45,74,34,0.3);
        }
        
        .qr-code-container{
            grid-column:2/3;display:flex;justify-content:center;align-items:center;
            background:white;padding:15px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1);
        }
        
        .qr-info{display:flex;flex-direction:column;align-items:center;gap:10px;color:#2d4a22}
        
        .action-buttons{display:flex;gap:20px;margin-top:30px}
        .action-buttons button{
            flex:1;padding:18px 25px;border-radius:50px;font-weight:600;color:#2d4a22;
            border:2px solid rgba(45,74,34,0.3);background:rgba(255,255,255,0.9);
            backdrop-filter:blur(10px);cursor:pointer;transition:all 0.4s ease;
            font-size:16px;position:relative;overflow:hidden;
            box-shadow:0 4px 15px rgba(45,74,34,0.1);
        }
        
        .action-buttons button:hover{
            transform:translateY(-3px) scale(1.02);box-shadow:0 8px 25px rgba(45,74,34,0.2);
            border-color:rgba(45,74,34,0.5);color:#1a2f15;background:rgba(255,255,255,0.95);
        }
        
        .action-buttons button:disabled{
            opacity:0.6;cursor:not-allowed;transform:none;
            background:rgba(200,200,200,0.5);color:#888;border-color:rgba(200,200,200,0.3);
        }
        
        .spinner{
            display:none;width:20px;height:20px;border:2px solid rgba(45,74,34,0.3);
            border-radius:50%;border-top-color:#2d4a22;animation:spin 1s ease-in-out infinite;
            margin-right:10px;
        }
        
        @keyframes spin{to{transform:rotate(360deg)}}
        @keyframes slideDown{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
        
        .share-buttons{
            display:flex;gap:10px;justify-content:center;margin-top:20px;flex-wrap:wrap;
        }
        
        .share-button{
            padding:12px 20px;border-radius:30px;border:2px solid rgba(45,74,34,0.3);
            background:rgba(255,255,255,0.9);backdrop-filter:blur(10px);color:#2d4a22;
            cursor:pointer;font-weight:600;transition:all 0.4s ease;font-size:14px;
            display:flex;align-items:center;gap:8px;position:relative;overflow:hidden;
            box-shadow:0 3px 12px rgba(45,74,34,0.1);
        }
        
        .share-button:hover{
            transform:translateY(-2px) scale(1.02);box-shadow:0 6px 20px rgba(45,74,34,0.15);
            border-color:rgba(45,74,34,0.5);background:rgba(255,255,255,0.95);
        }
        
        .toast{
            position:fixed;top:20px;right:20px;background:rgba(255,255,255,0.95);
            backdrop-filter:blur(10px);border:2px solid rgba(45,74,34,0.3);color:#2d4a22;
            padding:15px 20px;border-radius:30px;box-shadow:0 10px 25px rgba(45,74,34,0.2);
            transform:translateX(400px);transition:transform 0.3s ease;z-index:1000;font-weight:600;
        }
        
        .toast.show{transform:translateX(0)}
        .toast.error{
            background:rgba(255,240,240,0.95);border-color:rgba(239,68,68,0.3);color:#dc2626;
        }
        
        .event-image-container{position:relative;overflow:hidden;background:#f8f9fa}
        .event-image{width:100%;height:350px;object-fit:cover;display:block}
        
        .error-container{text-align:center;padding:60px 40px;background:white}
        .error-icon{font-size:4rem;color:#ef4444;margin-bottom:20px}
        
        /* Mobile optimizations */
        @media(max-width:640px){
            body{padding:10px}
            .card-container{margin:10px;max-width:calc(100vw - 20px)}
            .card-content{padding:20px}
            .guest-welcome,.countdown-section,.location-card,.qr-code-section,.guest-details{
                margin:15px 0;padding:15px 20px;border-radius:40px;
            }
            .guest-details{grid-template-columns:1fr}
            .action-buttons{flex-direction:column}
            .share-buttons{flex-direction:column}
            .qr-grid{
                grid-template-columns:1fr;grid-template-rows:auto auto auto auto;
            }
            .qr-code-container{grid-column:1/2}
            .countdown-timer{
                grid-template-columns:repeat(2,1fr);gap:10px;max-width:280px;
            }
            .countdown-item{
                min-height:70px;padding:12px 6px;border-radius:20px;
            }
            .countdown-number{font-size:clamp(1.2rem,5vw,1.8rem)}
            .countdown-label{font-size:clamp(0.6rem,3vw,0.75rem)}
            .detail-item{padding:12px 8px;border-radius:15px}
            .detail-value{font-size:1rem}
            .qr-title-box{border-radius:20px}
        }
        
        @media(min-width:641px) and (max-width:1024px){
            .countdown-timer{
                grid-template-columns:repeat(4,1fr);gap:12px;max-width:350px;
            }
            .countdown-item{
                min-height:75px;padding:12px 8px;border-radius:22px;
            }
            .qr-title-box{border-radius:22px}
        }
        
        @media(min-width:1025px){
            .countdown-timer{
                grid-template-columns:repeat(4,1fr);gap:15px;max-width:400px;
            }
            .countdown-item{
                min-height:85px;padding:15px 10px;border-radius:25px;
            }
            .qr-title-box{border-radius:25px}
        }
    </style>
    
    <script>
        const loadQRLibrary = () => {
            if (!window.QRCode) {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
                document.head.appendChild(script);
                return new Promise(resolve => script.onload = resolve);
            }
            return Promise.resolve();
        };
    </script>
    
</head>
<body>
    <div class="card-container">
        <div class="language-toggle">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>">
                    <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                </button>
            </form>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-container">
                <div class="error-icon">⚠</div>
                <h2 style="font-size:1.5rem;font-weight:bold;color:#374151;margin-bottom:1rem"><?= $t['invalid_link'] ?></h2>
                <p style="font-size:1rem;color:#6b7280"><?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php else: ?>
            
            <?php if (!empty($event_data['background_image_url'])): ?>
                <div class="event-image-container">
                    <img src="<?= htmlspecialchars($event_data['background_image_url']) ?>" 
                         alt="<?= htmlspecialchars($event_data['event_name']) ?>" 
                         class="event-image"
                         loading="lazy"
                         decoding="async">
                </div>
            <?php else: ?>
                <div class="description-box">
                    <p><?= nl2br(htmlspecialchars($event_data['event_paragraph_ar'] ?? 'مرحباً بكم في مناسبتنا الخاصة.')) ?></p>
                </div>
            <?php endif; ?>

            <div class="card-content" id="main-content">
                <div class="guest-welcome">
                    <h2><?= $t['welcome_guest'] ?></h2>
                    <p style="font-size:1.125rem;font-weight:600">
                        <?= htmlspecialchars($guest_data['name_ar'] ?? $t['dear_guest']) ?>
                    </p>
                </div>

                <?php if (!empty($event_data['rsvp_show_countdown'])): ?>
                <div class="countdown-section">
                    <h3 style="font-size:1.125rem;font-weight:bold;margin-bottom:10px">
                        <?= $t['countdown_title'] ?>
                    </h3>
                    <div class="countdown-timer" id="countdown-timer">
                        <div class="countdown-item">
                            <span class="countdown-number" id="days">--</span>
                            <div class="countdown-label"><?= $t['days'] ?></div>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">--</span>
                            <div class="countdown-label"><?= $t['hours'] ?></div>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">--</span>
                            <div class="countdown-label"><?= $t['minutes'] ?></div>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">--</span>
                            <div class="countdown-label"><?= $t['seconds'] ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="guest-details">
                    <?php if (!empty($event_data['rsvp_show_guest_count'])): ?>
                    <div class="detail-item">
                        <div class="detail-label"><?= $t['guest_count'] ?></div>
                        <div class="detail-value"><?= htmlspecialchars($guest_data['guests_count'] ?? '1') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($guest_data['table_number'])): ?>
                    <div class="detail-item">
                        <div class="detail-label"><?= $t['table_number'] ?></div>
                        <div class="detail-value"><?= htmlspecialchars($guest_data['table_number']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($event_data['venue_ar']) || !empty($event_data['Maps_link'])): ?>
                <div class="location-card">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <h3 style="font-weight:bold;margin-bottom:5px">
                                <?= htmlspecialchars($event_data['venue_ar'] ?? 'مكان الحفل') ?>
                            </h3>
                            <?php 
                            $display_date = '';
                            if ($lang === 'en' && !empty($event_data['event_date_en'])) {
                                $display_date = htmlspecialchars($event_data['event_date_en']);
                            } elseif ($lang === 'ar' && !empty($event_data['event_date_ar'])) {
                                try {
                                    $dateObj = new DateTime($event_data['event_date_ar']);
                                    $display_date = formatDateArabic($dateObj);
                                } catch (Exception $e) {
                                    $display_date = htmlspecialchars($event_data['event_date_ar']);
                                }
                            }
                            ?>
                            <?php if (!empty($display_date)): ?>
                            <p style="font-size:0.875rem">
                                <?= $display_date ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($event_data['Maps_link'])): ?>
                        <a href="<?= htmlspecialchars($event_data['Maps_link']) ?>" 
                           target="_blank" 
                           style="color:#2d4a22;font-size:1.25rem;transition:opacity 0.3s ease"
                           title="<?= $t['get_directions'] ?>">
                            ⚲
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div id="action-buttons-section" class="action-buttons">
                    <button id="confirm-button" onclick="handleRSVP('confirmed')">
                        <div class="spinner" id="confirm-spinner"></div>
                        <span id="confirm-text"><?= $t['confirm_attendance'] ?></span>
                    </button>
                    <button id="cancel-button" onclick="handleRSVP('canceled')">
                        <div class="spinner" id="cancel-spinner"></div>
                        <span id="cancel-text"><?= $t['decline_attendance'] ?></span>
                    </button>
                </div>
                
                <div id="response-message" style="display:none;margin-top:25px;padding:20px;border-radius:30px;text-align:center;font-weight:600"></div>
                
                <div class="share-buttons">
                    <button onclick="addToCalendar()" class="share-button">
                        <?= $t['add_to_calendar'] ?>
                    </button>
                    
                    <button onclick="shareInvitation()" class="share-button">
                        <?= $t['share_invitation'] ?>
                    </button>
                    
                    <?php if (!empty($event_data['Maps_link'])): ?>
                    <button onclick="openLocation()" class="share-button">
                        <?= $t['get_directions'] ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($event_data['rsvp_show_qr_code'])): ?>
            <div id="qr-code-section" class="qr-code-section">
                <div class="qr-grid">
                    <div class="qr-title-box">
                        <h3 style="font-size:1.25rem;font-weight:bold;margin-bottom:10px">
                            <?= htmlspecialchars($event_data['qr_card_title_ar'] ?? $t['entry_card']) ?>
                        </h3>
                        <p style="font-size:0.875rem"><?= $t['qr_code'] ?></p>
                    </div>
                    
                    <?php if (!empty($event_data['rsvp_show_guest_count'])): ?>
                    <div class="qr-info qr-info-left">
                        <div style="text-align:center">
                            <div style="font-size:0.75rem;margin-bottom:5px"><?= $t['guest_count'] ?></div>
                            <div style="font-size:1.5rem;font-weight:bold"><?= htmlspecialchars($guest_data['guests_count'] ?? '1') ?></div>
                        </div>
                        <div style="font-size:0.75rem;margin-top:20px">
                            <?= htmlspecialchars($event_data['qr_brand_text_ar'] ?? 'وصول') ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div id="qrcode" class="qr-code-container"></div>
                    
                    <div class="qr-info qr-info-right" style="text-align:center">
                        <p style="font-size:0.875rem;font-weight:600;margin-bottom:10px">
                            <?= htmlspecialchars($event_data['qr_show_code_instruction_ar'] ?? $t['show_at_entrance']) ?>
                        </p>
                        <div style="font-size:0.75rem">
                            <?= htmlspecialchars($event_data['qr_website'] ?? 'wosuol.com') ?>
                        </div>
                    </div>
                </div>
                
                <div class="share-buttons" style="margin-top:25px">
                    <button onclick="downloadQR()" class="share-button">
                        <?= $t['download_qr'] ?>
                    </button>
                    
                    <button onclick="shareQR()" class="share-button">
                        <?= $t['share_invitation'] ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <div id="toast" class="toast">
        <div id="toast-message"></div>
    </div>

    <?php if (empty($error_message)): ?>
    <script>
        // Configuration and Data
        const CONFIG = {
            guestData: <?= json_encode($guest_data, JSON_UNESCAPED_UNICODE) ?>,
            eventData: <?= json_encode($event_data, JSON_UNESCAPED_UNICODE) ?>,
            texts: <?= json_encode($t, JSON_UNESCAPED_UNICODE) ?>,
            lang: '<?= $lang ?>',
            csrfToken: '<?= htmlspecialchars($_SESSION['csrf_token']) ?>',
            eventDateTimeISO: '<?= $event_datetime_iso ?>'
        };

        // Global state
        let qrCodeGenerated = false;
        let countdownInterval;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkInitialStatus();
            // Start countdown only if the setting is enabled
            if (CONFIG.eventData.rsvp_show_countdown) {
                startCountdown();
            }
        });

        // Countdown Timer Function
        function startCountdown() {
            const eventDate = new Date(CONFIG.eventDateTimeISO);
            
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = eventDate.getTime() - now;
                
                if (timeLeft > 0) {
                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                    
                    const els = {
                        days: document.getElementById('days'),
                        hours: document.getElementById('hours'),
                        minutes: document.getElementById('minutes'),
                        seconds: document.getElementById('seconds')
                    };
                    
                    if (els.days) els.days.textContent = days.toString().padStart(2, '0');
                    if (els.hours) els.hours.textContent = hours.toString().padStart(2, '0');
                    if (els.minutes) els.minutes.textContent = minutes.toString().padStart(2, '0');
                    if (els.seconds) els.seconds.textContent = seconds.toString().padStart(2, '0');
                } else {
                    clearInterval(countdownInterval);
                    const countdownSection = document.querySelector('.countdown-section');
                    if (countdownSection) {
                        countdownSection.innerHTML = `
                            <h3 style="font-size:1.125rem;font-weight:bold;margin-bottom:10px">
                                ${CONFIG.texts.event_time_reached}
                            </h3>
                            <p>${CONFIG.texts.enjoy_time}</p>
                        `;
                    }
                }
            }
            
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        }

        // Check initial guest status
        function checkInitialStatus() {
            const status = CONFIG.guestData.status;
            
            if (status === 'confirmed') {
                showSuccessState('confirmed');
            } else if (status === 'canceled') {
                showSuccessState('canceled');
            }
        }

        // Handle RSVP response
        async function handleRSVP(status) {
            const confirmBtn = document.getElementById('confirm-button');
            const cancelBtn = document.getElementById('cancel-button');
            const spinner = document.getElementById(status === 'confirmed' ? 'confirm-spinner' : 'cancel-spinner');
            
            confirmBtn.disabled = true;
            cancelBtn.disabled = true;
            spinner.style.display = 'inline-block';
            
            try {
                const formData = new FormData();
                formData.append('ajax_rsvp', '1');
                formData.append('status', status);
                formData.append('guest_id', CONFIG.guestData.guest_id);
                formData.append('csrf_token', CONFIG.csrfToken);

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showSuccessState(status);
                    showToast(result.message, 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('RSVP Error:', error);
                showToast(error.message || CONFIG.texts.connection_error, 'error');
                confirmBtn.disabled = false;
                cancelBtn.disabled = false;
            } finally {
                spinner.style.display = 'none';
            }
        }

        // Show success state
        function showSuccessState(status) {
            const actionButtons = document.getElementById('action-buttons-section');
            const responseMessage = document.getElementById('response-message');
            const qrSection = document.getElementById('qr-code-section');
            
            actionButtons.style.display = 'none';
            
            if (status === 'confirmed') {
                responseMessage.style.background = 'rgba(255, 255, 255, 0.9)';
                responseMessage.style.backdropFilter = 'blur(10px)';
                responseMessage.style.border = '2px solid rgba(45, 74, 34, 0.3)';
                responseMessage.style.color = '#2d4a22';
                responseMessage.style.boxShadow = '0 4px 15px rgba(45, 74, 34, 0.1)';
                responseMessage.innerHTML = `✓ ${CONFIG.texts.already_confirmed}`;
                responseMessage.style.display = 'block';
                
                if (qrSection) {
                    qrSection.classList.add('active');
                    generateQRCode();
                }
            } else {
                responseMessage.style.background = 'rgba(255, 240, 240, 0.9)';
                responseMessage.style.backdropFilter = 'blur(10px)';
                responseMessage.style.border = '2px solid rgba(239, 68, 68, 0.3)';
                responseMessage.style.color = '#dc2626';
                responseMessage.style.boxShadow = '0 4px 15px rgba(239, 68, 68, 0.1)';
                responseMessage.innerHTML = `✗ ${CONFIG.texts.already_declined}`;
                responseMessage.style.display = 'block';
            }
        }

        // Generate QR Code
        async function generateQRCode() {
            if (qrCodeGenerated) return;
            
            const qrcodeContainer = document.getElementById('qrcode');
            if (!qrcodeContainer) return;
            
            qrcodeContainer.innerHTML = '';
            
            try {
                await loadQRLibrary();
                new QRCode(qrcodeContainer, {
                    text: CONFIG.guestData.guest_id,
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
                qrCodeGenerated = true;
            } catch (error) {
                console.error('QR Generation Error:', error);
                qrcodeContainer.innerHTML = '<div style="color:#ef4444">QR Code generation failed</div>';
            }
        }

        // Download QR Code
        function downloadQR() {
            try {
                const qrCanvas = document.querySelector('#qrcode canvas');
                if (!qrCanvas) {
                    showToast('QR Code not generated yet', 'error');
                    return;
                }

                const link = document.createElement('a');
                link.download = `invitation-qr-${CONFIG.guestData.guest_id}.png`;
                link.href = qrCanvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showToast('QR Code downloaded successfully!', 'success');
            } catch (error) {
                console.error('Download Error:', error);
                showToast('Download failed', 'error');
            }
        }

        // Share QR Code
        async function shareQR() {
            try {
                const qrCanvas = document.querySelector('#qrcode canvas');
                if (!qrCanvas) {
                    showToast('QR Code not generated yet', 'error');
                    return;
                }

                if (navigator.share && navigator.canShare) {
                    qrCanvas.toBlob(async (blob) => {
                        const file = new File([blob], 'invitation-qr.png', { type: 'image/png' });
                        
                        if (navigator.canShare({ files: [file] })) {
                            await navigator.share({
                                title: CONFIG.eventData.event_name,
                                text: `${CONFIG.texts.share_invitation} - ${CONFIG.eventData.event_name}`,
                                files: [file]
                            });
                        } else {
                            fallbackShare();
                        }
                    });
                } else {
                    fallbackShare();
                }
            } catch (error) {
                console.error('Share Error:', error);
                fallbackShare();
            }
        }

        // Share invitation
        async function shareInvitation() {
            const shareData = {
                title: CONFIG.eventData.event_name,
                text: `${CONFIG.texts.share_invitation} - ${CONFIG.eventData.event_name}`,
                url: window.location.href
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    await navigator.clipboard.writeText(window.location.href);
                    showToast('Link copied to clipboard!', 'success');
                }
            } catch (error) {
                console.error('Share Error:', error);
                fallbackShare();
            }
        }

        // Fallback share method
        function fallbackShare() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                showToast('Link copied to clipboard!', 'success');
            }).catch(() => {
                prompt('Copy this link:', url);
            });
        }

        function addToCalendar() {
            const eventData = CONFIG.eventData;
            const lang = CONFIG.lang;

            const startDate = new Date(CONFIG.eventDateTimeISO);
            if (isNaN(startDate)) {
                showToast('Invalid event date.', 'error');
                return;
            }
            
            // Assume a 3-hour duration for the event
            const endDate = new Date(startDate.getTime() + 3 * 60 * 60 * 1000);

            // Helper function to format a date for calendar URLs (YYYYMMDDTHHMMSSZ)
            const toUTCFormat = (date) => {
                return date.toISOString().replace(/[-:.]/g, '').slice(0, 15) + 'Z';
            };

            const startTimeUTC = toUTCFormat(startDate);
            const endTimeUTC = toUTCFormat(endDate);

            const messages = {
                ar: { opening: 'جاري فتح التقويم...', fileCreated: 'تم إنشاء ملف التقويم!' },
                en: { opening: 'Opening calendar...', fileCreated: 'Calendar file created!' }
            };

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            
            const eventTitle = eventData.event_name || (lang === 'ar' ? 'حدث' : 'Event');
            const eventDescription = eventData.event_paragraph_ar || (lang === 'ar' ? 'دعوة خاصة' : 'Special Invitation');
            const eventLocation = eventData.venue_ar || '';

            if (isIOS) {
                const icsContent = [
                    'BEGIN:VCALENDAR',
                    'VERSION:2.0',
                    'PRODID:-//Wosuol//Event//EN',
                    'BEGIN:VEVENT',
                    `UID:${Date.now()}@wosuol.com`,
                    `DTSTAMP:${startTimeUTC}`,
                    `DTSTART:${startTimeUTC}`,
                    `DTEND:${endTimeUTC}`,
                    `SUMMARY:${eventTitle}`,
                    `DESCRIPTION:${eventDescription}`,
                    `LOCATION:${eventLocation}`,
                    'END:VEVENT',
                    'END:VCALENDAR'
                ].join('\r\n');
                
                const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `${eventTitle}.ics`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showToast(messages[lang].fileCreated, 'success');
            } else {
                // Google Calendar Link
                const googleUrl = new URL('https://calendar.google.com/calendar/render');
                googleUrl.searchParams.append('action', 'TEMPLATE');
                googleUrl.searchParams.append('text', eventTitle);
                googleUrl.searchParams.append('dates', `${startTimeUTC}/${endTimeUTC}`);
                googleUrl.searchParams.append('details', eventDescription);
                googleUrl.searchParams.append('location', eventLocation);
                
                window.open(googleUrl.toString(), '_blank');
                showToast(messages[lang].opening, 'success');
            }
        }


        // Open location
        function openLocation() {
            if (CONFIG.eventData.Maps_link) {
                window.open(CONFIG.eventData.Maps_link, '_blank');
            }
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            toastMessage.textContent = message;
            toast.className = `toast ${type === 'error' ? 'error' : ''}`;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Global Error:', e.error);
            showToast(CONFIG.texts.error_occurred, 'error');
        });

        // Cleanup countdown on page unload
        window.addEventListener('beforeunload', function() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        });

        // Close toast with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const toast = document.getElementById('toast');
                if (toast.classList.contains('show')) {
                    toast.classList.remove('show');
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>