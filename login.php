<?php
// login.php - Enhanced with "Remember Me" functionality
// IMPORTANT: The "Remember Me" check must be at the very top of the file
// to ensure the redirect works correctly.

session_start();
require_once 'db_config.php';

// --- "Remember Me" & Automatic Login Check ---
if (!isset($_SESSION['loggedin']) && isset($_COOKIE['remember_me'])) {
    list($user_token, $token_hash) = explode(':', $_COOKIE['remember_me']);

    $stmt = $mysqli->prepare("SELECT u.username, u.role, u.event_id, rt.expires_at FROM users u JOIN remember_tokens rt ON u.username = rt.username WHERE rt.token_hash = ?");
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (time() < strtotime($row['expires_at'])) {
            // Valid token, log the user in
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['event_id_access'] = $row['event_id'];

            // Renew the token for security
            $new_token = bin2hex(random_bytes(32));
            $new_token_hash = password_hash($new_token, PASSWORD_DEFAULT);
            $new_expiry = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)); // 30 days
            $stmt_update = $mysqli->prepare("UPDATE remember_tokens SET token_hash = ?, expires_at = ? WHERE username = ?");
            $stmt_update->bind_param("sss", $new_token_hash, $new_expiry, $row['username']);
            $stmt_update->execute();
            $stmt_update->close();

            setcookie('remember_me', $row['username'] . ':' . $new_token_hash, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            // Determine redirect URL
            $redirect_url = '';
            switch ($row['role']) {
                case 'admin': $redirect_url = 'events.php'; break;
                case 'viewer': $redirect_url = 'dashboard.php?event_id=' . $row['event_id']; break;
                case 'checkin_user': $redirect_url = 'checkin.php?event_id=' . $row['event_id']; break;
            }
            header("Location: $redirect_url");
            exit;
        } else {
            // Expired token, remove from database
            $stmt_delete = $mysqli->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
            $stmt_delete->bind_param("s", $token_hash);
            $stmt_delete->execute();
            $stmt_delete->close();
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }
    $stmt->close();
}


// --- Language System ---
$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
if (isset($_POST['switch_language'])) {
    $lang = $_POST['switch_language'] === 'en' ? 'en' : 'ar';
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/');
}

// Language texts
$texts = [
    'ar' => [
        'login_title' => 'تسجيل الدخول',
        'welcome_back' => 'مرحباً بعودتك',
        'login_subtitle' => 'يرجى تسجيل الدخول للوصول إلى لوحة التحكم',
        'username_label' => 'اسم المستخدم',
        'username_placeholder' => 'أدخل اسم المستخدم',
        'password_label' => 'كلمة المرور',
        'password_placeholder' => 'أدخل كلمة المرور',
        'remember_me' => 'تذكرني',
        'login_button' => 'تسجيل الدخول',
        'logging_in' => 'جاري تسجيل الدخول...',
        'show_password' => 'إظهار كلمة المرور',
        'hide_password' => 'إخفاء كلمة المرور',
        'error_invalid_credentials' => 'اسم المستخدم أو كلمة المرور غير صحيحة.',
        'error_account_locked' => 'تم قفل الحساب مؤقتاً. يرجى المحاولة بعد {minutes} دقائق.',
        'error_too_many_attempts' => 'محاولات كثيرة جداً. يرجى الانتظار {seconds} ثانية.',
        'error_fill_fields' => 'الرجاء إدخال اسم المستخدم وكلمة المرور.',
        'error_csrf' => 'خطأ في التحقق من صحة الطلب. يرجى المحاولة مرة أخرى.',
        'error_general' => 'عذراً، حدث خطأ ما. يرجى المحاولة مرة أخرى.',
        'error_no_event_access' => 'هذا المستخدم غير مصرح له بالدخول.',
        'attempts_remaining' => 'محاولات متبقية: {count}',
        'login_success' => 'تم تسجيل الدخول بنجاح! جاري التحويل...'
    ],
    'en' => [
        'login_title' => 'Login',
        'welcome_back' => 'Welcome Back',
        'login_subtitle' => 'Please sign in to access your dashboard',
        'username_label' => 'Username',
        'username_placeholder' => 'Enter your username',
        'password_label' => 'Password',
        'password_placeholder' => 'Enter your password',
        'remember_me' => 'Remember me',
        'login_button' => 'Sign In',
        'logging_in' => 'Signing in...',
        'show_password' => 'Show password',
        'hide_password' => 'Hide password',
        'error_invalid_credentials' => 'Invalid username or password.',
        'error_account_locked' => 'Account temporarily locked. Please try again after {minutes} minutes.',
        'error_too_many_attempts' => 'Too many attempts. Please wait {seconds} seconds.',
        'error_fill_fields' => 'Please enter both username and password.',
        'error_csrf' => 'Security token mismatch. Please try again.',
        'error_general' => 'Sorry, something went wrong. Please try again.',
        'error_no_event_access' => 'This user is not authorized to access.',
        'attempts_remaining' => 'Attempts remaining: {count}',
        'login_success' => 'Login successful! Redirecting...'
    ]
];

$t = $texts[$lang];

// --- Security Settings ---
$MAX_ATTEMPTS = 5;
$LOCKOUT_TIME = 900; // 15 minutes
$RATE_LIMIT_TIME = 60; // 1 minute
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// --- CSRF Protection ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- Rate Limiting & Security ---
$attempts_key = 'login_attempts_' . md5($client_ip);
$lockout_key = 'login_lockout_' . md5($client_ip);
$last_attempt_key = 'last_attempt_' . md5($client_ip);

if (!isset($_SESSION[$attempts_key])) {
    $_SESSION[$attempts_key] = 0;
}

$message = '';
$messageType = 'error';
$attempts_remaining = $MAX_ATTEMPTS - $_SESSION[$attempts_key];
$is_locked_out = false;
$time_until_unlock = 0;

if (isset($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > time()) {
    $is_locked_out = true;
    $time_until_unlock = $_SESSION[$lockout_key] - time();
    $minutes_remaining = ceil($time_until_unlock / 60);
    $message = str_replace('{minutes}', $minutes_remaining, $t['error_account_locked']);
}

$rate_limited = false;
$rate_limit_seconds = 0;
if (isset($_SESSION[$last_attempt_key]) && $_SESSION[$attempts_key] >= 3) {
    $time_since_last = time() - $_SESSION[$last_attempt_key];
    if ($time_since_last < $RATE_LIMIT_TIME) {
        $rate_limited = true;
        $rate_limit_seconds = $RATE_LIMIT_TIME - $time_since_last;
        $message = str_replace('{seconds}', $rate_limit_seconds, $t['error_too_many_attempts']);
    }
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['switch_language'])) {
    // ... (Your existing login logic remains here) ...
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = $t['error_csrf'];
    }
    // Check if locked out or rate limited
    elseif ($is_locked_out || $rate_limited) {
        // Message is already set
    }
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);

        if (empty($username) || empty($password)) {
            $message = $t['error_fill_fields'];
            $_SESSION[$attempts_key]++;
            $_SESSION[$last_attempt_key] = time();
        } else {
            
            // Database query
            $sql = "SELECT username, password_hash, role, event_id FROM users WHERE username = ?";
            
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $username);
                
                if ($stmt->execute()) {
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($db_username, $db_password_hash, $db_role, $db_event_id);
                        
                        if ($stmt->fetch()) {
                            if (password_verify($password, $db_password_hash)) {
                                
                                // Successful login
                                unset($_SESSION[$attempts_key], $_SESSION[$lockout_key], $_SESSION[$last_attempt_key]);
                                session_regenerate_id(true);

                                $_SESSION['loggedin'] = true;
                                $_SESSION['username'] = $db_username;
                                $_SESSION['role'] = $db_role;
                                $_SESSION['event_id_access'] = $db_event_id;
                                
                                $message = $t['login_success'];
                                $messageType = 'success';

                                // --- New "Remember Me" logic for successful login ---
                                if ($remember_me) {
                                    $token = bin2hex(random_bytes(32));
                                    $token_hash = password_hash($token, PASSWORD_DEFAULT);
                                    $expires_at = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)); // 30 days
                                    
                                    // Delete any old tokens
                                    $stmt_delete = $mysqli->prepare("DELETE FROM remember_tokens WHERE username = ?");
                                    $stmt_delete->bind_param("s", $db_username);
                                    $stmt_delete->execute();
                                    $stmt_delete->close();
                                    
                                    // Insert new token
                                    $stmt_insert = $mysqli->prepare("INSERT INTO remember_tokens (username, token_hash, expires_at) VALUES (?, ?, ?)");
                                    $stmt_insert->bind_param("sss", $db_username, $token_hash, $expires_at);
                                    $stmt_insert->execute();
                                    $stmt_insert->close();
                                    
                                    // Set cookie
                                    setcookie('remember_me', $db_username . ':' . $token_hash, [
                                        'expires' => time() + (30 * 24 * 60 * 60),
                                        'path' => '/',
                                        'domain' => $_SERVER['HTTP_HOST'],
                                        'secure' => isset($_SERVER['HTTPS']),
                                        'httponly' => true,
                                        'samesite' => 'Lax'
                                    ]);
                                }
                                
                                // Determine redirect URL
                                $redirect_url = '';
                                switch ($db_role) {
                                    case 'admin': $redirect_url = 'events.php'; break;
                                    case 'viewer': $redirect_url = 'dashboard.php?event_id=' . $db_event_id; break;
                                    case 'checkin_user': $redirect_url = 'checkin.php?event_id=' . $db_event_id; break;
                                }

                                if (empty($redirect_url)) {
                                    $message = $t['error_no_event_access'];
                                    $messageType = 'error';
                                } else {
                                    echo "<script>setTimeout(() => window.location.href = '$redirect_url', 1500);</script>";
                                }

                            } else {
                                // Invalid password
                                $message = $t['error_invalid_credentials'];
                                $_SESSION[$attempts_key]++;
                                $_SESSION[$last_attempt_key] = time();
                            }
                        }
                    } else {
                        // User not found
                        $message = $t['error_invalid_credentials'];
                        $_SESSION[$attempts_key]++;
                        $_SESSION[$last_attempt_key] = time();
                    }
                } else {
                    $message = $t['error_general'];
                }
                $stmt->close();
            } else {
                $message = $t['error_general'];
            }
        }
        
        // Lock account if max attempts reached
        if ($_SESSION[$attempts_key] >= $MAX_ATTEMPTS) {
            $_SESSION[$lockout_key] = time() + $LOCKOUT_TIME;
            $message = str_replace('{minutes}', ceil($LOCKOUT_TIME / 60), $t['error_account_locked']);
        }
        $attempts_remaining = max(0, $MAX_ATTEMPTS - $_SESSION[$attempts_key]);
    }
}

// $mysqli->close(); // Close connection at the end of the script if needed
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['login_title'] ?> - وصول</title>
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

        .login-container {
            background: rgba(10, 10, 10, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 420px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.1);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #FFFFFF !important; /* --- تم التعديل هنا لضمان الوضوح --- */
            font-size: 0.875rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: #9ca3af; /* text-gray-400 */
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(156, 172, 128, 0.3);
        }

        .password-toggle {
            position: absolute;
            inset-inline-end: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(156, 172, 128, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .message {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .message.success {
            background-color: rgba(74, 222, 128, 0.1);
            color: #a7f3d0;
            border: 1px solid rgba(74, 222, 128, 0.3);
        }

        .message.error {
            background-color: rgba(248, 113, 113, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(248, 113, 113, 0.3);
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

        .attempts-counter {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: #fca5a5; /* Light red */
            font-weight: 600;
        }

    </style>
</head>
<body>
    <div class="language-toggle absolute top-4 right-4">
        <form method="POST" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>">
                <i class="fas fa-globe"></i>
                <?= $lang === 'ar' ? 'English' : 'العربية' ?>
            </button>
        </form>
    </div>

    <div class="login-container">
        <div class="text-center mb-8">
            <div class="logo-icon">
                <img src="logo.png" alt="وصول" class="w-10 h-10 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-white mt-4"><?= $t['welcome_back'] ?></h1>
            <p class="text-gray-400 text-sm"><?= $t['login_subtitle'] ?></p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION[$attempts_key] > 0 && !$is_locked_out && $messageType !== 'success'): ?>
            <div class="attempts-counter">
                <i class="fas fa-shield-alt"></i>
                <?= str_replace('{count}', $attempts_remaining, $t['attempts_remaining']) ?>
            </div>
        <?php endif; ?>
        <?php if ($rate_limited || $is_locked_out): ?>
            <div class="attempts-counter">
                <i class="fas fa-clock"></i>
                <span class="countdown-timer" id="countdown-timer"></span>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" <?= ($is_locked_out || $rate_limited) ? 'style="opacity: 0.5; pointer-events: none;"' : '' ?>>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="mb-4">
                <label for="username"><?= $t['username_label'] ?>:</label>
                <input type="text" id="username" name="username" class="form-input"
                       placeholder="<?= $t['username_placeholder'] ?>"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autocomplete="username">
            </div>

            <div class="mb-4">
                <label for="password"><?= $t['password_label'] ?>:</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="<?= $t['password_placeholder'] ?>"
                           required autocomplete="current-password">
                    <i class="fas fa-eye password-toggle" id="passwordToggle" onclick="togglePassword()" title="<?= $t['show_password'] ?>"></i>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="remember_me" class="w-4 h-4 rounded text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 ml-2">
                    <?= $t['remember_me'] ?>
                </label>
            </div>

            <button type="submit" class="btn-primary" id="loginButton">
                <span id="buttonText"><?= $t['login_button'] ?></span>
            </button>
        </form>
    </div>

    <script>
        const texts = <?= json_encode($t, JSON_UNESCAPED_UNICODE) ?>;

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
                toggleIcon.title = texts['hide_password'];
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
                toggleIcon.title = texts['show_password'];
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');

            button.disabled = true;
            buttonText.textContent = texts['logging_in'];
        });

        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) {
                usernameField.focus();
            } else {
                document.getElementById('password').focus();
            }
        });

        <?php if ($rate_limited || $is_locked_out): ?>
        let timeRemaining = <?= $is_locked_out ? $time_until_unlock : $rate_limit_seconds ?>;
        const countdownElement = document.getElementById('countdown-timer');

        function updateCountdown() {
            if (timeRemaining <= 0) {
                location.reload();
                return;
            }
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;

            if (minutes > 0) {
                countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            } else {
                countdownElement.textContent = `${seconds}s`;
            }
            timeRemaining--;
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);
        <?php endif; ?>
    </script>
</body>
</html>