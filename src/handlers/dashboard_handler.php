<?php
// src/handlers/dashboard_handler.php

// This file is included by public/dashboard.php and assumes bootstrap.php has been included.

// --- Language System ---
$texts = [
    'ar' => [
        'dashboard' => 'متابعة',
        'logout' => 'تسجيل الخروج',
        'back_to_events' => 'عودة للحفلات',
        'manage_guests' => 'إدارة الضيوف',
        'total_invited' => 'إجمالي المدعوين',
        'confirmed_attendance' => 'تأكيد الحضور',
        'checked_in_hall' => 'سجلوا الدخول الى القاعة',
        'declined_attendance' => 'إلغاء الحضور',
        'awaiting_response' => 'في انتظار الرد',
        'guest_list' => 'قائمة الضيوف',
        'export_report_csv' => 'تصدير تقرير (CSV)',
        'export_dashboard_pdf' => 'تصدير الداشبورد (PDF)',
        'refresh_data' => 'تحديث البيانات',
        'refreshing' => 'جاري التحديث...',
        'search_guest' => 'ابحث باسم الضيف...',
        'no_guests' => 'لا يوجد ضيوف',
        'error_fetching_data' => 'حدث خطأ في جلب البيانات',
        'table_number' => 'طاولة',
        'statistics_summary' => 'ملخص الإحصائيات',
        'guest_details' => 'تفاصيل الضيوف',
        'status_confirmed' => 'مؤكد',
        'status_declined' => 'معتذر',
        'status_pending' => 'في الانتظار',
        'status_checked_in' => 'حضر',
        'name' => 'الاسم',
        'phone' => 'الهاتف',
        'guests_count' => 'عدد الضيوف',
        'table' => 'الطاولة',
        'status' => 'الحالة',
        'checkin_status' => 'حالة الحضور',
        'people_singular' => 'شخص',
        'people_plural' => 'أشخاص',
        'presentation_mode' => 'وضع العرض',
        'exit_presentation' => 'إنهاء العرض',
        'fullscreen' => 'شاشة كاملة',
        'exit_fullscreen' => 'خروج من الشاشة الكاملة',
        'print' => 'طباعة',
        'refresh_success' => 'تم التحديث بنجاح',
        'export_excel_success' => 'تم تصدير Excel بنجاح',
        'new_guest_checked_in' => 'ضيف جديد سجل الحضور!',
        'new_guest_confirmed' => 'ضيف جديد أكد الحضور!',
    ],
    'en' => [
        'dashboard' => 'Dashboard',
        'logout' => 'Logout',
        'back_to_events' => 'Back to Events',
        'manage_guests' => 'Manage Guests',
        'total_invited' => 'Total Invited',
        'confirmed_attendance' => 'Confirmed Attendance',
        'checked_in_hall' => 'Checked into Hall',
        'declined_attendance' => 'Declined Attendance',
        'awaiting_response' => 'Awaiting Response',
        'guest_list' => 'Guest List',
        'export_report_csv' => 'Export Report (CSV)',
        'export_dashboard_pdf' => 'Export Dashboard (PDF)',
        'refresh_data' => 'Refresh Data',
        'refreshing' => 'Refreshing...',
        'search_guest' => 'Search by guest name...',
        'no_guests' => 'No guests',
        'error_fetching_data' => 'Error fetching data',
        'table_number' => 'Table',
        'statistics_summary' => 'Statistics Summary',
        'guest_details' => 'Guest Details',
        'status_confirmed' => 'Confirmed',
        'status_declined' => 'Declined',
        'status_pending' => 'Pending',
        'status_checked_in' => 'Checked In',
        'name' => 'Name',
        'phone' => 'Phone',
        'guests_count' => 'Guests Count',
        'table' => 'Table',
        'status' => 'Status',
        'checkin_status' => 'Check-in Status',
        'people_singular' => 'person',
        'people_plural' => 'people',
        'presentation_mode' => 'Presentation Mode',
        'exit_presentation' => 'Exit Presentation',
        'fullscreen' => 'Fullscreen',
        'exit_fullscreen' => 'Exit Fullscreen',
        'print' => 'Print',
        'refresh_success' => 'Refreshed successfully',
        'export_excel_success' => 'Excel exported successfully',
        'new_guest_checked_in' => 'New guest checked in!',
        'new_guest_confirmed' => 'New guest confirmed attendance!',
    ]
];
$t = array_merge($t, $texts[$lang]);

// --- Security Check & Permission Logic ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
$event_id = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if (!$event_id) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: events.php');
        exit;
    } else {
        die('Access Denied: Event ID is required.');
    }
}
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'viewer') {
    die('Access Denied: You do not have permission to view this event dashboard.');
}
if ($_SESSION['role'] === 'viewer' && $event_id != ($_SESSION['event_id_access'] ?? null)) {
    die('Access Denied: You do not have permission to view this event dashboard.');
}

// --- CSV Export Logic ---
if (isset($_GET['export_csv']) && $_GET['export_csv'] === 'true') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="guest_report_event_'.$event_id.'_'.date('Y-m-d').'.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    $csv_headers = [
        $t['name'],
        $t['phone'],
        $t['guests_count'],
        $t['table'],
        $t['status'],
        $t['checkin_status'],
        'وقت الحضور / Check-in Time'
    ];
    fputcsv($output, $csv_headers);

    $stmt = $mysqli->prepare("SELECT name_ar, phone_number, guests_count, table_number, status, checkin_status, checkin_time FROM guests WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $status_text = '';
            switch ($row['status']) {
                case 'confirmed':
                    $status_text = $t['status_confirmed'];
                    break;
                case 'canceled':
                    $status_text = $t['status_declined'];
                    break;
                default:
                    $status_text = $t['status_pending'];
                    break;
            }

            $checkin_text = ($row['checkin_status'] === 'checked_in') ? $t['status_checked_in'] : '-';

            fputcsv($output, [
                $row['name_ar'],
                $row['phone_number'],
                $row['guests_count'],
                $row['table_number'] ?: '-',
                $status_text,
                $checkin_text,
                $row['checkin_time'] ?: '-'
            ]);
        }
    }
    fclose($output);
    $stmt->close();
    $mysqli->close();
    exit;
}

// --- PDF Export Logic ---
if (isset($_GET['export_pdf']) && $_GET['export_pdf'] === 'true') {
    // Fetch data for PDF
    $stmt = $mysqli->prepare("SELECT name_ar, guests_count, table_number, status, checkin_status FROM guests WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $guests = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get event name
    $event_name = '';
    $stmt_event = $mysqli->prepare("SELECT event_name FROM events WHERE id = ?");
    $stmt_event->bind_param("i", $event_id);
    if ($stmt_event->execute()) {
        $result = $stmt_event->get_result();
        if ($row = $result->fetch_assoc()) {
            $event_name = $row['event_name'];
        }
    }
    $stmt_event->close();

    // Calculate statistics
    $total = count($guests);
    $confirmed = $canceled = $pending = $checkedIn = 0;

    foreach ($guests as $guest) {
        if ($guest['checkin_status'] === 'checked_in') {
            $checkedIn++;
        }
        if ($guest['status'] === 'confirmed') {
            $confirmed++;
        } elseif ($guest['status'] === 'canceled') {
            $canceled++;
        } else {
            $pending++;
        }
    }

    $html = generateDashboardHTML($event_name, $total, $confirmed, $checkedIn, $canceled, $pending, $guests, $t, $lang);

    echo $html;
    exit;
}

// --- API Endpoint for Dashboard Display ---
if (isset($_GET['fetch_data'])) {
    header('Content-Type: application/json');
    $stmt = $mysqli->prepare("SELECT name_ar, guests_count, table_number, status, checkin_status FROM guests WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $guests = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stats = [
        'total_guests' => count($guests),
        'total_people' => array_sum(array_column($guests, 'guests_count')),
        'confirmed_guests' => 0,
        'confirmed_people' => 0,
        'canceled_guests' => 0,
        'canceled_people' => 0,
        'pending_guests' => 0,
        'pending_people' => 0,
        'checkedIn_guests' => 0,
        'checkedIn_people' => 0
    ];

    foreach ($guests as $guest) {
        $guestCount = intval($guest['guests_count'] ?? 1);
        if ($guest['checkin_status'] === 'checked_in') {
            $stats['checkedIn_guests']++;
            $stats['checkedIn_people'] += $guestCount;
        }
        if ($guest['status'] === 'confirmed') {
            $stats['confirmed_guests']++;
            $stats['confirmed_people'] += $guestCount;
        } elseif ($guest['status'] === 'canceled') {
            $stats['canceled_guests']++;
            $stats['canceled_people'] += $guestCount;
        } else {
            $stats['pending_guests']++;
            $stats['pending_people'] += $guestCount;
        }
    }

    echo json_encode(['guests' => $guests, 'stats' => $stats]);
    $mysqli->close();
    exit;
}

// --- Fetch Event Name for Display ---
$event_name = $t['dashboard'];
$stmt_event = $mysqli->prepare("SELECT event_name FROM events WHERE id = ?");
$stmt_event->bind_param("i", $event_id);
if ($stmt_event->execute()) {
    $result = $stmt_event->get_result();
    if ($row = $result->fetch_assoc()) {
        $event_name = $row['event_name'];
    }
    $stmt_event->close();
}
?>
