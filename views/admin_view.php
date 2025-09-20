<?php require_once __DIR__ . '/layouts/admin_header.php'; ?>

<div class="container">
    <div class="wosuol-logo">
        <div class="wosuol-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="wosuol-text">وصول</div>
    </div>

    <div class="page-header">
         <h1 class="text-3xl font-bold"><?= $t['administration'] ?>: "<?= htmlspecialchars($event['event_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"</h1>
         <div class="header-buttons">
             <form method="POST" style="display: inline;">
                 <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>"
                         class="btn">
                     <i class="fas fa-language"></i>
                     <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                 </button>
             </form>
             <a href="events.php" class="btn">
                <i class="fas fa-arrow-right"></i>
                 <?= $lang === 'ar' ? 'العودة' : 'Back' ?>
             </a>
             <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i>
                 <?= $t['logout'] ?>
             </a>
         </div>
    </div>

    <nav class="main-nav">
        <a href="events.php" class="text-blue-600"><i class="fas fa-home"></i> <?= $lang === 'ar' ? 'كل الحفلات' : 'All Events' ?></a>
        <a href="dashboard.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-chart-bar"></i> <?= $lang === 'ar' ? 'متابعة' : 'Dashboard' ?></a>
        <a href="guests.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-users"></i> <?= $lang === 'ar' ? 'إدارة الضيوف' : 'Manage Guests' ?></a>
        <a href="send_invitations.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-paper-plane"></i> <?= $lang === 'ar' ? 'إرسال الدعوات' : 'Send Invitations' ?></a>
        <a href="checkin.php?event_id=<?= $event_id ?>" class="text-blue-600"><i class="fas fa-check-circle"></i> <?= $lang === 'ar' ? 'تسجيل الدخول' : 'Check-in' ?></a>
        <a href="register.php?event_id=<?= $event_id ?>" target="_blank" class="text-green-600 font-bold"><i class="fas fa-link"></i> <?= $lang === 'ar' ? 'عرض صفحة التسجيل' : 'View Registration Page' ?></a>
        <a href="rsvp.php?id=GUEST_ID_HERE" target="_blank" class="text-purple-600 font-bold"><i class="fas fa-qrcode"></i> <?= $lang === 'ar' ? 'عرض صفحة تأكيد الحضور' : 'View RSVP Page' ?></a>
    </nav>

    <?php if ($message): ?>
        <div class="message-box <?= $messageType === 'success' ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-button active" data-tab="general-settings"><i class="fas fa-cog"></i> <?= $t['event_settings'] ?></button>
        <button class="tab-button" data-tab="user-management"><i class="fas fa-user-shield"></i> <?= $t['user_management'] ?></button>
    </div>

    <div id="general-settings" class="tab-content active">
        <?php require_once __DIR__ . '/partials/admin_settings.php'; ?>
    </div>

    <div id="user-management" class="tab-content">
        <?php require_once __DIR__ . '/partials/admin_users.php'; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/admin_footer.php'; ?>
