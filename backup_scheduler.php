<?php
// backup_scheduler.php - إدارة جدولة النسخ الاحتياطية
session_start();
require_once 'db_config.php';
require_once 'auto_backup.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// CSRF token generation and validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed.');
}

$lang = $_SESSION['language'] ?? $_COOKIE['language'] ?? 'ar';
$texts = [
    'ar' => [
        'backup_scheduler' => 'مجدول النسخ الاحتياطية',
        'auto_backup_settings' => 'إعدادات النسخ التلقائي',
        'schedule_backup' => 'جدولة النسخ الاحتياطي',
        'backup_frequency' => 'تكرار النسخ الاحتياطي',
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
        'backup_time' => 'وقت النسخ الاحتياطي',
        'auto_cleanup' => 'تنظيف تلقائي للنسخ القديمة',
        'keep_backups_days' => 'الاحتفاظ بالنسخ لعدد الأيام',
        'backup_all_events' => 'نسخ احتياطي لجميع الأحداث',
        'backup_stats' => 'إحصائيات النسخ الاحتياطية',
        'total_backups' => 'إجمالي النسخ',
        'total_size' => 'الحجم الإجمالي',
        'latest_backup' => 'آخر نسخة احتياطية',
        'run_backup_now' => 'تشغيل النسخ الاحتياطي الآن',
        'backup_log' => 'سجل النسخ الاحتياطية',
        'view_log' => 'عرض السجل',
        'clear_log' => 'مسح السجل',
        'cron_setup' => 'إعداد Cron Job',
        'cron_command' => 'أمر Cron',
        'save_settings' => 'حفظ الإعدادات',
        'settings_saved' => 'تم حفظ الإعدادات بنجاح',
        'backup_started' => 'تم بدء النسخ الاحتياطي',
        'log_cleared' => 'تم مسح السجل بنجاح',
        'backups' => 'النسخ الاحتياطية',
        'events' => 'الأحداث',
        'system_info' => 'معلومات النظام',
        'php_path' => 'مسار PHP',
        'script_path' => 'مسار السكريبت',
        'backup_dir' => 'مجلد النسخ الاحتياطية',
        'exec_func' => 'صلاحيات التنفيذ',
        'exec_available' => '✓ متاح (النسخ الاحتياطي في الخلفية)',
        'exec_not_available' => '✗ غير متاح (النسخ الاحتياطي المتزامن)',
        'api_access' => 'واجهة برمجة التطبيقات (API)',
        'backup_all_events_api' => 'نسخ احتياطي لجميع الأحداث',
        'backup_specific_event' => 'نسخ احتياطي لحدث معين',
        'backup_stats_api' => 'إحصائيات النسخ الاحتياطية',
        'security_note' => 'أمان مهم',
        'change_api_key' => 'تأكد من تغيير مفتاح API في ملف auto_backup.php لحماية النظام.',
        'command_explanation' => 'تفسير الأمر',
        'to_setup_cron' => 'لإعداد Cron Job، استخدم الأمر'
    ],
    'en' => [
        'backup_scheduler' => 'Backup Scheduler',
        'auto_backup_settings' => 'Auto Backup Settings',
        'schedule_backup' => 'Schedule Backup',
        'backup_frequency' => 'Backup Frequency',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'backup_time' => 'Backup Time',
        'auto_cleanup' => 'Auto Cleanup Old Backups',
        'keep_backups_days' => 'Keep Backups for Days',
        'backup_all_events' => 'Backup All Events',
        'backup_stats' => 'Backup Statistics',
        'total_backups' => 'Total Backups',
        'total_size' => 'Total Size',
        'latest_backup' => 'Latest Backup',
        'run_backup_now' => 'Run Backup Now',
        'backup_log' => 'Backup Log',
        'view_log' => 'View Log',
        'clear_log' => 'Clear Log',
        'cron_setup' => 'Cron Job Setup',
        'cron_command' => 'Cron Command',
        'save_settings' => 'Save Settings',
        'settings_saved' => 'Settings saved successfully',
        'backup_started' => 'Backup process started',
        'log_cleared' => 'Log cleared successfully',
        'backups' => 'Backup Management',
        'events' => 'Events',
        'system_info' => 'System Information',
        'php_path' => 'PHP Path',
        'script_path' => 'Script Path',
        'backup_dir' => 'Backup Directory',
        'exec_func' => 'Exec Function',
        'exec_available' => '✓ Available (background backup)',
        'exec_not_available' => '✗ Not available (synchronous backup)',
        'api_access' => 'API Access',
        'backup_all_events_api' => 'Backup all events:',
        'backup_specific_event' => 'Backup specific event:',
        'backup_stats_api' => 'Backup statistics:',
        'security_note' => 'Security Important:',
        'change_api_key' => 'Make sure to change the API key in auto_backup.php file to secure your system.',
        'command_explanation' => 'Command explanation:',
        'to_setup_cron' => 'To setup cron job, use command:'
    ]
];
$t = $texts[$lang];

// Create settings table if not exists
$mysqli->query("
    CREATE TABLE IF NOT EXISTS backup_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['switch_language'])) {
        $new_lang = $_POST['switch_language'];
        if (array_key_exists($new_lang, $texts)) {
            $_SESSION['language'] = $new_lang;
            setcookie('language', $new_lang, time() + (86400 * 30), "/"); // 30 days
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['save_settings'])) {
        $settings = [
            'backup_frequency' => $_POST['backup_frequency'],
            'backup_time' => $_POST['backup_time'],
            'auto_cleanup' => isset($_POST['auto_cleanup']) ? '1' : '0',
            'keep_days' => (int)$_POST['keep_days']
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $mysqli->prepare("
                INSERT INTO backup_settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->bind_param("sss", $key, $value, $value);
            $stmt->execute();
            $stmt->close();
        }
        
        $message = $t['settings_saved'];
        $messageType = 'success';
    }
    
    if (isset($_POST['run_backup_now'])) {
        $backup = new AutoBackup($mysqli);
        
        if (function_exists('exec')) {
            $php_path = PHP_BINARY;
            $script_path = __DIR__ . '/auto_backup.php';
            exec("$php_path $script_path all > /dev/null 2>&1 &");
            $message = $t['backup_started'];
            $messageType = 'success';
        } else {
            $result = $backup->backupAllEvents();
            $message = "Backup completed: {$result['success_count']}/{$result['total']} events";
            $messageType = $result['success_count'] > 0 ? 'success' : 'warning';
        }
    }
    
    if (isset($_POST['clear_log'])) {
        $log_file = 'logs/backup_log.txt';
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
        }
        $message = $t['log_cleared'];
        $messageType = 'success';
    }
}

// Get current settings
function getSetting($key, $default = '') {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT setting_value FROM backup_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? htmlspecialchars($row['setting_value']) : htmlspecialchars($default);
}

// Get backup stats
$backup = new AutoBackup($mysqli);
$stats = $backup->getBackupStats();

// Get backup log
$log_content = '';
$log_file = 'logs/backup_log.txt';
if (file_exists($log_file)) {
    $log_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $log_content = implode(PHP_EOL, array_slice($log_lines, -50));
}

// Generate cron command
$backup_frequency = getSetting('backup_frequency', 'daily');
$backup_time = getSetting('backup_time', '02:00');
list($hour, $minute) = explode(':', $backup_time);

$cron_commands = [
    'daily' => "$minute $hour * * *",
    'weekly' => "$minute $hour * * 0",
    'monthly' => "$minute $hour 1 * *"
];

$current_cron = $cron_commands[$backup_frequency] ?? $cron_commands['daily'];
$php_path = PHP_BINARY;
$script_path = realpath(__DIR__ . '/auto_backup.php');
$full_cron_command = "$current_cron $php_path $script_path all";
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['backup_scheduler'] ?> - WOSUOL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: <?= $lang === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" ?>; 
            background: white; 
            padding: 20px; 
            color: #2d4a22;
        }
        .container { 
            max-width: 1200px; 
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
        .page-header, .card { 
            padding: 20px 25px;
            border-radius: 20px;
            font-weight: 600;
            color: #2d4a22;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            margin-bottom: 30px;
        }
        .page-header::before, .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(45, 74, 34, 0.1), transparent);
            transition: left 0.6s ease;
        }
        .page-header:hover::before, .card:hover::before {
            left: 100%;
        }
        .page-header:hover, .card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 8px 25px rgba(45, 74, 34, 0.2);
            border-color: rgba(45, 74, 34, 0.5);
            color: #1a2f15;
            background: rgba(255, 255, 255, 0.95);
        }
        .page-header {
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header-buttons { 
            display: flex; 
            gap: 12px; 
            align-items: center; 
        }
        .btn { 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            font-weight: 600;
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid rgba(45, 74, 34, 0.3);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
        .btn-primary { 
            background: linear-gradient(135deg, rgba(45, 74, 34, 0.9), rgba(26, 47, 21, 0.9));
            color: white; 
        }
        .btn-success { 
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9)); 
            color: white; 
        }
        .btn-warning { 
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.9), rgba(217, 119, 6, 0.9)); 
            color: white; 
        }
        .btn-danger { 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9)); 
            color: white; 
        }
        .btn-secondary { 
            background: linear-gradient(135deg, rgba(107, 114, 128, 0.9), rgba(75, 85, 99, 0.9)); 
            color: white; 
        }
        .btn-sm { 
            padding: 6px 12px; 
            font-size: 0.875rem; 
        }
        input, select {
            transition: all 0.3s ease; 
            border: 2px solid rgba(45, 74, 34, 0.3);
            border-radius: 15px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: #2d4a22;
            font-weight: 600;
        }
        input:focus, select:focus { 
            border-color: rgba(45, 74, 34, 0.6);
            box-shadow: 0 0 0 3px rgba(45, 74, 34, 0.1); 
            background: rgba(255, 255, 255, 0.95);
            outline: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            border: 2px solid rgba(45, 74, 34, 0.2);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 74, 34, 0.1);
            border-color: rgba(45, 74, 34, 0.4);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2d4a22;
        }
        .log-container {
            background: #1a2f15;
            color: #e5e7eb;
            padding: 20px;
            border-radius: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            border: 2px solid rgba(45, 74, 34, 0.3);
        }
        .cron-command {
            background: rgba(45, 74, 34, 0.1);
            padding: 15px;
            border-radius: 15px;
            border: 2px solid rgba(45, 74, 34, 0.2);
            font-family: 'Courier New', monospace;
            word-break: break-all;
            color: #2d4a22;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .cron-command:hover {
            background: rgba(45, 74, 34, 0.15);
            border-color: rgba(45, 74, 34, 0.4);
        }
        code {
            font-family: 'Courier New', monospace;
            background: rgba(45, 74, 34, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            color: #2d4a22;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wosuol-logo">
            <div class="wosuol-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="wosuol-text">وصول</div>
        </div>

        <div class="page-header">
            <h1 class="text-3xl font-bold"><?= $t['backup_scheduler'] ?></h1>
            <div class="header-buttons">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>" 
                            class="btn">
                        <i class="fas fa-language"></i>
                        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                    </button>
                </form>
                <a href="events.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <?= $t['events'] ?>
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="card mb-6">
                <div class="p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : ($messageType === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['backup_stats'] ?></h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= htmlspecialchars($stats['total_backups']) ?></div>
                    <div class="text-gray-600"><?= $t['total_backups'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= htmlspecialchars($stats['total_size_formatted']) ?></div>
                    <div class="text-gray-600"><?= $t['total_size'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="text-sm text-gray-600"><?= $t['latest_backup'] ?></div>
                    <div class="font-bold"><?= htmlspecialchars($stats['newest_backup'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['backup_all_events'] ?></h2>
            <form method="POST" class="flex gap-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" name="run_backup_now" class="btn btn-primary">
                    🚀 <?= $t['run_backup_now'] ?>
                </button>
            </form>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['auto_backup_settings'] ?></h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?= $t['backup_frequency'] ?>
                        </label>
                        <select name="backup_frequency" class="w-full">
                            <option value="daily" <?= getSetting('backup_frequency', 'daily') === 'daily' ? 'selected' : '' ?>>
                                <?= $t['daily'] ?>
                            </option>
                            <option value="weekly" <?= getSetting('backup_frequency') === 'weekly' ? 'selected' : '' ?>>
                                <?= $t['weekly'] ?>
                            </option>
                            <option value="monthly" <?= getSetting('backup_frequency') === 'monthly' ? 'selected' : '' ?>>
                                <?= $t['monthly'] ?>
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?= $t['backup_time'] ?>
                        </label>
                        <input type="time" name="backup_time" value="<?= getSetting('backup_time', '02:00') ?>" 
                               class="w-full">
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_cleanup" <?= getSetting('auto_cleanup', '1') === '1' ? 'checked' : '' ?> 
                               class="mr-2">
                        <?= $t['auto_cleanup'] ?>
                    </label>
                    
                    <div class="flex items-center gap-2">
                        <label class="text-sm"><?= $t['keep_backups_days'] ?>:</label>
                        <input type="number" name="keep_days" value="<?= getSetting('keep_days', '30') ?>" 
                               min="1" max="365" class="w-20">
                    </div>
                </div>
                
                <button type="submit" name="save_settings" class="btn btn-primary">
                    💾 <?= $t['save_settings'] ?>
                </button>
            </form>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6"><?= $t['cron_setup'] ?></h2>
            <p class="text-gray-600 mb-4">
                <?= $t['to_setup_cron'] ?> <code>crontab -e</code>
            </p>
            <div class="cron-command">
                <?= htmlspecialchars($full_cron_command) ?>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                <strong><?= $t['command_explanation'] ?>:</strong><br>
                <code><?= $current_cron ?></code> = <?= $t['backup_frequency'] ?> <?= $lang === 'ar' ? 'في' : 'at' ?> <?= htmlspecialchars($backup_time) ?><br>
            </div>
        </div>

        <div class="card">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold"><?= $t['backup_log'] ?></h2>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" name="clear_log" class="btn btn-danger btn-sm" 
                            onclick="return confirm('<?= $lang === 'ar' ? 'هل أنت متأكد من مسح السجل؟' : 'Are you sure you want to clear the log?' ?>')">
                        🗑️ <?= $t['clear_log'] ?>
                    </button>
                </form>
            </div>
            
            <div class="log-container">
                <?= htmlspecialchars($log_content ?: ($lang === 'ar' ? 'لا يوجد سجل بعد...' : 'No log entries yet...')) ?>
            </div>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6">
                <?= $t['api_access'] ?>
            </h2>
            <div class="space-y-3">
                <div>
                    <strong><?= $t['backup_all_events_api'] ?></strong><br>
                    <code class="text-sm bg-gray-100 p-2 rounded block">
                        <?= htmlspecialchars($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auto_backup.php?action=backup_all&key=wosuol_backup_2024') ?>
                    </code>
                </div>
                
                <div>
                    <strong><?= $t['backup_specific_event'] ?></strong><br>
                    <code class="text-sm bg-gray-100 p-2 rounded block">
                        <?= htmlspecialchars($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auto_backup.php?action=backup_event&event_id=ID&key=wosuol_backup_2024') ?>
                    </code>
                </div>
                
                <div>
                    <strong><?= $t['backup_stats_api'] ?></strong><br>
                    <code class="text-sm bg-gray-100 p-2 rounded block">
                        <?= htmlspecialchars($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/auto_backup.php?action=stats&key=wosuol_backup_2024') ?>
                    </code>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-100 border border-yellow-300 rounded-lg">
                <strong class="text-yellow-800">
                    <?= $t['security_note'] ?>
                </strong><br>
                <span class="text-yellow-700">
                    <?= $t['change_api_key'] ?>
                </span>
            </div>
        </div>

        <div class="card">
            <h2 class="text-2xl font-bold mb-6">
                <?= $t['system_info'] ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong><?= $t['php_path'] ?>:</strong><br>
                    <code class="bg-gray-100 p-1 rounded"><?= htmlspecialchars(PHP_BINARY) ?></code>
                </div>
                <div>
                    <strong><?= $t['script_path'] ?>:</strong><br>
                    <code class="bg-gray-100 p-1 rounded"><?= htmlspecialchars(realpath(__DIR__ . '/auto_backup.php')) ?></code>
                </div>
                <div>
                    <strong><?= $t['backup_dir'] ?>:</strong><br>
                    <code class="bg-gray-100 p-1 rounded"><?= htmlspecialchars(realpath('backups') ?: 'backups (not created yet)') ?></code>
                </div>
                <div>
                    <strong><?= $t['exec_func'] ?>:</strong><br>
                    <span class="<?= function_exists('exec') ? 'text-green-600' : 'text-red-600' ?>">
                        <?= function_exists('exec') ? $t['exec_available'] : $t['exec_not_available'] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh backup stats every 60 seconds
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 60000);
        
        // Copy cron command to clipboard
        document.addEventListener('DOMContentLoaded', function() {
            const cronCommand = document.querySelector('.cron-command');
            if (cronCommand) {
                cronCommand.style.cursor = 'pointer';
                cronCommand.title = '<?= $lang === 'ar' ? 'انقر للنسخ' : 'Click to copy' ?>';
                cronCommand.addEventListener('click', function() {
                    const textToCopy = this.textContent.trim();
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(textToCopy).then(function() {
                            alert('<?= $lang === 'ar' ? 'تم نسخ الأمر!' : 'Command copied!' ?>');
                        });
                    } else {
                        // Fallback for older browsers
                        const tempInput = document.createElement('textarea');
                        tempInput.value = textToCopy;
                        document.body.appendChild(tempInput);
                        tempInput.select();
                        document.execCommand('copy');
                        document.body.removeChild(tempInput);
                        alert('<?= $lang === 'ar' ? 'تم نسخ الأمر!' : 'Command copied!' ?>');
                    }
                });
            }
        });
    </script>
</body>
</html>