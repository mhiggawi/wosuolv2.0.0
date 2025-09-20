<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $lang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title><?= $event_data ? htmlspecialchars($event_data['event_name']) : 'دعوة' ?></title>

    <meta name="description" content="<?= htmlspecialchars($event_data['event_paragraph_ar'] ?? 'دعوة خاصة') ?>">
    <meta name="keywords" content="دعوة,حفل,زفاف,invitation,wedding">

    <meta property="og:title" content="<?= htmlspecialchars($event_data['event_name'] ?? 'دعوة') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($event_data['event_paragraph_ar'] ?? 'دعوة خاصة') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($event_data['background_image_url'] ?? '') ?>">

    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <link rel="stylesheet" href="css/rsvp.css">

    <script>
        const loadQRLibrary = () => {
            if (!window.QRCode) {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js';
                document.head.appendChild(script);
                return new Promise(resolve => script.onload = resolve);
            }
            return Promise.resolve();
        };
    </script>

</head>
<body>
    <div class="card-container">
        <div class="language-toggle">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" name="switch_language" value="<?= $lang === 'ar' ? 'en' : 'ar' ?>">
                    <?= $lang === 'ar' ? 'English' : 'العربية' ?>
                </button>
            </form>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-container">
                <div class="error-icon">⚠</div>
                <h2 style="font-size:1.5rem;font-weight:bold;color:#374151;margin-bottom:1rem"><?= $t['invalid_link'] ?></h2>
                <p style="font-size:1rem;color:#6b7280"><?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php else: ?>

            <?php if (!empty($event_data['background_image_url'])): ?>
                <div class="event-image-container">
                    <img src="<?= htmlspecialchars($event_data['background_image_url']) ?>"
                         alt="<?= htmlspecialchars($event_data['event_name']) ?>"
                         class="event-image"
                         loading="lazy"
                         decoding="async">
                </div>
            <?php else: ?>
                <div class="description-box">
                    <p><?= nl2br(htmlspecialchars($event_data['event_paragraph_ar'] ?? 'مرحباً بكم في مناسبتنا الخاصة.')) ?></p>
                </div>
            <?php endif; ?>

            <div class="card-content" id="main-content">
                <div class="guest-welcome">
                    <h2><?= $t['welcome_guest'] ?></h2>
                    <p style="font-size:1.125rem;font-weight:600">
                        <?= htmlspecialchars($guest_data['name_ar'] ?? $t['dear_guest']) ?>
                    </p>
                </div>

                <?php if (!empty($event_data['rsvp_show_countdown'])): ?>
                <div class="countdown-section">
                    <h3 style="font-size:1.125rem;font-weight:bold;margin-bottom:10px">
                        <?= $t['countdown_title'] ?>
                    </h3>
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

                <div class="guest-details">
                    <?php if (!empty($event_data['rsvp_show_guest_count'])): ?>
                    <div class="detail-item">
                        <div class="detail-label"><?= $t['guest_count'] ?></div>
                        <div class="detail-value"><?= htmlspecialchars($guest_data['guests_count'] ?? '1') ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($guest_data['table_number'])): ?>
                    <div class="detail-item">
                        <div class="detail-label"><?= $t['table_number'] ?></div>
                        <div class="detail-value"><?= htmlspecialchars($guest_data['table_number']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($event_data['venue_ar']) || !empty($event_data['Maps_link'])): ?>
                <div class="location-card">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <h3 style="font-weight:bold;margin-bottom:5px">
                                <?= htmlspecialchars($event_data['venue_ar'] ?? 'مكان الحفل') ?>
                            </h3>
                            <?php
                            $display_date = '';
                            if ($lang === 'en' && !empty($event_data['event_date_en'])) {
                                $display_date = htmlspecialchars($event_data['event_date_en']);
                            } elseif ($lang === 'ar' && !empty($event_data['event_date_ar'])) {
                                try {
                                    $dateObj = new DateTime($event_data['event_date_ar']);
                                    $display_date = formatDateArabic($dateObj);
                                } catch (Exception $e) {
                                    $display_date = htmlspecialchars($event_data['event_date_ar']);
                                }
                            }
                            ?>
                            <?php if (!empty($display_date)): ?>
                            <p style="font-size:0.875rem">
                                <?= $display_date ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($event_data['Maps_link'])): ?>
                        <a href="<?= htmlspecialchars($event_data['Maps_link']) ?>"
                           target="_blank"
                           style="color:#2d4a22;font-size:1.25rem;transition:opacity 0.3s ease"
                           title="<?= $t['get_directions'] ?>">
                            ⚲
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div id="action-buttons-section" class="action-buttons">
                    <button id="confirm-button" onclick="handleRSVP('confirmed')">
                        <div class="spinner" id="confirm-spinner"></div>
                        <span id="confirm-text"><?= $t['confirm_attendance'] ?></span>
                    </button>
                    <button id="cancel-button" onclick="handleRSVP('canceled')">
                        <div class="spinner" id="cancel-spinner"></div>
                        <span id="cancel-text"><?= $t['decline_attendance'] ?></span>
                    </button>
                </div>

                <div id="response-message" style="display:none;margin-top:25px;padding:20px;border-radius:30px;text-align:center;font-weight:600"></div>

                <div class="share-buttons">
                    <button onclick="addToCalendar()" class="share-button">
                        <?= $t['add_to_calendar'] ?>
                    </button>

                    <button onclick="shareInvitation()" class="share-button">
                        <?= $t['share_invitation'] ?>
                    </button>

                    <?php if (!empty($event_data['Maps_link'])): ?>
                    <button onclick="openLocation()" class="share-button">
                        <?= $t['get_directions'] ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($event_data['rsvp_show_qr_code'])): ?>
            <div id="qr-code-section" class="qr-code-section">
                <div class="qr-grid">
                    <div class="qr-title-box">
                        <h3 style="font-size:1.25rem;font-weight:bold;margin-bottom:10px">
                            <?= htmlspecialchars($event_data['qr_card_title_ar'] ?? $t['entry_card']) ?>
                        </h3>
                        <p style="font-size:0.875rem"><?= $t['qr_code'] ?></p>
                    </div>

                    <?php if (!empty($event_data['rsvp_show_guest_count'])): ?>
                    <div class="qr-info qr-info-left">
                        <div style="text-align:center">
                            <div style="font-size:0.75rem;margin-bottom:5px"><?= $t['guest_count'] ?></div>
                            <div style="font-size:1.5rem;font-weight:bold"><?= htmlspecialchars($guest_data['guests_count'] ?? '1') ?></div>
                        </div>
                        <div style="font-size:0.75rem;margin-top:20px">
                            <?= htmlspecialchars($event_data['qr_brand_text_ar'] ?? 'وصول') ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="qrcode" class="qr-code-container"></div>

                    <div class="qr-info qr-info-right" style="text-align:center">
                        <p style="font-size:0.875rem;font-weight:600;margin-bottom:10px">
                            <?= htmlspecialchars($event_data['qr_show_code_instruction_ar'] ?? $t['show_at_entrance']) ?>
                        </p>
                        <div style="font-size:0.75rem">
                            <?= htmlspecialchars($event_data['qr_website'] ?? 'wosuol.com') ?>
                        </div>
                    </div>
                </div>

                <div class="share-buttons" style="margin-top:25px">
                    <button onclick="downloadQR()" class="share-button">
                        <?= $t['download_qr'] ?>
                    </button>

                    <button onclick="shareQR()" class="share-button">
                        <?= $t['share_invitation'] ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <div id="toast" class="toast">
        <div id="toast-message"></div>
    </div>

    <?php if (empty($error_message)): ?>
    <script>
        const CONFIG = {
            guestData: <?= json_encode($guest_data, JSON_UNESCAPED_UNICODE) ?>,
            eventData: <?= json_encode($event_data, JSON_UNESCAPED_UNICODE) ?>,
            texts: <?= json_encode($t, JSON_UNESCAPED_UNICODE) ?>,
            lang: '<?= $lang ?>',
            csrfToken: '<?= htmlspecialchars($_SESSION['csrf_token']) ?>',
            eventDateTimeISO: '<?= $event_datetime_iso ?>'
        };
    </script>
    <script src="js/rsvp.js"></script>
    <?php endif; ?>
</body>
</html>
