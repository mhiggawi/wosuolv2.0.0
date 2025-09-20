<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['login_title'] ?> - وصول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
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
    </script>
    <script src="js/login.js"></script>
    <?php if ($messageType === 'success' && !empty($redirect_url)): ?>
    <script>
        setTimeout(() => window.location.href = '<?= $redirect_url ?>', 1500);
    </script>
    <?php endif; ?>
</body>
</html>
