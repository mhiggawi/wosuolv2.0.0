<?php
// logout.php - Clean and working version with "Remember Me" deletion

// Configure session settings BEFORE starting session
ini_set('session.cookie_httponly', 1);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
require_once 'db_config.php';

// Language System
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
if (isset($_POST['switch_language'])) {
    $lang = $_POST['switch_language'] === 'en' ? 'en' : 'ar';
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/');
}

// Language texts
$texts = [
    'ar' => [
        'logout_title' => 'تسجيل الخروج',
        'confirm_logout' => 'تأكيد تسجيل الخروج',
        'logout_message' => 'هل أنت متأكد من رغبتك في تسجيل الخروج؟',
        'logout_warning' => 'سيتم إنهاء جلستك الحالية وستحتاج لتسجيل الدخول مرة أخرى.',
        'session_info' => 'معلومات الجلسة',
        'logged_in_as' => 'مسجل دخول باسم',
        'login_time' => 'وقت تسجيل الدخول',
        'session_duration' => 'مدة الجلسة',
        'ip_address' => 'عنوان IP',
        'user_role' => 'دور المستخدم',
        'confirm_logout_btn' => 'تأكيد تسجيل الخروج',
        'cancel_btn' => 'إلغاء',
        'back_to_dashboard' => 'العودة للوحة التحكم',
        'goodbye_message' => 'شكراً لاستخدامك نظام وصول',
        'logout_success' => 'تم تسجيل خروجك بنجاح',
        'see_you_soon' => 'نراك قريباً!',
        'login_again' => 'تسجيل دخول مرة أخرى',
        'minutes' => 'دقيقة',
        'hours' => 'ساعة',
        'seconds' => 'ثانية',
        'admin' => 'مدير',
        'viewer' => 'مشاهد',
        'checkin_user' => 'مسجل دخول'
    ],
    'en' => [
        'logout_title' => 'Logout',
        'confirm_logout' => 'Confirm Logout',
        'logout_message' => 'Are you sure you want to logout?',
        'logout_warning' => 'Your current session will be terminated and you will need to login again.',
        'session_info' => 'Session Information',
        'logged_in_as' => 'Logged in as',
        'login_time' => 'Login Time',
        'session_duration' => 'Session Duration',
        'ip_address' => 'IP Address',
        'user_role' => 'User Role',
        'confirm_logout_btn' => 'Confirm Logout',
        'cancel_btn' => 'Cancel',
        'back_to_dashboard' => 'Back to Dashboard',
        'goodbye_message' => 'Thank you for using Wosuol',
        'logout_success' => 'You have been logged out successfully',
        'see_you_soon' => 'See you soon!',
        'login_again' => 'Login Again',
        'minutes' => 'minutes',
        'hours' => 'hours',
        'seconds' => 'seconds',
        'admin' => 'Administrator',
        'viewer' => 'Viewer',
        'checkin_user' => 'Check-in User'
    ]
];

$t = $texts[$lang];

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$step = $_GET['step'] ?? 'confirm';

// Collect session information for display
$session_info = [];
if ($is_logged_in) {
    $session_info = [
        'username' => $_SESSION['username'] ?? 'N/A',
        'role' => $_SESSION['role'] ?? 'N/A',
        'login_time' => $_SESSION['login_time'] ?? time(),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
}

// Handle logout confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout']) && !isset($_POST['switch_language'])) {
    
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = 'Security token mismatch.';
    } else {
        
        // --- New "Remember Me" deletion logic ---
        if (isset($_COOKIE['remember_me'])) {
            $token_hash = explode(':', $_COOKIE['remember_me'])[1];
            $stmt_delete_token = $mysqli->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
            $stmt_delete_token->bind_param("s", $token_hash);
            $stmt_delete_token->execute();
            $stmt_delete_token->close();
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        // Store data for goodbye page
        $goodbye_data = [
            'username' => $_SESSION['username'] ?? '',
            'session_duration' => time() - ($_SESSION['login_time'] ?? time())
        ];
        
        // Clear all session data
        $_SESSION = array();
        
        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Start new session for goodbye message
        session_start();
        $_SESSION['language'] = $lang;
        $_SESSION['goodbye_data'] = $goodbye_data;
        $_SESSION['logout_success'] = true;
        
        // Redirect to goodbye page
        header('Location: logout.php?step=goodbye');
        exit;
    }
}

// Helper functions
function formatDuration($seconds, $texts) {
    if ($seconds < 60) {
        return $seconds . ' ' . $texts['seconds'];
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . ' ' . $texts['minutes'];
    } else {
        $hours = floor($seconds / 3600);
        return $hours . ' ' . $texts['hours'];
    }
}

function getRoleText($role, $texts) {
    switch ($role) {
        case 'admin': return $texts['admin'];
        case 'viewer': return $texts['viewer'];
        case 'checkin_user': return $texts['checkin_user'];
        default: return $role;
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['logout_title'] ?> - وصول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #9CAC80;
            --dark-green: #7a8a5e;
            --premium-black: #0a0a0a;
        }

        body { 
            font-family: 'Cairo', sans-serif; 
            background: 
                radial-gradient(circle at 20% 80%, rgba(156, 172, 128, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(160, 124, 94, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(212, 175, 55, 0.06) 0%, transparent 50%),
                linear-gradient(135deg, 
                    rgba(10, 10, 10, 0.98) 0%, 
                    rgba(20, 20, 20, 0.95) 25%,
                    rgba(15, 15, 15, 0.97) 50%,
                    rgba(25, 25, 25, 0.94) 75%,
                    rgba(10, 10, 10, 0.98) 100%);
            min-height: 100vh;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .logout-container {
            background: rgba(10, 10, 10, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 550px;
            text-align: center;
        }
        
        .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .icon-wrapper.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        .icon-wrapper.success {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        }
        
        .session-info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: <?= $lang === 'ar' ? 'right' : 'left' ?>;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #d1d5db; /* text-gray-300 */
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-value {
            font-weight: 500;
            color: #f9fafb; /* text-gray-50 */
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: white;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }
        .btn-secondary:hover {
            background: var(--primary-green);
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
        }
        
        .language-toggle button {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .language-toggle button:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--primary-green);
        }
    </style>
</head>
<body>
    <div class="language-toggle absolute top-4 right-4">
        <form method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>">
                <i class="fas fa-globe"></i>
                <?= $lang === 'ar' ? 'English' : 'العربية' ?>
            </button>
        </form>
    </div>

    <div class="logout-container">
        
        <?php if ($step === 'goodbye' || isset($_SESSION['logout_success'])): ?>
            <div>
                <div class="icon-wrapper success">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-white mb-2"><?= $t['goodbye_message'] ?></h1>
                <p class="text-lg text-gray-300 mb-6"><?= $t['logout_success'] ?></p>
                
                <div class="mt-8">
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        <?= $t['login_again'] ?>
                    </a>
                </div>
            </div>
            
            <?php  
            // Clear goodbye data
            unset($_SESSION['goodbye_data']); 
            unset($_SESSION['logout_success']);
            ?>
            
        <?php elseif ($is_logged_in): ?>
            <div class="icon-wrapper danger">
                <i class="fas fa-sign-out-alt text-white text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-2"><?= $t['confirm_logout'] ?></h1>
            <p class="text-gray-300 mb-2"><?= $t['logout_message'] ?></p>
            <p class="text-sm text-gray-500"><?= $t['logout_warning'] ?></p>
            
            <div class="session-info-card">
                <h3 class="text-lg font-semibold mb-4 text-center text-white"><?= $t['session_info'] ?></h3>
                
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-user"></i> <?= $t['logged_in_as'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($session_info['username']) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-user-tag"></i> <?= $t['user_role'] ?></div>
                    <div class="info-value"><?= getRoleText($session_info['role'], $t) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-clock"></i> <?= $t['login_time'] ?></div>
                    <div class="info-value"><?= date('Y-m-d H:i:s', $session_info['login_time']) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-history"></i> <?= $t['session_duration'] ?></div>
                    <div class="info-value"><?= formatDuration(time() - $session_info['login_time'], $t) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> <?= $t['ip_address'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($session_info['ip_address']) ?></div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                <form method="POST" class="w-full sm:w-auto">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" name="confirm_logout" class="btn btn-danger w-full">
                        <i class="fas fa-sign-out-alt"></i>
                        <?= $t['confirm_logout_btn'] ?>
                    </button>
                </form>
                
                <?php
                // Determine dashboard URL based on role
                $dashboard_url = 'events.php'; // Default for admin
                if (isset($_SESSION['role'])) {
                    switch ($_SESSION['role']) {
                        case 'viewer':
                            $dashboard_url = isset($_SESSION['event_id_access']) ? 'dashboard.php?event_id=' . $_SESSION['event_id_access'] : 'dashboard.php';
                            break;
                        case 'checkin_user':
                            $dashboard_url = isset($_SESSION['event_id_access']) ? 'checkin.php?event_id=' . $_SESSION['event_id_access'] : 'checkin.php';
                            break;
                    }
                }
                ?>
                
                <a href="<?= $dashboard_url ?>" class="btn btn-secondary w-full sm:w-auto">
                    <i class="fas fa-arrow-left"></i>
                    <?= $t['back_to_dashboard'] ?>
                </a>
            </div>
            
        <?php else: ?>
            <div class="icon-wrapper success">
                <i class="fas fa-info-circle text-white text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-2"><?= $t['logout_success'] ?></h1>
            <p class="text-gray-300 mb-6"><?= $lang === 'ar' ? 'أنت غير مسجل دخول حالياً' : 'You are not currently logged in' ?></p>
            
            <div class="mt-8">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    <?= $t['login_again'] ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-redirect after successful logout (goodbye page)
        <?php if ($step === 'goodbye'): ?>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 5000); // Redirect after 5 seconds
        <?php endif; ?>
    </script>
</body>
</html>