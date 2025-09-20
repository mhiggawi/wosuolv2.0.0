<?php
session_start();
require_once 'db_config.php';

// Set language based on session or default to Arabic
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}
$lang = $_SESSION['lang'];

// Check for language change request
if (isset($_GET['lang'])) {
    $new_lang = $_GET['lang'] === 'en' ? 'en' : 'ar';
    $_SESSION['lang'] = $new_lang;
    header("Location: guests.php?event_id=" . ($_GET['event_id'] ?? ''));
    exit;
}

// Language translations array
$translations = [
    'ar' => [
        'page_title' => 'إدارة الضيوف',
        'app_name' => 'وصول',
        'event_management' => 'إدارة ضيوف',
        'dashboard' => 'لوحة التحكم',
        'logout' => 'تسجيل الخروج',
        'total_guests' => 'إجمالي الضيوف',
        'total_people' => 'شخص',
        'confirmed_guests' => 'مؤكد الحضور',
        'confirmed_people' => 'شخص',
        'checked_in_guests' => 'حضر فعلياً',
        'checked_in_people' => 'شخص',
        'pending_guests' => 'بانتظار التأكيد',
        'pending_people' => 'شخص',
        'search_placeholder' => 'اسم، هاتف، أو رقم الضيف...',
        'search_label' => 'البحث',
        'status_label' => 'الحالة',
        'table_label' => 'الطاولة',
        'all' => 'الكل',
        'pending' => 'بانتظار',
        'confirmed' => 'مؤكد',
        'canceled' => 'ملغي',
        'search_button' => 'بحث',
        'add_button' => 'إضافة',
        'export_button' => 'تصدير',
        'import_button' => 'استيراد',
        'refresh_button' => 'تحديث',
        'send_invitations_button' => 'إعداد الرسالة',
        'add_guest_title' => 'إضافة ضيف',
        'edit_guest_title' => 'تعديل بيانات الضيف',
        'send_invitations_title' => 'إعداد قالب الدعوة',
        'import_title' => 'استيراد ضيوف',
        'guest_name' => 'الاسم*',
        'phone_number' => 'الهاتف',
        'guests_count' => 'العدد',
        'table_number' => 'الطاولة',
        'location' => 'الموقع',
        'notes' => 'ملاحظات',
        'close' => 'إغلاق',
        'save' => 'حفظ',
        'add' => 'إضافة',
        'no_guests' => 'لا توجد ضيوف',
        'select_all' => 'تحديد الكل',
        'bulk_action' => 'اختر إجراء جماعي',
        'confirm_presence' => 'تأكيد الحضور',
        'cancel_presence' => 'إلغاء الحضور',
        'checkin' => 'تسجيل الدخول',
        'assign_table' => 'تعيين طاولة',
        'delete' => 'حذف',
        'enter_table_number' => 'أدخل رقم الطاولة',
        'execute' => 'تنفيذ',
        'guest_id_col' => 'الرقم',
        'name_col' => 'الاسم',
        'phone_col' => 'الهاتف',
        'count_col' => 'العدد',
        'table_col' => 'الطاولة',
        'location_col' => 'الموقع',
        'status_col' => 'الحالة',
        'checkin_col' => 'الدخول',
        'notes_col' => 'ملاحظات',
        'actions_col' => 'الإجراءات',
        'not_checked_in' => 'لم يحضر',
        'checked_in' => 'حضر',
        'edit_tooltip' => 'تعديل',
        'checkin_tooltip' => 'تسجيل دخول',
        'delete_tooltip' => 'حذف',
        'guests_plural' => 'ضيوف',
        'action_success' => 'تم تنفيذ الإجراء بنجاح على',
        'guest_singular' => 'ضيف',
        'action_failed' => 'فشل في تنفيذ الإجراء الجماعي',
        'add_guest_success' => 'تم إضافة الضيف بنجاح',
        'add_guest_failed' => 'فشل في إضافة الضيف',
        'update_guest_success' => 'تم تحديث بيانات الضيف بنجاح',
        'update_guest_failed' => 'فشل في تحديث بيانات الضيف',
        'no_selection_error' => 'يرجى تحديد ضيف واحد على الأقل',
        'no_table_error' => 'يرجى إدخال رقم الطاولة',
        'not_found_error' => 'الحدث غير موجود.',
        'no_event_specified' => 'لم يتم تحديد الحدث.',
        'access_denied' => 'ليس لديك صلاحية للوصول لهذا الحدث.',
        'confirm_delete' => 'حذف الضيف',
        'confirm_delete_text' => 'هل تريد حذف',
        'confirm_bulk_action' => 'هل أنت متأكد؟',
        'confirm_bulk_text' => 'سيتم تنفيذ الإجراء على الضيوف المحددين.',
        'confirm_checkin' => 'تسجيل الدخول',
        'confirm_checkin_text' => 'هل تريد تسجيل دخول هذا الضيف؟',
        'yes' => 'نعم',
        'cancel' => 'إلغاء',
        'execute_now' => 'نعم، نفذ',
        'no_data_to_export' => 'لا توجد بيانات لتصديرها.',
        'invalid_import_data' => 'لا توجد بيانات صالحة للاستيراد',
        'confirm_import' => 'تأكيد الاستيراد',
        'confirm_import_text' => 'سيتم استيراد',
        'importing' => 'جاري الاستيراد...',
        'import_success' => 'نجاح',
        'import_error' => 'خطأ',
        'network_error' => 'حدث خطأ في الشبكة',
        'update_success' => 'تم تحديث الحالة بنجاح',
        'update_failed' => 'فشل تحديث الحالة',
        'import_instructions' => 'تعليمات',
        'import_file_types' => 'يدعم Excel (.xlsx, .xls) و CSV (.csv)',
        'header_row_required' => 'الصف الأول يجب أن يحتوي على العناوين',
        'header' => 'العنوان',
        'description' => 'الوصف',
        'required' => 'مطلوب؟',
        'yes_required' => 'نعم',
        'no_required' => 'لا',
        'choose_file' => 'اختر ملف',
        'preview' => 'معاينة',
        'import_stats' => 'إحصائيات:',
        'total_rows' => 'إجمالي',
        'valid_rows' => 'صالح',
        'invalid_rows' => 'غير صالح',
        'download_template' => 'تحميل نموذج',
        'message_template_title' => 'قالب الرسالة',
        'message_template_instructions' => 'اكتب رسالتك هنا:',
        'send_invitation_col' => 'إرسال دعوة',
        'copy_tooltip' => 'نسخ الرسالة',
        'whatsapp_tooltip' => 'إرسال عبر واتساب',
        'invalid_phone' => 'رقم الهاتف غير صحيح',
        'variable_name' => 'اسم الضيف',
        'variable_count' => 'عدد الضيوف',
        'variable_link' => 'رابط الدعوة',
        'variable_table' => 'رقم الطاولة',
        'variable_location_link' => 'رابط الموقع',
        'variables' => 'المتغيرات',
        'default_message_ar' => "مرحبا (guest_name),\n\nنتشرف بدعوتكم لحضور حفلنا. نتطلع لرؤيتكم!\n\nعدد المدعوين: (guests_count)\nرقم الطاولة: (table_number)\n\nللتأكيد، يرجى زيارة الرابط التالي:\n(invitation_link)\n\nمكان الحفل: (event_location_link)"
    ],
    'en' => [
        'page_title' => 'Guest Management',
        'app_name' => 'Wosuol',
        'event_management' => 'Guest Management for',
        'dashboard' => 'Dashboard',
        'logout' => 'Logout',
        'total_guests' => 'Total Guests',
        'total_people' => 'people',
        'confirmed_guests' => 'Confirmed',
        'confirmed_people' => 'people',
        'checked_in_guests' => 'Checked-in',
        'checked_in_people' => 'people',
        'pending_guests' => 'Pending',
        'pending_people' => 'people',
        'search_placeholder' => 'Name, Phone, or Guest ID...',
        'search_label' => 'Search',
        'status_label' => 'Status',
        'table_label' => 'Table',
        'all' => 'All',
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'canceled' => 'Canceled',
        'search_button' => 'Search',
        'add_button' => 'Add',
        'export_button' => 'Export',
        'import_button' => 'Import',
        'refresh_button' => 'Refresh',
        'send_invitations_button' => 'Setup Message',
        'add_guest_title' => 'Add Guest',
        'edit_guest_title' => 'Edit Guest Details',
        'send_invitations_title' => 'Setup Invitation Template',
        'import_title' => 'Import Guests',
        'guest_name' => 'Name*',
        'phone_number' => 'Phone',
        'guests_count' => 'Count',
        'table_number' => 'Table',
        'location' => 'Location',
        'notes' => 'Notes',
        'close' => 'Close',
        'save' => 'Save',
        'add' => 'Add',
        'no_guests' => 'No guests found',
        'select_all' => 'Select All',
        'bulk_action' => 'Choose a bulk action',
        'confirm_presence' => 'Confirm Presence',
        'cancel_presence' => 'Cancel Presence',
        'checkin' => 'Check-in',
        'assign_table' => 'Assign Table',
        'delete' => 'Delete',
        'enter_table_number' => 'Enter table number',
        'execute' => 'Execute',
        'guest_id_col' => 'ID',
        'name_col' => 'Name',
        'phone_col' => 'Phone',
        'count_col' => 'Count',
        'table_col' => 'Table',
        'location_col' => 'Location',
        'status_col' => 'Status',
        'checkin_col' => 'Check-in',
        'notes_col' => 'Notes',
        'actions_col' => 'Actions',
        'not_checked_in' => 'Not Checked-in',
        'checked_in' => 'Checked-in',
        'edit_tooltip' => 'Edit',
        'checkin_tooltip' => 'Check-in',
        'delete_tooltip' => 'Delete',
        'guests_plural' => 'guests',
        'action_success' => 'Action successfully executed on',
        'guest_singular' => 'guest',
        'action_failed' => 'Failed to execute bulk action',
        'add_guest_success' => 'Guest added successfully',
        'add_guest_failed' => 'Failed to add guest',
        'update_guest_success' => 'Guest details updated successfully',
        'update_guest_failed' => 'Failed to update guest details',
        'delete_guest_success' => 'Guest deleted successfully',
        'delete_guest_failed' => 'Failed to delete guest',
        'no_selection_error' => 'Please select at least one guest',
        'no_table_error' => 'Please enter a table number',
        'not_found_error' => 'Event not found.',
        'no_event_specified' => 'No event specified.',
        'access_denied' => 'You do not have permission to access this event.',
        'confirm_delete' => 'Delete Guest',
        'confirm_delete_text' => 'Do you want to delete',
        'confirm_bulk_action' => 'Are you sure?',
        'confirm_bulk_text' => 'The action will be executed on the selected guests.',
        'confirm_checkin' => 'Check-in',
        'confirm_checkin_text' => 'Do you want to check in this guest?',
        'yes' => 'Yes',
        'cancel' => 'Cancel',
        'execute_now' => 'Yes, Execute',
        'no_data_to_export' => 'No guests to export.',
        'invalid_import_data' => 'No valid data to import',
        'confirm_import' => 'Confirm Import',
        'confirm_import_text' => 'You are about to import',
        'importing' => 'Importing...',
        'import_success' => 'Success',
        'import_error' => 'Error',
        'network_error' => 'A network error occurred',
        'update_success' => 'Status updated successfully',
        'update_failed' => 'Failed to update status',
        'import_instructions' => 'Instructions',
        'import_file_types' => 'Supports Excel (.xlsx, .xls) and CSV (.csv)',
        'header_row_required' => 'The first row must contain the headers',
        'header' => 'Header',
        'description' => 'Description',
        'required' => 'Required?',
        'yes_required' => 'Yes',
        'no_required' => 'No',
        'choose_file' => 'Choose file',
        'preview' => 'Preview',
        'import_stats' => 'Statistics:',
        'total_rows' => 'Total',
        'valid_rows' => 'Valid',
        'invalid_rows' => 'Invalid',
        'download_template' => 'Download Template',
        'message_template_title' => 'Message Template',
        'message_template_instructions' => 'Write your message here:',
        'send_invitation_col' => 'Send Invitation',
        'copy_tooltip' => 'Copy Message',
        'whatsapp_tooltip' => 'Send via WhatsApp',
        'invalid_phone' => 'Invalid phone number',
        'variable_name' => 'Guest Name',
        'variable_count' => 'Guests Count',
        'variable_link' => 'Invitation Link',
        'variable_table' => 'Table Number',
        'variable_location_link' => 'Event Location Link',
        'variables' => 'Variables',
        'default_message_en' => "Hello (guest_name),\n\nWe would be honored to have you at our event. We look forward to seeing you!\n\nGuests attending: (guests_count)\nTable Number: (table_number)\n\nTo RSVP, please click the link below:\n(invitation_link)\n\nEvent Location: (event_location_link)"
    ]
];

// Helper function to get translation
function translate($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $key; // Fallback to key if not found
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

// Handle form submissions
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
                default: // confirm, cancel, checkin
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

// Fetch guests data
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

// Fetch statistics
$stats_query = "SELECT COUNT(*) as total_guests, SUM(guests_count) as total_people, SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_guests, SUM(CASE WHEN status = 'confirmed' THEN guests_count ELSE 0 END) as confirmed_people, SUM(CASE WHEN checkin_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in_guests, SUM(CASE WHEN checkin_status = 'checked_in' THEN guests_count ELSE 0 END) as checked_in_people, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_guests, SUM(CASE WHEN status = 'pending' THEN guests_count ELSE 0 END) as pending_people FROM guests WHERE event_id = ?";
$stats_stmt = $mysqli->prepare($stats_query);
$stats_stmt->bind_param("i", $event_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
$stats_stmt->close();

// Fetch unique tables
$tables_query = "SELECT DISTINCT table_number FROM guests WHERE event_id = ? AND table_number IS NOT NULL AND table_number != '' ORDER BY table_number";
$tables_stmt = $mysqli->prepare($tables_query);
$tables_stmt->bind_param("i", $event_id);
$tables_stmt->execute();
$tables = $tables_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$tables_stmt->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('page_title'); ?> - <?php echo htmlspecialchars($event['event_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Cairo', sans-serif;
        background: white;
        padding: 10px;
        color: #2d4a22;
        -webkit-text-size-adjust: 100%;
    }
    .container-fluid {
        max-width: 1200px;
        margin: 15px auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        padding: 25px;
    }
    .wosuol-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #2d4a22; font-weight: bold; margin-bottom: 20px; }
    .wosuol-icon { width: 35px; height: 35px; background: rgba(45, 74, 34, 0.9); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; }
    .wosuol-text { font-size: 1.25rem; font-weight: 700; color: #2d4a22; }
    .page-header, .stats-row, .filters-section, .card {
        border-radius: 20px;
        border: 1px solid rgba(45, 74, 34, 0.15);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 15px rgba(45, 74, 34, 0.05);
        transition: all 0.3s;
    }
    .page-header:hover, .stats-row:hover, .filters-section:hover, .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 74, 34, 0.1);
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 15px 20px;
        border-radius: 50px;
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
    .page-header:hover::before { left: 100%; }
    .page-header:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(45, 74, 34, 0.2);
        border-color: rgba(45, 74, 34, 0.5);
        color: #1a2f15;
        background: rgba(255, 255, 255, 0.95);
    }
    .stats-row {
        padding: 10px;
        margin-bottom: 1rem;
        border-radius: 50px;
        border: 2px solid rgba(45, 74, 34, 0.3);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
    }
    .stat-item { text-align: center; padding: 0.5rem; }
    .stat-number { font-size: 1.75rem; font-weight: bold; }
    .stat-people { font-size: 1rem; font-weight: 600; color: #007bff; }
    .stat-label { font-size: 0.8rem; opacity: 0.8; }
    .filters-section { padding: 15px 20px; margin-bottom: 1rem; }
    .card { border-radius: 20px; overflow: hidden; }
    .card-header { background: rgba(255, 255, 255, 0.9); border-bottom: 1px solid rgba(45, 74, 34, 0.15); padding: 1rem; }
    .btn {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        border-radius: 25px;
        padding: 8px 16px;
        border: 2px solid rgba(45, 74, 34, 0.3);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        color: #2d4a22;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
        text-decoration: none;
        display: inline-block;
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
    .btn:hover::before { left: 100%; }
    .btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 20px rgba(45, 74, 34, 0.2);
        border-color: rgba(45, 74, 34, 0.5);
        color: #1a2f15;
        background: rgba(255, 255, 255, 0.95);
    }
    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid rgba(45, 74, 34, 0.3);
        padding: 0.375rem 0.75rem;
    }
    .table-responsive { -webkit-overflow-scrolling: touch; }
    .table th {
        background: rgba(45, 74, 34, 0.08);
        position: sticky; top: 0; z-index: 10;
        white-space: normal; vertical-align: middle;
        font-size: 0.9rem;
    }
    .table td {
        vertical-align: middle;
        white-space: normal;
        overflow-wrap: break-word;
        word-wrap: break-word;
        font-size: 0.85rem;
    }
    .people-count-display { font-size: 0.7rem; color: #007bff; font-weight: 600; padding: 1px 5px; background: rgba(0, 123, 255, 0.1); border-radius: 6px; display: inline-block; }
    .action-buttons { display: flex; gap: 3px; justify-content: flex-start; flex-wrap: wrap; }
    .action-btn { width: 26px; height: 26px; border-radius: 5px; border: 1px solid; background: rgba(255, 255, 255, 0.9); display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; font-size: 0.7rem; }
    .action-btn:hover { transform: translateY(-1px) scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .edit-btn { border-color: #007bff; color: #007bff; } .edit-btn:hover { background: #007bff; color: white; }
    .checkin-btn { border-color: #28a745; color: #28a745; } .checkin-btn:hover { background: #28a745; color: white; }
    .delete-btn { border-color: #dc3545; color: #dc3545; } .delete-btn:hover { background: #dc3545; color: white; }
    .bulk-actions { background: rgba(45, 74, 34, 0.05); padding: 0.75rem; border-radius: 15px; margin-bottom: 1rem; border: 1px solid rgba(45, 74, 34, 0.1); }
    .modal-content { border-radius: 15px; border: 1px solid rgba(45, 74, 34, 0.2); }
    .modal-header { background: rgba(45, 74, 34, 0.05); border-bottom: 1px solid rgba(45, 74, 34, 0.15); }
    .alert { border-radius: 12px; }
    .guest-id-link { text-decoration: none; }
    .lang-switcher { font-weight: bold; text-decoration: none; }
    .header-buttons { display: flex; gap: 12px; align-items: center; }

    /* Responsive adjustments for Tablets and Phones */
    @media (max-width: 991px) {
        body { padding: 5px; }
        .container-fluid { padding: 10px; margin: 5px auto; }
        .page-header { flex-direction: column; gap: 10px; padding: 10px 15px; }
        .page-header h1 { font-size: 1.1rem; }
        .filters-section, .stats-row { padding: 10px; margin-bottom: 0.75rem; }
        .table { font-size: 0.85rem; }
        .table td, .table th { padding: 0.4rem 0.3rem; }
        .status-select { min-width: 95px; }
    }

    /* Specific adjustments for small phones */
    @media (max-width: 480px) {
        .container-fluid { border-radius: 10px; }
        .stat-number { font-size: 1.4rem; }
        .stat-people { font-size: 0.8rem; }
        .header-buttons .btn { font-size: 0.75rem; padding: 6px 10px; }
        .bulk-actions .row { flex-direction: column; gap: 8px; }
    }
    .variables-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        padding: 10px;
        background-color: #f0f4f0;
        border-radius: 8px;
        border: 1px dashed #d0d7d3;
    }
    .variable-tag {
        background-color: #e6f0e6;
        color: #2d4a22;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.85rem;
        cursor: pointer;
        border: 1px solid #c0d0c0;
        transition: all 0.2s ease;
    }
    .variable-tag:hover {
        background-color: #d1e7d1;
        transform: translateY(-1px);
    }
    </style>
</head>
<body dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<div class="container-fluid">
    <div class="wosuol-logo">
        <div class="wosuol-icon"><i class="fas fa-users"></i></div>
        <div class="wosuol-text"><?php echo translate('app_name'); ?></div>
    </div>

    <div class="page-header">
        <h1 class="h3 mb-0"><?php echo translate('event_management'); ?>: <?php echo htmlspecialchars($event['event_name']); ?></h1>
        <div class="header-buttons">
            <a href="?event_id=<?php echo $event_id; ?>&lang=<?php echo $lang === 'ar' ? 'en' : 'ar'; ?>" class="btn">
                <i class="fas fa-language"></i>
                <?php echo $lang === 'ar' ? 'English' : 'العربية'; ?>
            </a>
            <?php if ($user_role === 'admin'): ?>
                <a href="events.php" class="btn btn-outline-secondary"><i class="fas fa-list-ul me-1"></i>العودة للحفلات</a>
            <?php endif; ?>
            <?php if ($user_role === 'admin' || $user_role === 'viewer'): ?>
                <a href="dashboard.php?event_id=<?php echo $event_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-chart-bar me-1"></i><?php echo translate('dashboard'); ?></a>
            <?php endif; ?>
            <?php if ($user_role === 'viewer'): ?>
                 <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-1"></i><?php echo translate('logout'); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="stats-row">
        <div class="row">
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-primary"><?php echo $stats['total_guests']; ?></div><div class="stat-people"><?php echo $stats['total_people']; ?> <?php echo translate('total_people'); ?></div><div class="stat-label"><?php echo translate('total_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-success"><?php echo $stats['confirmed_guests']; ?></div><div class="stat-people"><?php echo $stats['confirmed_people']; ?> <?php echo translate('confirmed_people'); ?></div><div class="stat-label"><?php echo translate('confirmed_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-info"><?php echo $stats['checked_in_guests']; ?></div><div class="stat-people"><?php echo $stats['checked_in_people']; ?> <?php echo translate('checked_in_people'); ?></div><div class="stat-label"><?php echo translate('checked_in_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-warning"><?php echo $stats['pending_guests']; ?></div><div class="stat-people"><?php echo $stats['pending_people']; ?> <?php echo translate('pending_people'); ?></div><div class="stat-label"><?php echo translate('pending_guests'); ?></div></div></div>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <div class="col-md-4"><label class="form-label"><?php echo translate('search_label'); ?></label><input type="text" class="form-control" name="search" placeholder="<?php echo translate('search_placeholder'); ?>" value="<?php echo htmlspecialchars($search); ?>"></div>
            <div class="col-md-3"><label class="form-label"><?php echo translate('status_label'); ?></label><select name="status" class="form-select"><option value=""><?php echo translate('all'); ?></option><option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>><?php echo translate('pending'); ?></option><option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>><?php echo translate('confirmed'); ?></option><option value="canceled" <?php echo $status_filter === 'canceled' ? 'selected' : ''; ?>><?php echo translate('canceled'); ?></option></select></div>
            <div class="col-md-3"><label class="form-label"><?php echo translate('table_label'); ?></label><select name="table" class="form-select"><option value=""><?php echo translate('all'); ?></option><?php foreach ($tables as $table): ?><option value="<?php echo htmlspecialchars($table['table_number']); ?>" <?php echo $table_filter === $table['table_number'] ? 'selected' : ''; ?>><?php echo translate('table_label'); ?> <?php echo htmlspecialchars($table['table_number']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><div class="d-grid"><button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i><?php echo translate('search_button'); ?></button></div></div>
        </form>
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2" role="group">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGuestModal"><i class="fas fa-user-plus me-1"></i><?php echo translate('add_button'); ?></button>
                    <button type="button" class="btn btn-info" onclick="exportGuests()"><i class="fas fa-download me-1"></i><?php echo translate('export_button'); ?></button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-1"></i><?php echo translate('import_button'); ?></button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendInvitationsModal"><i class="fas fa-paper-plane me-1"></i><?php echo translate('send_invitations_button'); ?></button>
                    <a href="?event_id=<?php echo $event_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-refresh me-1"></i><?php echo translate('refresh_button'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($success_message)): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($success_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if (!empty($error_message)): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($error_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <form id="bulkForm" method="POST">
        <div class="card">
            <div class="card-header">
                <div class="bulk-actions"><div class="row align-items-center"><div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="selectAll"><label class="form-check-label fw-bold" for="selectAll"><?php echo translate('select_all'); ?></label></div></div><div class="col-md-9"><div class="row g-2 align-items-center"><div class="col-md-5"><select name="bulk_action" id="bulkActionSelect" class="form-select" required><option value=""><?php echo translate('bulk_action'); ?></option><option value="confirm"><?php echo translate('confirm_presence'); ?></option><option value="cancel"><?php echo translate('cancel_presence'); ?></option><option value="checkin"><?php echo translate('checkin'); ?></option><option value="assign_table"><?php echo translate('assign_table'); ?></option><option value="delete"><?php echo translate('delete'); ?></option></select></div><div class="col-md-5"><div id="bulkTableInputContainer" style="display: none;"><input type="number" name="bulk_table_number" class="form-control" placeholder="<?php echo translate('enter_table_number'); ?>" min="1"></div></div><div class="col-md-2"><button type="submit" class="btn btn-warning w-100"><i class="fas fa-bolt me-1"></i><?php echo translate('execute'); ?></button></div></div></div></div></div>
            </div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th><input type="checkbox" id="selectAllHeader"></th><th><?php echo translate('guest_id_col'); ?></th><th><?php echo translate('name_col'); ?></th><th><?php echo translate('phone_col'); ?></th><th><?php echo translate('count_col'); ?></th><th><?php echo translate('table_col'); ?></th><th><?php echo translate('location_col'); ?></th><th><?php echo translate('status_col'); ?></th><th><?php echo translate('checkin_col'); ?></th><th><?php echo translate('notes_col'); ?></th><th><?php echo translate('actions_col'); ?></th><th><?php echo translate('send_invitation_col'); ?></th></tr></thead><tbody>
                <?php if (empty($guests)): ?>
                    <tr><td colspan="12" class="text-center py-5 text-muted"><i class="fas fa-users-slash fa-3x mb-3"></i><h5><?php echo translate('no_guests'); ?></h5></td></tr>
                <?php else: ?>
                    <?php foreach ($guests as $guest): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_guests[]" value="<?php echo htmlspecialchars($guest['guest_id']); ?>" class="guest-checkbox"></td>
                            <td>
                                <a href="rsvp.php?id=<?php echo htmlspecialchars($guest['guest_id']); ?>" target="_blank" class="guest-id-link" title="عرض الدعوة">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($guest['guest_id']); ?></span>
                                </a>
                            </td>
                            <td><strong><?php echo htmlspecialchars($guest['name_ar']); ?></strong><div class="people-count-display"><?php echo $guest['guests_count']; ?> <?php echo translate('total_people'); ?></div></td>
                            <td><?php echo htmlspecialchars($guest['phone_number'] ?: '-'); ?></td>
                            <td><span class="badge bg-info fs-6"><?php echo $guest['guests_count']; ?></span></td>
                            <td><?php echo !empty($guest['table_number']) ? '<span class="badge bg-primary">' . translate('table_label') . ' ' . htmlspecialchars($guest['table_number']) . '</span>' : '-'; ?></td>
                            <td><?php echo htmlspecialchars($guest['assigned_location'] ?: '-'); ?></td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-guest-id="<?php echo $guest['guest_id']; ?>" data-current-status="<?php echo $guest['status']; ?>">
                                    <option value="pending" <?php echo $guest['status'] === 'pending' ? 'selected' : ''; ?>><?php echo translate('pending'); ?></option>
                                    <option value="confirmed" <?php echo $guest['status'] === 'confirmed' ? 'selected' : ''; ?>><?php echo translate('confirmed'); ?></option>
                                    <option value="canceled" <?php echo $guest['status'] === 'canceled' ? 'selected' : ''; ?>><?php echo translate('canceled'); ?></option>
                                </select>
                            </td>
                            <td><?php echo $guest['checkin_status'] === 'checked_in' ? '<span class="badge bg-success">' . translate('checked_in') . '</span>' : '<span class="badge bg-secondary">' . translate('not_checked_in') . '</span>'; ?></td>
                            <td><?php if (!empty($guest['notes'])): ?><i class="fas fa-comment text-warning" title="<?php echo htmlspecialchars($guest['notes']); ?>" data-bs-toggle="tooltip"></i><?php endif; ?></td>
                            <td><div class="action-buttons"><button type="button" class="action-btn edit-btn" onclick='editGuest(<?php echo htmlspecialchars(json_encode($guest, JSON_UNESCAPED_UNICODE)); ?>)' title="<?php echo translate('edit_tooltip'); ?>"><i class="fas fa-edit"></i></button><?php if ($guest['checkin_status'] !== 'checked_in'): ?><button type="button" class="action-btn checkin-btn" onclick="quickCheckin('<?php echo $guest['guest_id']; ?>')" title="<?php echo translate('checkin_tooltip'); ?>"><i class="fas fa-check"></i></button><?php endif; ?><button type="button" class="action-btn delete-btn" onclick="deleteGuest('<?php echo $guest['guest_id']; ?>', '<?php echo htmlspecialchars($guest['name_ar']); ?>')" title="<?php echo translate('delete_tooltip'); ?>"><i class="fas fa-trash"></i></button></div></td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn btn-whatsapp" onclick="sendWhatsApp('<?php echo htmlspecialchars($guest['phone_number']); ?>', '<?php echo htmlspecialchars($guest['name_ar'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($guest['guests_count']); ?>', '<?php echo htmlspecialchars($guest['table_number']); ?>', '<?php echo htmlspecialchars($guest['guest_id']); ?>')" title="<?php echo translate('whatsapp_tooltip'); ?>">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button type="button" class="action-btn btn-copy" onclick="copyInvitation('<?php echo htmlspecialchars($guest['name_ar'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($guest['guests_count']); ?>', '<?php echo htmlspecialchars($guest['table_number']); ?>', '<?php echo htmlspecialchars($guest['guest_id']); ?>')" title="<?php echo translate('copy_tooltip'); ?>">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody></table></div></div>
        </div>
    </form>
</div>

<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title"><?php echo translate('add_guest_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label"><?php echo translate('guest_name'); ?></label><input type="text" class="form-control" name="name_ar" required></div>
                        <div class="col-md-6"><label class="form-label"><?php echo translate('phone_number'); ?></label><input type="tel" class="form-control" name="phone_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('guests_count'); ?></label><input type="number" class="form-control" name="guests_count" value="1" min="1"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('table_label'); ?></label><input type="text" class="form-control" name="table_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('status_col'); ?></label><select class="form-select" name="status"><option value="pending"><?php echo translate('pending'); ?></option><option value="confirmed"><?php echo translate('confirmed'); ?></option><option value="canceled"><?php echo translate('canceled'); ?></option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('location'); ?></label><select class="form-select" name="assigned_location"><option value=""><?php echo translate('all'); ?></option><option value="أهل العروس">أهل العروس</option><option value="أهل العريس">أهل العريس</option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('notes'); ?></label><textarea class="form-control" name="notes" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button><button type="submit" name="add_guest" class="btn btn-primary"><?php echo translate('add'); ?></button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title"><?php echo translate('edit_guest_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="guest_id" id="edit_guest_id">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label"><?php echo translate('guest_name'); ?></label><input type="text" class="form-control" name="name_ar" id="edit_name_ar" required></div>
                        <div class="col-md-6"><label class="form-label"><?php echo translate('phone_number'); ?></label><input type="tel" class="form-control" name="phone_number" id="edit_phone_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('guests_count'); ?></label><input type="number" class="form-control" name="guests_count" id="edit_guests_count" min="1"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('table_number'); ?></label><input type="text" class="form-control" name="table_number" id="edit_table_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('status_col'); ?></label><select class="form-select" name="status" id="edit_status"><option value="pending"><?php echo translate('pending'); ?></option><option value="confirmed"><?php echo translate('confirmed'); ?></option><option value="canceled"><?php echo translate('canceled'); ?></option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('location'); ?></label><select class="form-select" name="assigned_location" id="edit_assigned_location"><option value=""><?php echo translate('all'); ?></option><option value="أهل العروس">أهل العروس</option><option value="أهل العريس">أهل العريس</option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('notes'); ?></label><textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button><button type="submit" name="update_guest" class="btn btn-primary"><?php echo translate('save'); ?></button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><?php echo translate('import_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row"><div class="col-md-6"><div class="import-instructions"><h6><?php echo translate('import_instructions'); ?></h6><ul><li><?php echo translate('import_file_types'); ?></li><li><?php echo translate('header_row_required'); ?></li></ul><table class="table table-sm"><thead><tr><th><?php echo translate('header'); ?></th><th><?php echo translate('description'); ?></th><th><?php echo translate('required'); ?></th></tr></thead><tbody><tr><td><code>name_ar</code></td><td><?php echo translate('guest_name'); ?></td><td><?php echo translate('yes_required'); ?></td></tr><tr><td><code>phone_number</code></td><td><?php echo translate('phone_number'); ?></td><td><?php echo translate('no_required'); ?></td></tr><tr><td><code>guests_count</code></td><td><?php echo translate('guests_count'); ?></td><td><?php echo translate('no_required'); ?></td></tr></tbody></table></div><form id="importForm" class="mt-3"><div class="mb-3"><label for="importFile" class="form-label"><?php echo translate('choose_file'); ?></label><input type="file" class="form-control" id="importFile" accept=".xlsx,.xls,.csv" required></div></form></div><div class="col-md-6"><label class="form-label"><?php echo translate('preview'); ?></label><div id="previewContainer" class="border rounded p-3 bg-light" style="min-height: 300px;"></div><div id="importStats" class="d-none mt-2"><h6><?php echo translate('import_stats'); ?></h6><p><?php echo translate('total_rows'); ?>: <span id="totalRows">0</span>, <?php echo translate('valid_rows'); ?>: <span id="validRows">0</span>, <?php echo translate('invalid_rows'); ?>: <span id="invalidRows">0</span></p></div></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button>
                <button type="button" id="downloadTemplate" class="btn btn-outline-primary"><?php echo translate('download_template'); ?></button>
                <button type="button" id="importBtn" class="btn btn-success" disabled><?php echo translate('import_button'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sendInvitationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo translate('send_invitations_title'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6><?php echo translate('variables'); ?></h6>
                    <div class="variables-container">
                        <span class="variable-tag" data-variable="(guest_name)"><?php echo translate('variable_name'); ?></span>
                        <span class="variable-tag" data-variable="(guests_count)"><?php echo translate('variable_count'); ?></span>
                        <span class="variable-tag" data-variable="(table_number)"><?php echo translate('variable_table'); ?></span>
                        <span class="variable-tag" data-variable="(invitation_link)"><?php echo translate('variable_link'); ?></span>
                        <span class="variable-tag" data-variable="(event_location_link)"><?php echo translate('variable_location_link'); ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    <h6><?php echo translate('message_template_instructions'); ?></h6>
                    <textarea id="messageTemplate" class="form-control" rows="8" placeholder="مرحبا (guest_name)..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button>
                <button type="button" class="btn btn-primary" id="saveTemplateBtn"><?php echo translate('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

<script>
    const eventId = "<?php echo $event_id; ?>";
    const language = "<?php echo $lang; ?>";
    const translations = <?php echo json_encode($translations, JSON_UNESCAPED_UNICODE); ?>[language];

    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Tooltip(document.body, { selector: "[data-bs-toggle='tooltip']" });

        const bulkActionSelect = document.getElementById('bulkActionSelect');
        if(bulkActionSelect) {
            bulkActionSelect.addEventListener('change', function() {
                const tableInputContainer = document.getElementById('bulkTableInputContainer');
                tableInputContainer.style.display = (this.value === 'assign_table') ? 'block' : 'none';
            });
        }

        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        const checkboxes = document.querySelectorAll('.guest-checkbox');

        function syncCheckboxes(master) {
            const isChecked = master.checked;
            checkboxes.forEach(cb => cb.checked = isChecked);
            if (selectAll) selectAll.checked = isChecked;
            if (selectAllHeader) selectAllHeader.checked = isChecked;
        }

        if(selectAll) selectAll.addEventListener('change', () => syncCheckboxes(selectAll));
        if(selectAllHeader) selectAllHeader.addEventListener('change', () => syncCheckboxes(selectAllHeader));

        const bulkForm = document.getElementById('bulkForm');
        if(bulkForm){
            bulkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = this.querySelector('#bulkActionSelect').value;
                if (!action) {
                    Swal.fire(translations.import_error, translations.bulk_action, 'warning');
                    return;
                }
                if (this.querySelectorAll('.guest-checkbox:checked').length === 0) {
                    Swal.fire(translations.import_error, translations.no_selection_error, 'warning');
                    return;
                }
                if (action === 'assign_table' && !this.querySelector('[name="bulk_table_number"]').value) {
                    Swal.fire(translations.import_error, translations.no_table_error, 'warning');
                    return;
                }
                Swal.fire({
                    title: translations.confirm_bulk_action,
                    text: translations.confirm_bulk_text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: translations.execute_now,
                    cancelButtonText: translations.cancel
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            });
        }

        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const guestId = this.dataset.guestId;
                const newStatus = this.value;
                const selectElement = this;

                selectElement.disabled = true;

                const formData = new FormData();
                formData.append('quick_status_update', '1');
                formData.append('guest_id', guestId);
                formData.append('new_status', newStatus);

                fetch('guests_api.php?event_id=' + eventId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = selectElement.closest('tr');
                        row.style.transition = 'background-color 0.5s';
                        row.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { row.style.backgroundColor = ''; }, 1500);
                        selectElement.dataset.currentStatus = newStatus;
                    } else {
                        Swal.fire(translations.import_error, data.message || translations.update_failed, 'error');
                        selectElement.value = selectElement.dataset.currentStatus;
                    }
                })
                .catch(() => Swal.fire(translations.import_error, translations.network_error, 'error'))
                .finally(() => { selectElement.disabled = false; });
            });
        });
    });

    function editGuest(guest) {
        const modal = new bootstrap.Modal(document.getElementById('editGuestModal'));
        document.getElementById('edit_guest_id').value = guest.guest_id;
        document.getElementById('edit_name_ar').value = guest.name_ar;
        document.getElementById('edit_phone_number').value = guest.phone_number || '';
        document.getElementById('edit_guests_count').value = guest.guests_count;
        document.getElementById('edit_table_number').value = guest.table_number || '';
        document.getElementById('edit_status').value = guest.status;
        document.getElementById('edit_assigned_location').value = guest.assigned_location || '';
        document.getElementById('edit_notes').value = guest.notes || '';
        modal.show();
    }

    function quickCheckin(guestId) {
        Swal.fire({
            title: translations.confirm_checkin,
            text: translations.confirm_checkin_text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: translations.yes,
            cancelButtonText: translations.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('bulkForm');
                form.querySelector('#bulkActionSelect').value = 'checkin';
                form.querySelectorAll('.guest-checkbox').forEach(cb => cb.checked = false);
                form.querySelector(`.guest-checkbox[value="${guestId}"]`).checked = true;
                form.submit();
            }
        });
    }

    function deleteGuest(guestId, guestName) {
         Swal.fire({
            title: translations.confirm_delete,
            text: `${translations.confirm_delete_text} "${guestName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: translations.yes,
            cancelButtonText: translations.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="delete_guest" value="1"><input type="hidden" name="guest_id" value="${guestId}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function exportGuests() {
        const guestsData = <?php echo json_encode($guests, JSON_UNESCAPED_UNICODE); ?>;
        if (!guestsData.length) {
            Swal.fire(translations.no_data_to_export, '', 'info');
            return;
        }
        const exportData = guestsData.map(g => ({
            [translations.name_col]: g.name_ar,
            [translations.phone_col]: g.phone_number,
            [translations.count_col]: g.guests_count,
            [translations.table_col]: g.table_number,
            [translations.status_col]: g.status
        }));
        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, translations.guests_plural);
        XLSX.writeFile(wb, `guests_<?php echo str_replace(' ', '_', $event['event_name']); ?>.xlsx`);
    }

    let previewData = [];
    const importFileInput = document.getElementById('importFile');
    if(importFileInput) {
        importFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const ws = workbook.Sheets[workbook.SheetNames[0]];
                previewData = XLSX.utils.sheet_to_json(ws, {header: 1});
                displayPreview(previewData);
                document.getElementById('importBtn').disabled = false;
            };
            reader.readAsArrayBuffer(file);
        });
    }

    function displayPreview(data) {
        const container = document.getElementById('previewContainer');
        const headers = data[0] || [];
        const rows = data.slice(1);
        const requiredHeaders = ['name_ar', 'اسم الضيف'];
        const hasRequiredHeader = headers.some(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')));

        let validRows = 0;
        if (hasRequiredHeader) {
            validRows = rows.filter(row => row[headers.findIndex(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')))] && row[headers.findIndex(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')))].toString().trim() !== '').length;
        }

        if (document.getElementById('totalRows')) {
            document.getElementById('totalRows').textContent = rows.length;
            document.getElementById('validRows').textContent = validRows;
            document.getElementById('invalidRows').textContent = rows.length - validRows;
            document.getElementById('importStats').classList.remove('d-none');
        }

        let html = `<div class="table-responsive"><table class="table table-sm"><thead><tr>`;
        headers.forEach(header => { html += `<th>${header}</th>`; });
        html += '</tr></thead><tbody>';
        rows.slice(0, 5).forEach(row => {
            html += '<tr>';
            row.forEach(cell => { html += `<td>${cell || ''}</td>`; });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    const importBtn = document.getElementById('importBtn');
    if(importBtn){
        importBtn.addEventListener('click', function() {
            if (previewData.length < 2) {
                Swal.fire(translations.import_error, translations.invalid_import_data, 'warning');
                return;
            }
            Swal.fire({
                title: translations.confirm_import,
                text: `${translations.confirm_import_text} ${previewData.length - 1} ${translations.guests_plural}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: translations.yes,
                cancelButtonText: translations.cancel
            }).then((result) => {
                if (result.isConfirmed) importGuests();
            });
        });
    }

    function importGuests() {
        Swal.fire({ title: translations.importing, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        const headers = previewData[0].map(h => h.toString().trim());
        const rows = previewData.slice(1);
        const guestsToImport = rows.map(row => {
            let guest = {};
            const fieldMap = { 'name_ar': 'name_ar', 'اسم الضيف': 'name_ar', 'phone_number': 'phone_number', 'رقم الهاتف': 'phone_number', 'guests_count': 'guests_count', 'عدد الأشخاص': 'guests_count', 'table_number': 'table_number', 'رقم الطاولة': 'رقم الطاولة', 'assigned_location': 'assigned_location', 'الموقع': 'الموقع', 'notes': 'notes', 'ملاحظات': 'ملاحظات' };
            headers.forEach((header, i) => {
                const key = fieldMap[header];
                if (key) guest[key] = row[i];
            });
            return guest;
        });

        fetch('import_guests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event_id: eventId, guests: guestsToImport })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire(translations.import_success, data.message, 'success').then(() => location.reload());
            } else {
                throw new Error(data.message);
            }
        })
        .catch(err => Swal.fire(translations.import_error, err.message, 'error'));
    }

    const downloadTemplateBtn = document.getElementById('downloadTemplate');
    if(downloadTemplateBtn){
        downloadTemplateBtn.addEventListener('click', function() {
            const templateData = [['name_ar', 'phone_number', 'guests_count', 'table_number', 'assigned_location', 'notes']];
            const ws = XLSX.utils.aoa_to_sheet(templateData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, translations.download_template);
            XLSX.writeFile(wb, 'guest_import_template.xlsx');
        });
    }

    const messageTemplate = document.getElementById('messageTemplate');
    const saveTemplateBtn = document.getElementById('saveTemplateBtn');
    const sendInvitationsModal = document.getElementById('sendInvitationsModal');

    // Load saved template from DB on modal open
    if (sendInvitationsModal) {
        sendInvitationsModal.addEventListener('show.bs.modal', function () {
            fetch(`guests_api.php?event_id=${eventId}&action=get_template`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.template) {
                        messageTemplate.value = data.template;
                    } else {
                        messageTemplate.value = translations[`default_message_${language}`];
                    }
                });
        });
    }

    // Save template to DB
    if (saveTemplateBtn) {
        saveTemplateBtn.addEventListener('click', function() {
            const templateContent = messageTemplate.value;
            const formData = new FormData();
            formData.append('event_id', eventId);
            formData.append('action', 'save_template');
            formData.append('template_content', templateContent);
            formData.append('language', language);

            fetch(`guests_api.php?event_id=${eventId}`, {
            method: 'POST',
            body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => { throw new Error(error.message || 'Network response was not ok'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ',
                            text: 'تم حفظ قالب الرسالة بنجاح.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        bootstrap.Modal.getInstance(sendInvitationsModal).hide();
                    } else {
                        Swal.fire('خطأ', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('خطأ', `حدث خطأ: ${error.message}`, 'error');
                });
        });
    }

    document.querySelectorAll('.variable-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const variable = this.dataset.variable;
            const start = messageTemplate.selectionStart;
            const end = messageTemplate.selectionEnd;
            const text = messageTemplate.value;
            messageTemplate.value = text.substring(0, start) + variable + text.substring(end);
            messageTemplate.focus();
            messageTemplate.setSelectionRange(start + variable.length, start + variable.length);
        });
    });

    function generateInvitationLink(guestId) {
        const protocol = window.location.protocol;
        const host = window.location.host;
        return `${protocol}//${host}/rsvp.php?id=${guestId}`;
    }

    function prepareMessage(guestName, guestsCount, tableNumber, guestId) {
        const template = messageTemplate.value;
        const link = generateInvitationLink(guestId);
        const eventLocationLink = "<?php echo htmlspecialchars($event['Maps_link']); ?>";
        const venueName = "<?php echo htmlspecialchars($event['venue_' . $lang]); ?>";

        let locationText = '';
        if (venueName && eventLocationLink) {
            locationText = `${venueName} - ${eventLocationLink}`;
        } else if (venueName) {
            locationText = venueName;
        } else if (eventLocationLink) {
            locationText = eventLocationLink;
        } else {
            locationText = '-';
        }

        return template
            .replace(/\(guest_name\)/g, guestName)
            .replace(/\(guests_count\)/g, guestsCount)
            .replace(/\(table_number\)/g, tableNumber || '-')
            .replace(/\(invitation_link\)/g, link)
            .replace(/\(event_location_link\)/g, locationText);
    }

    function sendWhatsApp(phoneNumber, guestName, guestsCount, tableNumber, guestId) {
        if (!phoneNumber || phoneNumber.trim() === '-' || phoneNumber.trim() === '') {
            Swal.fire(translations.import_error, translations.invalid_phone, 'warning');
            return;
        }

        const message = prepareMessage(guestName, guestsCount, tableNumber, guestId);
        const encodedMessage = encodeURIComponent(message);

        let cleanPhoneNumber = phoneNumber.replace(/[-\s]/g, '');

        if (!cleanPhoneNumber.startsWith('+')) {
            cleanPhoneNumber = '962' + (cleanPhoneNumber.startsWith('0') ? cleanPhoneNumber.substring(1) : cleanPhoneNumber);
        } else {
            cleanPhoneNumber = cleanPhoneNumber.substring(1);
        }

        const url = `https://wa.me/${cleanPhoneNumber}?text=${encodedMessage}`;
        window.open(url, '_blank');
    }

    function copyInvitation(guestName, guestsCount, tableNumber, guestId) {
        const message = prepareMessage(guestName, guestsCount, tableNumber, guestId);
        navigator.clipboard.writeText(message).then(() => {
            Swal.fire({
                icon: 'success',
                title: translations.copy_tooltip,
                text: 'تم نسخ الرسالة بنجاح إلى الحافظة.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل في نسخ الرسالة.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            console.error('Failed to copy text: ', err);
        });
    }

</script>

</body>
</html>