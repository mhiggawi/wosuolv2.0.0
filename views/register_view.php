<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حضور: <?= safe_html($event['event_name']) ?></title>

    <meta name="description" content="<?= safe_html(($lang === 'en' && !empty($event['event_paragraph_en'])) ? $event['event_paragraph_en'] : ($event['event_paragraph_ar'] ?? 'دعوة خاصة')) ?>">
    <meta name="keywords" content="دعوة,حفل,زفاف,invitation,wedding">

    <meta property="og:title" content="<?= safe_html(($lang === 'en' && !empty($event['event_name_en'])) ? $event['event_name_en'] : ($event['event_name'] ?? 'دعوة')) ?>">
    <meta property="og:description" content="<?= safe_html(($lang === 'en' && !empty($event['event_paragraph_en'])) ? $event['event_paragraph_en'] : ($event['event_paragraph_ar'] ?? 'دعوة خاصة')) ?>">
    <meta property="og:image" content="<?= safe_html($event['background_image_url'] ?? '') ?>">

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="css/register.css">

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap"></noscript>

</head>
<body>
    <div class="card-container">
        <div class="language-toggle">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= safe_html($_SESSION['csrf_token']) ?>">
                <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>">
                    <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                </button>
            </form>
        </div>

        <?php
        $event_image_url = $event['background_image_url'] ?? '';
        $event_paragraph = ($lang === 'en' && !empty($event['event_paragraph_en'])) ? $event['event_paragraph_en'] : ($event['event_paragraph_ar'] ?? 'مرحباً بكم في مناسبتنا الخاصة.');
        ?>
        <?php if (!empty($event_image_url)): ?>
            <div class="event-image-container">
                <img src="<?= safe_html($event_image_url) ?>"
                     alt="<?= safe_html(($lang === 'en' && !empty($event['event_name_en'])) ? $event['event_name_en'] : ($event['event_name'] ?? 'دعوة')) ?>"
                     class="event-image"
                     loading="lazy"
                     decoding="async">
            </div>
        <?php else: ?>
            <div class="description-box">
                <p><?= nl2br(safe_html($event_paragraph)) ?></p>
            </div>
        <?php endif; ?>

        <div class="card-content">
            <div class="event-header">
                <?php
                $event_title = ($lang === 'en' && !empty($event['event_name_en'])) ? $event['event_name_en'] : ($event['event_name'] ?? 'دعوة');

                $event_date_display = '';
                if ($lang === 'en' && !empty($event['event_date_en'])) {
                    $event_date_display = $event['event_date_en'];
                } elseif ($lang === 'ar' && !empty($event['event_date_ar'])) {
                    try {
                        $dateObj = new DateTime($event['event_date_ar']);
                        $event_date_display = formatDateArabic($dateObj);
                    } catch (Exception $e) {
                        $event_date_display = $event['event_date_ar']; // Fallback
                    }
                } elseif (!empty($event['event_date_ar'])) {
                    $event_date_display = $event['event_date_ar'];
                }
                ?>
                <h1><?= safe_html($event_title) ?></h1>
                <?php if (!empty($event_date_display)): ?>
                <p><?= nl2br(safe_html($event_date_display)) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($show_countdown): ?>
            <div class="countdown-section">
                <h3><?= $t['countdown_title'] ?></h3>
                <div class="countdown-timer" id="countdown-timer">
                    <div class="countdown-item">
                        <span class="countdown-number" id="days">--</span>
                        <div class="countdown-label"><?= $t['days'] ?></div>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="hours">--</span>
                        <div class="countdown-label"><?= $t['hours'] ?></div>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="minutes">--</span>
                        <div class="countdown-label"><?= $t['minutes'] ?></div>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="seconds">--</span>
                        <div class="countdown-label"><?= $t['seconds'] ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($show_location && (!empty($event['venue_ar']) || !empty($event['Maps_link']))): ?>
            <div class="location-card">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div>
                        <h3 style="margin-bottom:5px"><?= $t['event_location'] ?></h3>
                        <?php if (!empty($event['venue_ar'])): ?>
                        <p><?= safe_html($event['venue_ar']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($event['Maps_link'])): ?>
                    <a href="<?= safe_html($event['Maps_link']) ?>"
                       target="_blank"
                       style="color:#2d4a22;transition:opacity 0.3s ease"
                       title="<?= $t['get_directions'] ?>">
                        ⚲
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!$registration_successful): ?>
                <div class="form-section">
                    <p style="text-align:center;margin-bottom:25px;font-weight:500"><?= $t['registration_instruction'] ?></p>

                    <form id="rsvpForm" method="POST" action="register.php?event_id=<?= $event_id ?>" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= safe_html($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="rsvp_status" id="rsvp_status" value="confirmed">

                        <div class="form-group">
                            <label for="name_ar"><?= $t['name_label'] ?></label>
                            <input type="text" id="name_ar" name="name_ar" required
                                   value="<?= safe_html($_POST['name_ar'] ?? '') ?>"
                                   placeholder="<?= safe_html($t['placeholder_name']) ?>">
                        </div>

                        <?php if ($show_phone): ?>
                        <div class="form-group">
                            <label for="country_code"><?= $t['phone_label'] ?></label>
                            <div class="phone-input-container">
                                <select id="country_code" name="country_code" <?= $require_phone ? 'required' : '' ?> onchange="updatePhonePlaceholder()">
                                    <option value=""><?= $t['select_country'] ?></option>
                                    <?php foreach ($t['countries'] as $code => $name): ?>
                                        <option value="<?= safe_html($code) ?>"
                                                <?= (($_POST['country_code'] ?? '+962') === $code) ? 'selected' : '' ?>>
                                            <?= safe_html($name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="tel" id="phone_number" name="phone_number" <?= $require_phone ? 'required' : '' ?> placeholder="791234567"
                                       value="<?= safe_html($_POST['phone_number'] ?? '') ?>">
                            </div>
                            <div class="help-text" id="phone-help-text"><?= $t['enter_local_number'] ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($show_guest_count): ?>
                        <div class="form-group">
                            <label for="guests_count"><?= $t['guests_count_label'] ?></label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" id="guests_count" name="guests_count"
                                   value="<?= safe_html($_POST['guests_count'] ?? '') ?>" placeholder="<?= safe_html($t['guests_count_placeholder']) ?>" min="1" max="20" required>
                        </div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <button type="submit" onclick="document.getElementById('rsvp_status').value='confirmed';"
                                    class="btn-confirm">
                                <?= $t['confirm_attendance'] ?>
                            </button>
                            <button type="submit" onclick="document.getElementById('rsvp_status').value='canceled';"
                                    class="btn-decline">
                                <?= $t['decline_attendance'] ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="successModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">✓</div>
            <div class="success-title" id="successTitle">تم تأكيد حضورك بنجاح!</div>
            <div class="success-message" id="successMessage">سيتم الآن نقلك لصفحة الدعوة الخاصة بك للحصول على QR Code.</div>
            <button class="success-button" onclick="proceedToInvitation()">
                المتابعة للدعوة
            </button>
        </div>
    </div>

    <?php if ($message && $messageType === 'error'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert("<?= addslashes(safe_html($message)) ?>");
        });
    </script>
    <?php endif; ?>

    <script src="js/register.js"></script>
</body>
</html>
