<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['event_management'] ?> - wosuol.com</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/events.css">
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
            <h1 class="text-3xl font-bold"><?= $t['event_management'] ?></h1>
            <div class="header-buttons">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>"
                            class="btn">
                        <i class="fas fa-language"></i>
                        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                    </button>
                </form>
                <a href="backup_scheduler.php" class="btn">
                    <i class="fas fa-cog"></i> <?= $t['backup_scheduler'] ?>
                </a>
                <a href="backup_event.php" class="btn">
                    <i class="fas fa-archive"></i> <?= $t['backup_management'] ?>
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <?= $t['logout'] ?>
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="p-4 mb-6 text-sm rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="create-section">
            <h2 class="text-2xl font-bold mb-4"><?= $t['create_new_event'] ?></h2>
            <form method="POST" action="events.php" class="flex items-end gap-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="flex-grow">
                    <label for="event_name" class="block mb-2 font-medium"><?= $t['event_name'] ?>:</label>
                    <input type="text" id="event_name" name="event_name" required
                           class="w-full">
                </div>
                <button type="submit" name="create_event" class="btn">
                    <i class="fas fa-plus"></i>
                    <?= $t['create'] ?>
                </button>
            </form>
        </div>

        <div class="bulk-messaging-section">
            <h2 class="text-xl font-bold mb-3"><?= $t['bulk_messaging'] ?></h2>
            <p class="mb-4 opacity-80"><?= $t['bulk_messaging_description'] ?></p>
            <form method="POST" action="events.php" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_global_send'] ?>');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="messaging_action" value="global_send_all">
                <button type="submit" class="btn">
                    <i class="fas fa-rocket"></i>
                    <?= $t['global_send_all'] ?>
                </button>
            </form>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4 text-gray-700"><?= $t['current_events'] ?></h2>

            <?php if (empty($events)): ?>
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🎉</div>
                    <p class="text-xl text-gray-500 mb-6"><?= $t['no_events'] ?></p>
                    <p class="text-gray-400"><?= $t['create_first_event'] ?></p>
                </div>
            <?php else: ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card" data-event-id="<?= $event['id'] ?>">
                            <div class="loading-overlay" id="loading-<?= $event['id'] ?>">
                                <div class="text-center">
                                    <div class="spinner"></div>
                                    <p class="mt-2 text-gray-600"><?= $t['sending_messages'] ?></p>
                                </div>
                            </div>

                            <div class="event-header">
                                <div>
                                    <div class="flex items-center">
                                        <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                                        <?php if (!empty($event['n8n_initial_invite_webhook'])): ?>
                                            <span class="webhook-status webhook-configured">✓ Webhook</span>
                                        <?php else: ?>
                                            <span class="webhook-status webhook-missing">⚠ No Webhook</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="event-date"><?= htmlspecialchars($event['event_date_ar']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <?= $event['guest_count'] ?> <?= $lang === 'ar' ? 'ضيف' : 'guests' ?>, <?= $event['confirmed_count'] ?> <?= $lang === 'ar' ? 'مؤكد' : 'confirmed' ?>
                                    </p>
                                </div>
                                <div class="quick-copy">
                                    <button onclick="copyRegistrationLink(<?= $event['id'] ?>)" class="btn btn-small">
                                        <i class="fas fa-copy"></i> <?= $t['copy_link'] ?>
                                    </button>
                                    <div class="copy-tooltip" id="tooltip-<?= $event['id'] ?>"><?= $t['link_copied'] ?></div>
                                </div>
                            </div>

                            <div class="event-stats" id="stats-<?= $event['id'] ?>">
                                <div class="stat-item">
                                    <span class="stat-number" data-stat="total">0</span>
                                    <div class="stat-label"><?= $t['total_guests'] ?></div>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number" data-stat="confirmed">0</span>
                                    <div class="stat-label"><?= $t['confirmed_guests'] ?></div>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number" data-stat="pending">0</span>
                                    <div class="stat-label"><?= $t['pending_guests'] ?></div>
                                </div>
                            </div>

                            <div class="event-actions">
                                <a href="guests.php?event_id=<?= $event['id'] ?>" class="btn">
                                    <i class="fas fa-users"></i> <?= $t['manage_guests'] ?>
                                </a>
                                <a href="admin.php?event_id=<?= $event['id'] ?>" class="btn">
                                    <i class="fas fa-cog"></i> <?= $t['settings'] ?>
                                </a>
                                <a href="dashboard.php?event_id=<?= $event['id'] ?>" class="btn">
                                    <i class="fas fa-chart-bar"></i> <?= $t['dashboard'] ?>
                                </a>
                                <a href="send_invitations.php?event_id=<?= $event['id'] ?>" class="btn">
                                    <i class="fas fa-paper-plane"></i> <?= $t['send_invitations'] ?>
                                </a>
                                <a href="checkin.php?event_id=<?= $event['id'] ?>" class="btn">
                                    <i class="fas fa-check-circle"></i> <?= $t['checkin'] ?>
                                </a>
                                <a href="register.php?event_id=<?= $event['id'] ?>" target="_blank" class="btn">
                                    <i class="fas fa-link"></i> <?= $t['registration_link'] ?>
                                </a>
                            </div>

                            <div class="messaging-actions">
                                <?php if (!empty($event['n8n_initial_invite_webhook'])): ?>
                                    <form method="POST" action="events.php" style="flex: 1;" onsubmit="return handleSendSubmit(this, <?= $event['id'] ?>)">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="messaging_action" value="send_to_all">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" class="btn w-full">
                                            <i class="fas fa-bullhorn"></i> <?= $t['send_to_all'] ?>
                                        </button>
                                    </form>
                                    <button onclick="openGuestSelection(<?= $event['id'] ?>)" class="btn" style="flex: 1;">
                                        <i class="fas fa-bullseye"></i> <?= $t['send_to_selected'] ?>
                                    </button>
                                <?php else: ?>
                                    <div class="w-full text-center p-3 bg-yellow-100 text-yellow-800 rounded-lg text-sm">
                                        <?= $t['webhook_not_configured'] ?>
                                        <br>
                                        <a href="admin.php?event_id=<?= $event['id'] ?>" class="underline font-semibold">
                                            <?= $t['go_to_settings'] ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="send-results-section" id="send-results-<?= $event['id'] ?>">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-sm text-gray-700"><?= $t['last_send_results'] ?></h4>
                                    <button onclick="refreshSendResults(<?= $event['id'] ?>)" class="text-xs text-blue-600 hover:underline">
                                        <i class="fas fa-sync-alt"></i> <?= $t['refresh_results'] ?>
                                    </button>
                                </div>
                                <div id="results-content-<?= $event['id'] ?>">
                                    <p class="text-sm text-gray-500"><?= $t['no_send_history'] ?></p>
                                </div>
                            </div>

                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(45, 74, 34, 0.2);">
                                <form method="POST" action="events.php" onsubmit="return confirm('<?= $t['confirm_delete_event'] ?>');" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="delete_id" value="<?= $event['id'] ?>">
                                    <button type="submit" name="delete_event" class="btn btn-danger btn-small">
                                        <i class="fas fa-trash"></i> <?= $t['delete'] ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer mt-12 pt-8 border-t border-gray-200 text-center">
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

    <div id="guestSelectionModal" class="modal">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-6"><?= $t['select_guests_title'] ?></h2>

            <div class="mb-4">
                <input type="text" id="guest-search" placeholder="<?= $t['search_guests'] ?>"
                       class="w-full">
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2">
                    <button onclick="selectAllGuests()" class="btn btn-small"><?= $t['select_all'] ?></button>
                    <button onclick="clearGuestSelection()" class="btn btn-small"><?= $t['clear_selection'] ?></button>
                </div>
                <span id="selection-count" class="text-gray-600">0 <?= $t['guests_selected'] ?></span>
            </div>

            <div class="guest-list" id="guest-list">
                <div class="text-center p-8 text-gray-500"><?= $t['processing'] ?></div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button onclick="closeGuestSelection()" class="btn"><?= $t['cancel'] ?></button>
                <button onclick="sendToSelectedGuests()" class="btn" id="send-selected-btn" disabled>
                    <?= $t['send_selected'] ?>
                </button>
            </div>
        </div>
    </div>

    <script src="js/events.js"></script>
</body>
</html>
