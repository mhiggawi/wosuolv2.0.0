<?php
// src/handlers/register_handler.php

// This file is included by public/register.php and assumes bootstrap.php has been included.

// Enable output compression
if (extension_loaded('zlib') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}

// Performance headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// The language is already set in bootstrap.php, but we need the texts for this page.
// This will be replaced by the centralized language system.
$texts = [
    'ar' => [
        'registration_instruction' => 'يرجى تسجيل بياناتك لتأكيد الحضور أو الاعتذار.',
        'name_label' => 'الاسم الكامل (ثلاثي على الأقل):',
        'placeholder_name' => 'مثال: آدم محمد رياض ',
        'phone_label' => 'رقم الجوال (مع رمز الدولة):',
        'guests_count_label' => 'عدد الضيوف (شاملاً لك):',
        'guests_count_placeholder' => 'مثال: 5',
        'confirm_attendance' => 'تأكيد الحضور',
        'decline_attendance' => 'الاعتذار عن الحضور',
        'select_country' => 'اختر الدولة',
        'enter_local_number' => 'أدخل رقم الجوال المحلي فقط (مثال: 791234567)',
        'registration_success_confirm' => 'تم تأكيد حضورك بنجاح! سيتم الآن نقلك لصفحة الدعوة الخاصة بك للحصول على QR Code.',
        'registration_success_cancel' => 'شكراً لك، تم تسجيل اعتذارك عن الحضور.',
        'registration_error' => 'حدث خطأ أثناء التسجيل. قد يكون رقم الهاتف مسجلاً مسبقاً.',
        'fill_all_fields' => 'الرجاء إدخال جميع الحقول المطلوبة.',
        'invalid_phone_format' => 'يرجى إدخال رقم الجوال المحلي فقط (بدون رمز الدولة). مثال: 791234567',
        'invalid_phone_international' => 'يرجى إدخال الرقم مع رمز الدولة الكامل (مثال: 96279...).',
        'invalid_phone_general' => 'صيغة رقم الجوال غير صحيحة. يرجى إدخال الرقم كاملاً مع رمز الدولة.',
        'invalid_name_format' => 'الرجاء إدخال الاسم كاملاً (ثلاثي على الأقل).',
        'event_location' => 'مكان الحفل',
        'event_time' => 'موعد الحفل',
        'get_directions' => 'الحصول على الاتجاهات',
        'countdown_title' => 'العد التنازلي للحفل',
        'days' => 'يوم',
        'hours' => 'ساعة',
        'minutes' => 'دقيقة',
        'seconds' => 'ثانية',
        'event_time_reached' => '🎉 حان وقت الحفل! 🎉',
        'enjoy_time' => 'نتمنى لكم وقتاً ممتعاً',
        'countries' => [
            '+962' => 'الأردن (+962)',
            '+966' => 'السعودية (+966)',
            '+971' => 'الإمارات (+971)',
            '+965' => 'الكويت (+965)',
            '+974' => 'قطر (+974)',
            '+973' => 'البحرين (+973)',
            '+968' => 'عمان (+968)',
            '+961' => 'لبنان (+961)',
            '+963' => 'سوريا (+963)',
            '+964' => 'العراق (+964)',
            '+970' => 'فلسطين (+970)',
            '+20' => 'مصر (+20)',
            '+1' => 'أمريكا/كندا (+1)',
            '+44' => 'بريطانيا (+44)',
            '+49' => 'ألمانيا (+49)',
            '+33' => 'فرنسا (+33)',
            '+90' => 'تركيا (+90)',
            'other' => 'دولة أخرى'
        ]
    ],
    'en' => [
        'registration_instruction' => 'Please enter your details to confirm or decline attendance',
        'name_label' => 'Full Name (at least three words):',
        'placeholder_name' => 'Example: Adam Mohammad Riyad',
        'phone_label' => 'Mobile Number (with country code):',
        'guests_count_label' => 'Number of Guests (including you):',
        'guests_count_placeholder' => 'Example: 5',
        'confirm_attendance' => 'Confirm Attendance',
        'decline_attendance' => 'Decline Attendance',
        'select_country' => 'Select Country',
        'enter_local_number' => 'Enter local mobile number only (example: 791234567)',
        'registration_success_confirm' => 'Your attendance has been confirmed successfully! You will now be redirected to your invitation page to get the QR Code.',
        'registration_success_cancel' => 'Thank you, your decline has been recorded.',
        'registration_error' => 'An error occurred during registration. The phone number may already be registered.',
        'fill_all_fields' => 'Please fill in all required fields.',
        'invalid_phone_format' => 'Please enter local mobile number only (without country code). Example: 791234567',
        'invalid_phone_international' => 'Please enter the number with full country code (example: 96279...).',
        'invalid_phone_general' => 'Invalid mobile number format. Please enter the full number with country code.',
        'invalid_name_format' => 'Please enter your full name (at least three words).',
        'event_location' => 'Event Location',
        'event_time' => 'Event Time',
        'get_directions' => 'Get Directions',
        'countdown_title' => 'Event Countdown',
        'days' => 'Days',
        'hours' => 'Hours',
        'minutes' => 'Minutes',
        'seconds' => 'Seconds',
        'event_time_reached' => '🎉 Event Time! 🎉',
        'enjoy_time' => 'Have a wonderful time!',
        'countries' => [
            '+962' => 'Jordan (+962)',
            '+966' => 'Saudi Arabia (+966)',
            '+971' => 'UAE (+971)',
            '+965' => 'Kuwait (+965)',
            '+974' => 'Qatar (+974)',
            '+973' => 'Bahrain (+973)',
            '+968' => 'Oman (+968)',
            '+961' => 'Lebanon (+961)',
            '+963' => 'Syria (+963)',
            '+964' => 'Iraq (+964)',
            '+970' => 'Palestine (+970)',
            '+20' => 'Egypt (+20)',
            '+1' => 'USA/Canada (+1)',
            '+44' => 'UK (+44)',
            '+49' => 'Germany (+49)',
            '+33' => 'France (+33)',
            '+90' => 'Turkey (+90)',
            'other' => 'Other Country'
        ]
    ]
];
$t = array_merge($t, $texts[$lang]);

// Rate Limiting
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rate_limit_key = 'rate_limit_' . md5($client_ip);
$current_time = time();

if (!isset($_SESSION[$rate_limit_key])) {
    $_SESSION[$rate_limit_key] = ['count' => 0, 'first_attempt' => $current_time];
}

$message = '';
$messageType = '';
$registration_successful = false;
$redirect_url = '';

$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if (!$event_id) {
    die('رابط التسجيل غير صالح: معرف الحفل مفقود.');
}

$event = null;
$stmt_event = $mysqli->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
if ($stmt_event) {
    $stmt_event->bind_param("i", $event_id);
    $stmt_event->execute();
    $result_event = $stmt_event->get_result();
    if ($result_event && $result_event->num_rows > 0) {
        $event = $result_event->fetch_assoc();
    } else {
        die('الحفل المطلوب غير موجود.');
    }
    $stmt_event->close();
}

$show_phone = $event['registration_show_phone'] ?? 1;
$require_phone = $event['registration_require_phone'] ?? 1;
$show_guest_count = $event['registration_show_guest_count'] ?? 1;
$show_countdown = $event['registration_show_countdown'] ?? 1;
$show_location = $event['registration_show_location'] ?? 1;

if (isset($event['registration_mode']) && $event['registration_mode'] === 'simple') {
    $show_countdown = 0;
    $show_location = 0;
    $show_guest_count = 0;
    $require_phone = 0;
}

$event_date_formatted = parseEventDateTime($event['event_date_ar'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['switch_language'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Security token mismatch. Please try again.';
        $messageType = 'error';
    } else {
        if ($current_time - $_SESSION[$rate_limit_key]['first_attempt'] > 300) {
            $_SESSION[$rate_limit_key] = ['count' => 0, 'first_attempt' => $current_time];
        }

        if ($_SESSION[$rate_limit_key]['count'] >= 5) {
            $message = 'تم إرسال طلبات كثيرة. يرجى الانتظار قبل المحاولة مرة أخرى.';
            $messageType = 'error';
        } else {
            $_SESSION[$rate_limit_key]['count']++;

            $name_ar = trim($_POST['name_ar'] ?? '');
            $country_code = trim($_POST['country_code'] ?? '');

            $phone_number_raw = convertToLatinNumerals(trim($_POST['phone_number'] ?? ''));
            $guests_count_raw = convertToLatinNumerals(trim($_POST['guests_count'] ?? ''));

            if ($show_guest_count) {
                $guests_count = filter_var($guests_count_raw, FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1, 'max_range' => 20]
                ]) ?: 1;
            } else {
                $guests_count = 1;
            }
            $status = in_array($_POST['rsvp_status'] ?? '', ['confirmed', 'canceled']) ? $_POST['rsvp_status'] : 'canceled';

            $missing_fields = false;

            if (empty($name_ar)) {
                $missing_fields = true;
            }

            if ($show_phone && $require_phone && (empty($country_code) || empty($phone_number_raw))) {
                $missing_fields = true;
            }

            $name_parts = preg_split('/\s+/', $name_ar);
            if (count($name_parts) < 3) {
                $message = $t['invalid_name_format'];
                $messageType = 'error';
                $missing_fields = true;
            }

            if ($missing_fields) {
                if (empty($message)) {
                    $message = $t['fill_all_fields'];
                    $messageType = 'error';
                }
            } else {
                $is_valid = false;
                $error_message = '';
                $phone_number_normalized = '';

                if (!$show_phone) {
                    $is_valid = true;
                    $phone_number_normalized = '';
                } else if ($country_code === 'other') {
                    $phone_to_validate = $phone_number_raw;

                    if (substr($phone_to_validate, 0, 2) === '00') {
                        $phone_to_validate = substr($phone_to_validate, 2);
                    } elseif (substr($phone_to_validate, 0, 1) === '+') {
                        $phone_to_validate = substr($phone_to_validate, 1);
                    }

                    if (substr($phone_number_raw, 0, 1) === '0' && substr($phone_number_raw, 0, 2) !== '00') {
                        $error_message = $t['invalid_phone_international'];
                    } elseif (!ctype_digit($phone_to_validate) || strlen($phone_to_validate) < 10 || strlen($phone_to_validate) > 15) {
                        $error_message = $t['invalid_phone_general'];
                    } else {
                        $is_valid = true;
                        $phone_number_normalized = '+' . $phone_to_validate;
                    }
                } else {
                    $local_number = $phone_number_raw;

                    if (substr($local_number, 0, 1) === '0') {
                        $local_number = substr($local_number, 1);
                    }

                    if (!ctype_digit($local_number) || strlen($local_number) < 7 || strlen($local_number) > 10) {
                        $error_message = $t['invalid_phone_format'];
                    } else {
                        $is_valid = true;
                        $phone_number_normalized = $country_code . $local_number;
                    }
                }

                if (!$is_valid) {
                    $message = $error_message;
                    $messageType = 'error';
                } else {
                    $stmt_check = $mysqli->prepare("SELECT id, guest_id FROM guests WHERE phone_number = ? AND event_id = ? LIMIT 1");
                    if ($stmt_check) {
                        $stmt_check->bind_param("si", $phone_number_normalized, $event_id);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();

                        if ($result_check && $result_check->num_rows > 0 && $show_phone) {
                            $existing_guest = $result_check->fetch_assoc();

                            $update_query = "UPDATE guests SET name_ar = ?, guests_count = ?, status = ? WHERE id = ?";
                            $update_stmt = $mysqli->prepare($update_query);
                            $update_stmt->bind_param("sisi", $name_ar, $guests_count, $status, $existing_guest['id']);

                            if ($update_stmt->execute()) {
                                $registration_successful = true;
                                $message = ($status === 'confirmed') ? $t['registration_success_confirm'] : $t['registration_success_cancel'];
                                $messageType = 'success';
                                $redirect_url = "rsvp.php?id=" . $existing_guest['guest_id'];

                                if ($status === 'confirmed' && $show_phone && !empty($event['n8n_confirm_webhook'])) {
                                    $webhook_url = $event['n8n_confirm_webhook'];
                                    $n8n_payload = json_encode(['guest_id' => $existing_guest['guest_id'], 'phone_number' => $phone_number_normalized]);
                                    $ch = curl_init($webhook_url);
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $n8n_payload);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($n8n_payload)]);
                                    curl_exec($ch);
                                    curl_close($ch);
                                }
                            } else {
                                $message = $t['registration_error'];
                                $messageType = 'error';
                            }
                            $update_stmt->close();

                        } else {
                            $guest_id = substr(md5(uniqid($phone_number_normalized . microtime(), true)), 0, 4);

                            $stmt = $mysqli->prepare("INSERT INTO guests (event_id, guest_id, name_ar, phone_number, guests_count, status) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("isssis", $event_id, $guest_id, $name_ar, $phone_number_normalized, $guests_count, $status);

                                if ($stmt->execute()) {
                                    $registration_successful = true;
                                    if ($status === 'confirmed') {
                                        if (!$show_phone) {
                                            $message = $lang === 'ar' ? 'تم تأكيد حضورك بنجاح!' : 'Your attendance has been confirmed successfully!';
                                        } else {
                                            $message = $t['registration_success_confirm'];
                                        }
                                    } else {
                                        $message = $t['registration_success_cancel'];
                                    }
                                    $messageType = 'success';
                                    $redirect_url = "rsvp.php?id=" . $guest_id;

                                    if ($show_phone && $status === 'confirmed') {
                                        $webhook_url = $event['n8n_confirm_webhook'] ?? null;
                                        if ($webhook_url && filter_var($webhook_url, FILTER_VALIDATE_URL)) {
                                            $n8n_payload = json_encode(['guest_id' => $guest_id, 'phone_number' => $phone_number_normalized]);
                                            $ch = curl_init($webhook_url);
                                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                            curl_setopt($ch, CURLOPT_POSTFIELDS, $n8n_payload);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($n8n_payload)]);
                                            curl_exec($ch);
                                            curl_close($ch);
                                        }
                                    }
                                } else {
                                    $message = $t['registration_error'];
                                    $messageType = 'error';
                                }
                                $stmt->close();
                            }
                        }
                        $stmt_check->close();
                    }
                }
            }
        }
    }
}
?>
