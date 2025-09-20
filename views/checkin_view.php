<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['checkin_system'] ?>: <?= htmlspecialchars($event_name) ?></title>

    <meta name="theme-color" content="#2d4a22">
    <meta name="description" content="<?= $t['checkin_system'] ?> - <?= htmlspecialchars($event_name) ?>">
    <link rel="manifest" href="data:application/json;charset=utf-8,<?= urlencode(json_encode([
        'name' => $t['checkin_system'] . ': ' . $event_name,
        'short_name' => 'تسجيل الدخول',
        'description' => $t['checkin_system'],
        'start_url' => './checkin.php?event_id=' . $event_id,
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#2d4a22',
        'icons' => [
            [
                'src' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#2d4a22"><path d="M152.1 38.2c9.9 8.9 10.7 24 1.8 33.9l-72 80c-4.4 4.9-10.6 7.8-17.2 7.9s-12.9-2.4-17.6-7L7 113C-2.3 103.6-2.3 88.4 7 79s24.6-9.4 33.9 0l22.1 22.1 55.1-61.2c8.9-9.9 24-10.7 33.9-1.8zm0 160c9.9 8.9 10.7 24 1.8 33.9l-72 80c-4.4 4.9-10.6 7.8-17.2 7.9s-12.9-2.4-17.6-7L7 273c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l22.1 22.1 55.1-61.2c8.9-9.9 24-10.7 33.9-1.8zM224 96c0-17.7 14.3-32 32-32H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H256c-17.7 0-32-14.3-32-32zm0 160c0-17.7 14.3-32 32-32H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H256c-17.7 0-32-14.3-32-32zM160 416c0-17.7 14.3-32 32-32H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H192c-17.7 0-32-14.3-32-32zM48 368a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>'),
                'sizes' => '512x512',
                'type' => 'image/svg+xml'
            ]
        ]
    ])) ?>">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= $t['checkin_system'] ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/checkin.css">
</head>
<body>
    <div class="header-brand">
        <a href="https://wosuol.com" target="_blank" class="wosuol-logo">
            <div class="wosuol-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div style="font-size: 1.5rem;">وصول</div>
                <div style="font-size: 0.8rem; opacity: 0.7;"><?= $t['wosuol_tagline'] ?></div>
            </div>
        </a>
        <div class="header-buttons">
            <form method="POST" style="display: inline;">
                <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>"
                        class="btn">
                    <i class="fas fa-globe"></i>
                    <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                </button>
            </form>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="events.php" class="btn">
                    <i class="fas fa-arrow-left"></i>
                    <?= $t['back_to_events'] ?>
                </a>
            <?php else: ?>
                <a href="logout.php" class="btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <?= $t['logout'] ?>
                </a>
            <?php endif; ?>

            <button id="install-button" class="btn" style="display: none;">
                <i class="fas fa-download"></i>
                <?= $t['install_app'] ?>
            </button>

            <button id="download-offline-button" class="btn btn-success">
                <i class="fas fa-cloud-download-alt"></i>
                <?= $t['download_offline'] ?>
            </button>
        </div>
    </div>

    <div class="container">
        <?php if ($isViewerMode): ?>
        <div class="bg-blue-100 text-blue-800 p-4 rounded-lg mb-4 text-center">
            <i class="fas fa-eye"></i>
            <strong><?= $t['viewer_mode'] ?></strong> - <?= $t['no_permission_checkin'] ?>
        </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">
            <i class="fas fa-clipboard-check text-green-700"></i>
            <?= $t['checkin_system'] ?>
            <?php if ($isViewerMode): ?>
                <span class="text-sm text-green-700">(<?= $t['viewer_mode'] ?>)</span>
            <?php endif; ?>
        </h2>
        <p class="text-center text-gray-600 mb-4"><?= $t['event_title'] ?>: <?= htmlspecialchars($event_name) ?></p>

        <div class="stats-bar" id="stats-bar">
            <div class="stat-item">
                <span class="stat-number" id="today-checkins">0</span>
                <div class="stat-label"><?= $t['today_checkins'] ?></div>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="total-confirmed">0</span>
                <div class="stat-label"><?= $t['total_confirmed'] ?></div>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="total-pending">0</span>
                <div class="stat-label"><?= $t['total_pending'] ?></div>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="remaining-guests">0</span>
                <div class="stat-label"><?= $t['remaining_guests'] ?></div>
            </div>
        </div>

        <p class="text-gray-600 mb-6 text-center"><?= $t['scan_qr_or_search'] ?></p>

        <video id="video" playsinline></video>
        <canvas id="canvas" class="hidden"></canvas>

        <div class="control-buttons">
            <button id="start-scan-button" class="btn">
                <i class="fas fa-qrcode"></i>
                <?= $t['start_scanning'] ?>
            </button>
            <button id="stop-scan-button" class="btn">
                <i class="fas fa-stop"></i>
                <?= $t['stop_scanning'] ?>
            </button>
            <button id="sound-toggle" class="btn btn-toggle">
                <i class="fas fa-volume-up"></i>
                <?= $t['sound_enabled'] ?>
            </button>
            <button id="manual-toggle" class="btn btn-toggle">
                <i class="fas fa-keyboard"></i>
                <?= $t['manual_entry'] ?>
            </button>
        </div>

        <div class="flex justify-center items-center mb-4">
            <span class="text-sm text-gray-600"><?= $t['volume_control'] ?>:</span>
            <input type="range" id="volume-slider" class="volume-slider" min="0" max="1" step="0.1" value="0.7">
            <span id="volume-display" class="text-sm text-gray-600 ml-2">70%</span>
        </div>

        <div class="search-container">
            <div class="flex gap-2">
                <input type="text"
                       id="search-input"
                       class="search-input flex-grow"
                       placeholder="<?= $t['search_placeholder'] ?>"
                       autocomplete="off">
                <?php if (!$isViewerMode): ?>
                <button id="check-in-button" class="btn btn-success">
                    <i class="fas fa-check"></i>
                    <?= $t['checkin_button'] ?>
                </button>
                <button id="confirm-checkin-button" class="btn btn-warning" style="display: none;">
                    <i class="fas fa-user-check"></i>
                    <?= $t['confirm_and_checkin'] ?>
                </button>
                <?php else: ?>
                <button class="btn" disabled>
                    <i class="fas fa-eye"></i>
                    <?= $t['viewer_mode'] ?>
                </button>
                <?php endif; ?>
            </div>
            <div id="suggestions-box" class="hidden"></div>
        </div>

        <div id="response-area" class="response-area">
            <p class="text-gray-500"><?= $t['results_appear_here'] ?></p>
        </div>

        <div class="mt-4">
            <button id="advanced-search-toggle" class="btn btn-toggle">
                <i class="fas fa-search-plus"></i>
                <?= $t['advanced_search'] ?>
            </button>
        </div>

        <div id="advanced-search-panel" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?= $t['search_by_table'] ?>:</label>
                    <input type="text" id="table-search" class="search-input" placeholder="رقم الطاولة...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?= $t['search_by_status'] ?>:</label>
                    <select id="status-filter" class="search-input">
                        <option value=""><?= $t['all_statuses'] ?></option>
                        <option value="confirmed"><?= $t['confirmed'] ?></option>
                        <option value="pending"><?= $t['pending'] ?></option>
                        <option value="canceled"><?= $t['canceled'] ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-2 mt-4">
            <button id="export-button" class="btn">
                <i class="fas fa-download"></i>
                <?= $t['export_report'] ?>
            </button>
            <button id="print-button" class="btn">
                <i class="fas fa-print"></i>
                <?= $t['print_list'] ?>
            </button>
            <button id="backup-button" class="btn btn-warning">
                <i class="fas fa-save"></i>
                <?= $t['backup_data'] ?>
            </button>
        </div>

        <div class="recent-checkins">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold text-gray-700">
                    <i class="fas fa-history"></i>
                    <?= $t['recent_checkins'] ?>
                </h3>
                <button id="clear-recent" class="text-sm text-green-700 hover:underline">
                    <i class="fas fa-trash"></i>
                    <?= $t['clear_recent'] ?>
                </button>
            </div>
            <div id="recent-list"></div>
        </div>
    </div>

    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="footer-brand">
        <p class="text-gray-600 mb-2">
            <?= $t['powered_by'] ?>
            <a href="https://wosuol.com" target="_blank">
                <strong>وصول - Wosuol.com</strong>
            </a>
        </p>
        <p class="text-sm text-gray-500">
            &copy; <?= date('Y') ?> جميع الحقوق محفوظة
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script src="js/checkin.js"></script>
</body>
</html>
