<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['reminder_management'] ?> - <?= safe_html($event['event_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/send_invitations.css">
</head>
<body>
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><?= $t['reminder_management'] ?></h1>
            <div class="flex gap-3 items-center">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg border border-gray-300 transition-colors">
                        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                    </button>
                </form>
                <a href="events.php" class="btn btn-secondary"><?= $t['back_to_events'] ?></a>
                <a href="logout.php" class="btn btn-danger"><?= $t['logout'] ?></a>
            </div>
        </div>

        <div class="reminder-card">
            <h2 class="text-2xl font-bold mb-4"><?= safe_html($event['event_name']) ?></h2>
            <p class="text-lg opacity-90"><?= $t['event_reminders'] ?></p>

            <?php if ($days_until_event !== null): ?>
            <div class="days-counter mt-4">
                <?php if ($days_until_event > 0): ?>
                    <div class="days-number"><?= $days_until_event ?></div>
                    <div class="text-lg"><?= $t['days_until_event'] ?></div>
                <?php elseif ($days_until_event === 0): ?>
                    <div class="days-number">🎉</div>
                    <div class="text-lg"><?= $t['event_is_today'] ?></div>
                <?php else: ?>
                    <div class="text-lg text-red-200"><?= $t['event_date_passed'] ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="p-4 mb-6 text-sm rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number text-blue-600"><?= $stats['total_guests'] ?></div>
                <div class="stat-label">إجمالي الضيوف</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-green-600"><?= $stats['confirmed_guests'] ?></div>
                <div class="stat-label">مؤكدين</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-yellow-600"><?= $stats['pending_guests'] ?></div>
                <div class="stat-label"><?= $t['pending_guests'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-red-600"><?= $stats['canceled_guests'] ?></div>
                <div class="stat-label">معتذرين</div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="text-xl font-bold mb-4"><?= $t['reminder_settings'] ?></h3>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="save_reminder_settings" value="1">

                <div class="image-section <?= !empty($event['reminder_image_url']) ? 'has-image' : '' ?>">
                    <h4 class="font-bold text-lg mb-4"><?= $t['reminder_image'] ?></h4>

                    <?php if(!empty($event['reminder_image_url'])): ?>
                        <div class="my-4 p-4 border rounded-lg bg-gray-50">
                            <p class="font-semibold mb-2"><?= $t['current_reminder_image'] ?>:</p>
                            <img src="<?= safe_html($event['reminder_image_url']) ?>" alt="<?= $t['current_reminder_image'] ?>" class="image-preview">
                            <div class="mt-3">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="remove_reminder_image" value="1" class="mx-2">
                                    <?= $t['remove_reminder_image'] ?>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2">
                         <label class="block font-medium"><?= $t['upload_reminder_image'] ?>:</label>
                         <input type="file" name="reminder_image_upload" accept="image/*" class="mt-1">
                         <p class="text-sm text-gray-600 mt-1">حد أقصى: 5MB، الأنواع المدعومة: JPG, PNG, GIF, WebP</p>
                    </div>
                    <input type="hidden" name="current_reminder_image" value="<?= safe_html($event['reminder_image_url']) ?>">
                </div>

                <button type="submit" class="btn btn-success"><?= $t['save_settings'] ?></button>
            </form>
        </div>

        <div class="form-section">
            <h3 class="text-xl font-bold mb-4"><?= $t['quick_reminder'] ?></h3>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="send_reminder" value="1">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium mb-2"><?= $t['reminder_type'] ?>:</label>
                        <select name="reminder_type" class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="pending_only"><?= $t['send_to_pending_only'] ?></option>
                            <option value="all_guests"><?= $t['send_to_all_guests'] ?></option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-2"><?= $t['reminder_image'] ?>:</label>
                        <select name="reminder_image_option" class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="event_image"><?= $t['use_event_image'] ?></option>
                            <?php if (!empty($event['reminder_image_url'])): ?>
                            <option value="reminder_image">استخدام صورة التذكير</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-medium mb-2"><?= $t['reminder_message'] ?>:</label>
                    <textarea name="custom_message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg"
                              placeholder="<?= $t['custom_message_placeholder'] ?>"></textarea>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        <?= $t['send_reminders'] ?>
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($reminder_logs)): ?>
        <div class="form-section">
            <h3 class="text-xl font-bold mb-4"><?= $t['reminder_history'] ?></h3>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= $t['reminder_type'] ?></th>
                            <th><?= $t['reminder_sent_at'] ?></th>
                            <th><?= $t['status'] ?></th>
                            <th><?= $t['view_details'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reminder_logs as $log): ?>
                        <tr>
                            <td>
                                <?php
                                echo $log['reminder_type'] === 'pending_only' ? $t['send_to_pending_only'] : $t['send_to_all_guests'];
                                ?>
                            </td>
                            <td><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
                            <td>
                                <?php if ($log['http_code'] >= 200 && $log['http_code'] < 300): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">نجح</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">فشل</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="showReminderDetails(<?= htmlspecialchars(json_encode($log), ENT_QUOTES) ?>)"
                                        class="text-blue-600 hover:underline">
                                    <?= $t['view_details'] ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
            <h3 class="text-lg font-bold mb-4">تفاصيل التذكير</h3>
            <div id="modalContent"></div>
            <button onclick="closeModal()" class="mt-4 btn btn-secondary">إغلاق</button>
        </div>
    </div>

    <script src="js/send_invitations.js"></script>
</body>
</html>
