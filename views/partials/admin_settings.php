<form id="event-settings-form" method="POST" action="admin.php?event_id=<?= $event_id ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="update_event_settings" value="1">

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['event_details'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content" style="display: block;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label><?= $t['event_name'] ?>:</label>
                <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label><?= $t['event_name_en'] ?>:</label>
                <input type="text" name="event_name_en" value="<?= htmlspecialchars($event['event_name_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label><?= $t['event_slug'] ?>:</label>
                <input type="text" name="event_slug" value="<?= htmlspecialchars($event['event_slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label><?= $t['google_maps_link'] ?>:</label>
                <input type="url" name="maps_link" value="<?= htmlspecialchars($event['Maps_link'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label><?= $t['event_date_time'] ?>:</label>
                <input type="datetime-local" name="event_datetime" value="<?= htmlspecialchars($datetime_value) ?>" required>
            </div>
            <div class="form-group">
                <label><?= $t['venue_ar'] ?>:</label>
                <input type="text" name="venue_ar" value="<?= htmlspecialchars($event['venue_ar'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label><?= $t['venue_en'] ?>:</label>
                <input type="text" name="venue_en" value="<?= htmlspecialchars($event['venue_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <div class="form-group">
            <label><?= $t['event_description_ar'] ?>:</label>
            <textarea name="event_paragraph_ar" rows="4"><?= htmlspecialchars($event['event_paragraph_ar'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
        <div class="form-group">
            <label><?= $t['event_description_en'] ?>:</label>
            <textarea name="event_paragraph_en" rows="4"><?= htmlspecialchars($event['event_paragraph_en'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
    </div>

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['image_settings'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content">
        <div class="image-section <?= !empty($event['background_image_url']) ? 'has-image' : '' ?>">
            <h4 class="font-bold text-lg mb-4 text-blue-800">
                <i class="fas fa-desktop"></i>
                <?= $t['display_image'] ?>
            </h4>
            <p class="text-sm text-gray-600 mb-4">هذه الصورة ستظهر في صفحة RSVP وصفحة التسجيل على الموقع</p>

            <?php if(!empty($event['background_image_url'])): ?>
                <div class="my-4 p-4 border rounded-lg bg-gray-50">
                    <p class="font-semibold mb-2"><?= $t['current_display_image'] ?>:</p>
                    <img src="<?= htmlspecialchars($event['background_image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= $t['current_display_image'] ?>" class="image-preview">
                    <div class="mt-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remove_display_image" value="1" class="mx-2" onchange="toggleImageUpload(this, 'display')">
                            <?= $t['remove_current_image'] ?>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div id="display-image-upload-section" class="mt-2">
                 <label class="block font-medium"><?= $t['upload_display_image'] ?>:</label>
                 <input type="file" id="display_image_upload" name="display_image_upload" accept="image/*" class="mt-1" onchange="previewNewImage(this, 'display')">
                 <p class="text-sm text-gray-600 mt-1">حد أقصى: 5MB، الأنواع المدعومة: JPG, PNG, GIF, WebP</p>
            </div>

            <div id="display-image-preview-container" class="my-2" style="display: none;">
                 <p class="font-semibold"><?= $t['image_preview'] ?>:</p>
                 <img id="display-image-preview" src="#" alt="<?= $t['image_preview'] ?>" class="image-preview">
                 <button type="button" class="mt-2 text-sm text-red-600 hover:underline" onclick="cancelImageSelection('display')"><?= $t['cancel_selection'] ?></button>
            </div>
            <input type="hidden" name="current_display_image" value="<?= htmlspecialchars($event['background_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="image-section <?= !empty($event['whatsapp_image_url']) ? 'has-image' : '' ?>">
            <h4 class="font-bold text-lg mb-4 text-green-800">
                <i class="fab fa-whatsapp"></i>
                <?= $t['whatsapp_image'] ?>
            </h4>
            <p class="text-sm text-gray-600 mb-4">هذه الصورة ستُرسل مع رسائل الدعوة عبر الواتساب</p>

            <?php if(!empty($event['whatsapp_image_url'])): ?>
                <div class="my-4 p-4 border rounded-lg bg-gray-50">
                    <p class="font-semibold mb-2"><?= $t['current_whatsapp_image'] ?>:</p>
                    <img src="<?= htmlspecialchars($event['whatsapp_image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= $t['current_whatsapp_image'] ?>" class="image-preview">
                    <div class="mt-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remove_whatsapp_image" value="1" class="mx-2" onchange="toggleImageUpload(this, 'whatsapp')">
                            <?= $t['remove_current_image'] ?>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div id="whatsapp-image-upload-section" class="mt-2">
                 <label class="block font-medium"><?= $t['upload_whatsapp_image'] ?>:</label>
                 <input type="file" id="whatsapp_image_upload" name="whatsapp_image_upload" accept="image/*" class="mt-1" onchange="previewNewImage(this, 'whatsapp')">
                 <p class="text-sm text-gray-600 mt-1">حد أقصى: 5MB، الأنواع المدعومة: JPG, PNG, GIF, WebP</p>
            </div>

            <div id="whatsapp-image-preview-container" class="my-2" style="display: none;">
                 <p class="font-semibold"><?= $t['image_preview'] ?>:</p>
                 <img id="whatsapp-image-preview" src="#" alt="<?= $t['image_preview'] ?>" class="image-preview">
                 <button type="button" class="mt-2 text-sm text-red-600 hover:underline" onclick="cancelImageSelection('whatsapp')"><?= $t['cancel_selection'] ?></button>
            </div>
            <input type="hidden" name="current_whatsapp_image" value="<?= htmlspecialchars($event['whatsapp_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['qr_settings'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label>عنوان بطاقة QR (عربي):</label>
                <input type="text" name="qr_card_title_ar" value="<?= htmlspecialchars($event['qr_card_title_ar'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>عنوان بطاقة QR (إنجليزي):</label>
                <input type="text" name="qr_card_title_en" value="<?= htmlspecialchars($event['qr_card_title_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>تعليمات إظهار الكود (عربي):</label>
                <input type="text" name="qr_show_code_instruction_ar" value="<?= htmlspecialchars($event['qr_show_code_instruction_ar'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>تعليمات إظهار الكود (إنجليزي):</label>
                <input type="text" name="qr_show_code_instruction_en" value="<?= htmlspecialchars($event['qr_show_code_instruction_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>نص العلامة التجارية (عربي):</label>
                <input type="text" name="qr_brand_text_ar" value="<?= htmlspecialchars($event['qr_brand_text_ar'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>نص العلامة التجارية (إنجليزي):</label>
                <input type="text" name="qr_brand_text_en" value="<?= htmlspecialchars($event['qr_brand_text_en'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>موقع الويب على البطاقة:</label>
                <input type="text" name="qr_website" value="<?= htmlspecialchars($event['qr_website'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
    </div>

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['webhook_settings'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label>Webhook لتأكيد الحضور:</label>
                <input type="url" name="n8n_confirm_webhook" value="<?= htmlspecialchars($event['n8n_confirm_webhook'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Webhook للدعوات الأولية:</label>
                <input type="url" name="n8n_initial_invite_webhook" value="<?= htmlspecialchars($event['n8n_initial_invite_webhook'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
    </div>

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['registration_settings'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="registration_show_phone" value="1"
                           <?= ($event['registration_show_phone'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['registration_show_phone'] ?>
                </label>
            </div>
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="registration_require_phone" value="1"
                           <?= ($event['registration_require_phone'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['registration_require_phone'] ?>
                </label>
            </div>
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="registration_show_guest_count" value="1"
                           <?= ($event['registration_show_guest_count'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['registration_show_guest_count'] ?>
                </label>
            </div>
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="registration_show_countdown" value="1"
                           <?= ($event['registration_show_countdown'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['registration_show_countdown'] ?>
                </label>
            </div>
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="registration_show_location" value="1"
                           <?= ($event['registration_show_location'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['registration_show_location'] ?>
                </label>
            </div>
            <div class="form-group">
                <label><?= $t['registration_mode'] ?>:</label>
                <select name="registration_mode">
                    <option value="full" <?= ($event['registration_mode'] ?? 'full') === 'full' ? 'selected' : '' ?>><?= $t['registration_mode_full'] ?></option>
                    <option value="simple" <?= ($event['registration_mode'] ?? 'full') === 'simple' ? 'selected' : '' ?>><?= $t['registration_mode_simple'] ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="accordion-header" onclick="toggleAccordion(this)"><?= $t['rsvp_settings'] ?> <span class="toggle-icon">▼</span></div>
    <div class="accordion-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="rsvp_show_guest_count" value="1"
                           <?= ($event['rsvp_show_guest_count'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['rsvp_show_guest_count'] ?>
                </label>
            </div>
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="rsvp_show_qr_code" value="1"
                           <?= ($event['rsvp_show_qr_code'] ?? 1) ? 'checked' : '' ?> class="mx-2">
                    <?= $t['rsvp_show_qr_code'] ?>
                </label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-blue mt-6">
        <i class="fas fa-save"></i>
        <?= $t['save_all_settings'] ?>
    </button>
</form>
