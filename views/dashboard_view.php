<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['dashboard'] ?>: <?= htmlspecialchars($event_name) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <div class="wosuol-logo">
            <div class="wosuol-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="wosuol-text">وصول</div>
        </div>

        <div class="page-header">
            <h1 class="text-3xl font-bold"><?= $t['dashboard'] ?>: <?= htmlspecialchars($event_name) ?></h1>
            <div class="header-buttons">
                <div class="live-counter">
                    <div class="pulse-dot"></div>
                    <span><?= $lang === 'ar' ? 'مباشر' : 'Live' ?></span>
                </div>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>"
                        class="btn">
                        <i class="fas fa-language"></i>
                        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                    </button>
                </form>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="guests.php?event_id=<?= $event_id ?>" class="btn">
                        <i class="fas fa-users"></i> <?= $t['manage_guests'] ?>
                    </a>
                    <a href="events.php" class="btn">
                        <i class="fas fa-arrow-left"></i>
                        <?= $t['back_to_events'] ?>
                    </a>
                <?php elseif ($_SESSION['role'] === 'viewer'): ?>
                    <a href="guests.php?event_id=<?= $event_id ?>" class="btn">
                        <i class="fas fa-users"></i>
                        <?= $t['manage_guests'] ?>
                    </a>
                    <a href="logout.php" class="btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <?= $t['logout'] ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-buttons">
            <button id="presentation-mode-btn" class="btn">
                <i class="fas fa-tv"></i>
                <?= $t['presentation_mode'] ?>
            </button>
            <button id="fullscreen-btn" class="btn">
                <i class="fas fa-expand"></i>
                <?= $t['fullscreen'] ?>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="stat-card total">
                <div class="value" id="total-guests">0</div>
                <div class="people-count" id="total-people">0 <?= $t['people_plural'] ?></div>
                <div class="mini-chart">
                    <canvas id="totalChart" width="80" height="30"></canvas>
                </div>
                <div class="label"><?= $t['total_invited'] ?></div>
            </div>
            <div class="stat-card confirmed">
                <div class="value" id="confirmed-guests">0</div>
                <div class="people-count" id="confirmed-people">0 <?= $t['people_plural'] ?></div>
                <div class="mini-chart">
                    <canvas id="confirmedChart" width="80" height="30"></canvas>
                </div>
                <div class="label"><?= $t['confirmed_attendance'] ?></div>
            </div>
            <div class="stat-card checked-in">
                <div class="value" id="checked-in-guests">0</div>
                <div class="people-count" id="checked-in-people">0 <?= $t['people_plural'] ?></div>
                <div class="mini-chart">
                    <canvas id="checkedinChart" width="80" height="30"></canvas>
                </div>
                <div class="label"><?= $t['checked_in_hall'] ?></div>
            </div>
            <div class="stat-card canceled">
                <div class="value" id="canceled-guests">0</div>
                <div class="people-count" id="canceled-people">0 <?= $t['people_plural'] ?></div>
                <div class="mini-chart">
                    <canvas id="canceledChart" width="80" height="30"></canvas>
                </div>
                <div class="label"><?= $t['declined_attendance'] ?></div>
            </div>
            <div class="stat-card pending">
                <div class="value" id="pending-guests">0</div>
                <div class="people-count" id="pending-people">0 <?= $t['people_plural'] ?></div>
                <div class="mini-chart">
                    <canvas id="pendingChart" width="80" height="30"></canvas>
                </div>
                <div class="label"><?= $t['awaiting_response'] ?></div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold"><?= $t['guest_list'] ?></h2>
            <div class="flex gap-4">
                <a href="?event_id=<?= $event_id ?>&export_csv=true"
                    class="btn">
                    <i class="fas fa-file-csv"></i>
                    CSV
                </a>
                <a href="?event_id=<?= $event_id ?>&export_pdf=true" target="_blank"
                    class="btn">
                    <i class="fas fa-file-pdf"></i>
                    PDF
                </a>
                <button id="export-excel-btn" class="btn">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </button>
                <button id="print-btn" class="btn">
                    <i class="fas fa-print"></i>
                    <?= $t['print'] ?>
                </button>
                <button id="refresh-button"
                    class="btn">
                    <i class="fas fa-sync-alt"></i>
                    <?= $t['refresh_data'] ?>
                </button>
            </div>
        </div>

        <input type="text" id="guest-search"
            class="search-input w-full p-3 text-lg mb-6"
            placeholder="<?= $t['search_guest'] ?>">

        <div class="pull-indicator" id="pull-indicator">
            <i class="fas fa-arrow-down"></i>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pull-to-refresh" id="guest-lists">
            <div>
                <div class="column-header checked-in">
                    <i class="fas fa-check-circle"></i>
                    <?= $t['checked_in_hall'] ?>
                </div>
                <div id="checked-in-list" class="guest-list-container"></div>
            </div>
            <div>
                <div class="column-header confirmed">
                    <i class="fas fa-user-check"></i>
                    <?= $t['confirmed_attendance'] ?>
                </div>
                <div id="confirmed-list" class="guest-list-container"></div>
            </div>
            <div>
                <div class="column-header canceled">
                    <i class="fas fa-user-times"></i>
                    <?= $t['declined_attendance'] ?>
                </div>
                <div id="canceled-list" class="guest-list-container"></div>
            </div>
            <div>
                <div class="column-header pending">
                    <i class="fas fa-user-clock"></i>
                    <?= $t['awaiting_response'] ?>
                </div>
                <div id="pending-list" class="guest-list-container"></div>
            </div>
        </div>

        <div id="toast-notification" class="toast-notification">
            <i class="fas fa-bell"></i>
            <span id="toast-message"></span>
        </div>

        <div class="footer">
            <div class="wosuol-logo justify-center mb-4">
                <div class="wosuol-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="wosuol-text">وصول</div>
            </div>
            <p class="text-sm opacity-70">
                &copy; <?= date('Y') ?> <a href="https://wosuol.com" target="_blank" class="hover:opacity-80 font-medium">وصول - Wosuol.com</a> - جميع الحقوق محفوظة
            </p>
        </div>
    </div>
    <script>
        const eventId = <?= $event_id ?>;
    </script>
    <script src="js/dashboard.js"></script>
</body>
</html>
