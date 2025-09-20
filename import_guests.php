<?php
// import_guests.php - Enhanced import with better phone number handling and validation
session_start();
require_once 'db_config.php';

// Security & Permission Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !in_array($_SESSION['role'], ['admin', 'checkin_user', 'viewer'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'غير مخول للوصول']);
    exit;
}

$user_role = $_SESSION['role'];
$user_event_access = $_SESSION['event_id_access'] ?? $_SESSION['event_id'] ?? null;

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['event_id']) || !isset($input['guests'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
    exit;
}

$event_id = intval($input['event_id']);
$guests_data = $input['guests'];

// Check event access permissions
if ($user_role !== 'admin' && $event_id != $user_event_access) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'ليس لديك صلاحية للوصول لهذا الحدث']);
    exit;
}

// Verify event exists
$event_check = $mysqli->prepare("SELECT id FROM events WHERE id = ?");
$event_check->bind_param("i", $event_id);
$event_check->execute();
$event_result = $event_check->get_result();
if ($event_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'الحدث غير موجود']);
    exit;
}
$event_check->close();

$imported_count = 0;
$failed_count = 0;
$errors = [];
$debug_info = [];

// Function to clean and validate phone number
function cleanPhoneNumber($phone) {
    if (empty($phone)) {
        return '';
    }
    
    // Convert to string if it's a number
    $phone_str = strval($phone);
    
    // Remove decimal points if present (from Excel scientific notation)
    if (strpos($phone_str, '.') !== false) {
        $phone_str = rtrim(rtrim($phone_str, '0'), '.');
    }
    
    // Remove all non-digit characters
    $clean_phone = preg_replace('/[^\d]/', '', $phone_str);
    
    // Validate length (should be between 7 and 15 digits)
    if (strlen($clean_phone) >= 7 && strlen($clean_phone) <= 15) {
        return $clean_phone;
    }
    
    return '';
}

// Function to generate unique guest ID
function generateUniqueGuestId($mysqli) {
    $attempts = 0;
    do {
        $guest_id = substr(str_shuffle('0123456789abcdef'), 0, 4);
        $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM guests WHERE guest_id = ?");
        $check_stmt->bind_param("s", $guest_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $exists = $check_result->fetch_row()[0];
        $check_stmt->close();
        $attempts++;
    } while ($exists > 0 && $attempts < 20);
    
    return $attempts < 20 ? $guest_id : null;
}

// Start transaction
$mysqli->autocommit(false);

try {
    foreach ($guests_data as $index => $guest_data) {
        $row_number = $index + 2; // Adding 2 because Excel starts from 1 and we skip header
        
        // Validate required data
        $name_ar = trim($guest_data['name_ar'] ?? '');
        if (empty($name_ar)) {
            $errors[] = "السطر {$row_number}: اسم الضيف مطلوب";
            $failed_count++;
            continue;
        }
        
        // Enhanced phone number processing
        $phone_number = '';
        if (isset($guest_data['phone_number']) && !empty($guest_data['phone_number'])) {
            $original_phone = $guest_data['phone_number'];
            $phone_number = cleanPhoneNumber($original_phone);
            
            if (empty($phone_number) && !empty($original_phone)) {
                $errors[] = "السطر {$row_number}: رقم الهاتف '{$original_phone}' غير صحيح، تم تجاهله";
            }
            
            // Debug info for first few records
            if ($index < 5) {
                $debug_info[] = [
                    'row' => $row_number,
                    'original_phone' => $original_phone,
                    'cleaned_phone' => $phone_number,
                    'original_type' => gettype($original_phone)
                ];
            }
        }
        
        // Validate and process guest count
        $guests_count = 1;
        if (isset($guest_data['guests_count'])) {
            $count_value = $guest_data['guests_count'];
            if (is_numeric($count_value)) {
                $guests_count = max(1, intval($count_value));
            } else {
                $errors[] = "السطر {$row_number}: عدد الأشخاص غير صحيح، تم استخدام القيمة الافتراضية 1";
            }
        }
        
        // Process other fields
        $table_number = trim($guest_data['table_number'] ?? '');
        $assigned_location = trim($guest_data['assigned_location'] ?? '');
        $notes = trim($guest_data['notes'] ?? '');
        
        // Validate table number if provided
        if (!empty($table_number) && !is_numeric($table_number)) {
            // Allow non-numeric table numbers but clean them
            $table_number = preg_replace('/[^\w\s-]/', '', $table_number);
        }
        
        // Generate unique guest ID
        $guest_id = generateUniqueGuestId($mysqli);
        if (!$guest_id) {
            $errors[] = "السطر {$row_number}: فشل في توليد رقم ضيف فريد";
            $failed_count++;
            continue;
        }
        
        // Insert guest with comprehensive error handling
        $insert_stmt = $mysqli->prepare("INSERT INTO guests (event_id, guest_id, name_ar, phone_number, guests_count, table_number, assigned_location, notes, status, checkin_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'not_checked_in')");
        
        if (!$insert_stmt) {
            $errors[] = "السطر {$row_number}: خطأ في إعداد الاستعلام - " . $mysqli->error;
            $failed_count++;
            continue;
        }
        
        $insert_stmt->bind_param("isssssss", 
            $event_id, 
            $guest_id, 
            $name_ar, 
            $phone_number, 
            $guests_count, 
            $table_number, 
            $assigned_location, 
            $notes
        );
        
        if ($insert_stmt->execute()) {
            $imported_count++;
            
            // Log successful imports for debugging (first few only)
            if ($index < 5) {
                error_log("تم إدراج الضيف {$row_number}: " . json_encode([
                    'name' => $name_ar,
                    'phone_processed' => $phone_number,
                    'guests_count' => $guests_count,
                    'table_number' => $table_number ?: 'فارغ'
                ]));
            }
        } else {
            $error_msg = $insert_stmt->error;
            $errors[] = "السطر {$row_number}: فشل في إدراج البيانات - {$error_msg}";
            $failed_count++;
            
            // Log the failed insert for debugging
            error_log("فشل إدراج الضيف {$row_number}: " . json_encode([
                'name' => $name_ar,
                'error' => $error_msg,
                'data' => $guest_data
            ]));
        }
        $insert_stmt->close();
    }
    
    // Commit transaction if we have any successful imports
    if ($imported_count > 0) {
        $mysqli->commit();
    } else {
        $mysqli->rollback();
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'imported_count' => $imported_count,
        'failed_count' => $failed_count,
        'total_processed' => count($guests_data),
        'message' => "تم استيراد {$imported_count} ضيف بنجاح"
    ];
    
    if ($failed_count > 0) {
        $response['message'] .= " مع فشل {$failed_count} ضيف";
        $response['errors'] = $errors;
        $response['has_errors'] = true;
    }
    
    // Add debug information
    $response['debug_info'] = [
        'sample_processing' => $debug_info,
        'phone_validation_summary' => [
            'total_with_phones' => count(array_filter($guests_data, function($g) { 
                return !empty($g['phone_number']); 
            })),
            'successfully_cleaned' => count(array_filter($guests_data, function($g) { 
                return !empty(cleanPhoneNumber($g['phone_number'] ?? '')); 
            }))
        ]
    ];
    
} catch (Exception $e) {
    // Rollback transaction on error
    $mysqli->rollback();
    
    $response = [
        'success' => false,
        'message' => 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage(),
        'imported_count' => 0,
        'failed_count' => count($guests_data),
        'errors' => ['خطأ في النظام: ' . $e->getMessage()]
    ];
    
    // Log the system error
    error_log("Import system error: " . $e->getMessage());
}

// Restore autocommit
$mysqli->autocommit(true);
$mysqli->close();

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>