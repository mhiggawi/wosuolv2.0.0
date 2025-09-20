const eventId = "<?php echo $event_id; ?>";
const language = "<?php echo $lang; ?>";
const translations = <?php echo json_encode($translations, JSON_UNESCAPED_UNICODE); ?>[language];

document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Tooltip(document.body, { selector: "[data-bs-toggle='tooltip']" });

    const bulkActionSelect = document.getElementById('bulkActionSelect');
    if(bulkActionSelect) {
        bulkActionSelect.addEventListener('change', function() {
            const tableInputContainer = document.getElementById('bulkTableInputContainer');
            tableInputContainer.style.display = (this.value === 'assign_table') ? 'block' : 'none';
        });
    }

    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const checkboxes = document.querySelectorAll('.guest-checkbox');

    function syncCheckboxes(master) {
        const isChecked = master.checked;
        checkboxes.forEach(cb => cb.checked = isChecked);
        if (selectAll) selectAll.checked = isChecked;
        if (selectAllHeader) selectAllHeader.checked = isChecked;
    }

    if(selectAll) selectAll.addEventListener('change', () => syncCheckboxes(selectAll));
    if(selectAllHeader) selectAllHeader.addEventListener('change', () => syncCheckboxes(selectAllHeader));

    const bulkForm = document.getElementById('bulkForm');
    if(bulkForm){
        bulkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = this.querySelector('#bulkActionSelect').value;
            if (!action) {
                Swal.fire(translations.import_error, translations.bulk_action, 'warning');
                return;
            }
            if (this.querySelectorAll('.guest-checkbox:checked').length === 0) {
                Swal.fire(translations.import_error, translations.no_selection_error, 'warning');
                return;
            }
            if (action === 'assign_table' && !this.querySelector('[name="bulk_table_number"]').value) {
                Swal.fire(translations.import_error, translations.no_table_error, 'warning');
                return;
            }
            Swal.fire({
                title: translations.confirm_bulk_action,
                text: translations.confirm_bulk_text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: translations.execute_now,
                cancelButtonText: translations.cancel
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        });
    }

    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const guestId = this.dataset.guestId;
            const newStatus = this.value;
            const selectElement = this;

            selectElement.disabled = true;

            const formData = new FormData();
            formData.append('quick_status_update', '1');
            formData.append('guest_id', guestId);
            formData.append('new_status', newStatus);

            fetch('guests_api.php?event_id=' + eventId, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = selectElement.closest('tr');
                    row.style.transition = 'background-color 0.5s';
                    row.style.backgroundColor = '#d1e7dd';
                    setTimeout(() => { row.style.backgroundColor = ''; }, 1500);
                    selectElement.dataset.currentStatus = newStatus;
                } else {
                    Swal.fire(translations.import_error, data.message || translations.update_failed, 'error');
                    selectElement.value = selectElement.dataset.currentStatus;
                }
            })
            .catch(() => Swal.fire(translations.import_error, translations.network_error, 'error'))
            .finally(() => { selectElement.disabled = false; });
        });
    });
});

function editGuest(guest) {
    const modal = new bootstrap.Modal(document.getElementById('editGuestModal'));
    document.getElementById('edit_guest_id').value = guest.guest_id;
    document.getElementById('edit_name_ar').value = guest.name_ar;
    document.getElementById('edit_phone_number').value = guest.phone_number || '';
    document.getElementById('edit_guests_count').value = guest.guests_count;
    document.getElementById('edit_table_number').value = guest.table_number || '';
    document.getElementById('edit_status').value = guest.status;
    document.getElementById('edit_assigned_location').value = guest.assigned_location || '';
    document.getElementById('edit_notes').value = guest.notes || '';
    modal.show();
}

function quickCheckin(guestId) {
    Swal.fire({
        title: translations.confirm_checkin,
        text: translations.confirm_checkin_text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: translations.yes,
        cancelButtonText: translations.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('bulkForm');
            form.querySelector('#bulkActionSelect').value = 'checkin';
            form.querySelectorAll('.guest-checkbox').forEach(cb => cb.checked = false);
            form.querySelector(`.guest-checkbox[value="${guestId}"]`).checked = true;
            form.submit();
        }
    });
}

function deleteGuest(guestId, guestName) {
     Swal.fire({
        title: translations.confirm_delete,
        text: `${translations.confirm_delete_text} "${guestName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: translations.yes,
        cancelButtonText: translations.cancel
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="delete_guest" value="1"><input type="hidden" name="guest_id" value="${guestId}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function exportGuests() {
    const guestsData = <?php echo json_encode($guests, JSON_UNESCAPED_UNICODE); ?>;
    if (!guestsData.length) {
        Swal.fire(translations.no_data_to_export, '', 'info');
        return;
    }
    const exportData = guestsData.map(g => ({
        [translations.name_col]: g.name_ar,
        [translations.phone_col]: g.phone_number,
        [translations.count_col]: g.guests_count,
        [translations.table_col]: g.table_number,
        [translations.status_col]: g.status
    }));
    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, translations.guests_plural);
    XLSX.writeFile(wb, `guests_<?php echo str_replace(' ', '_', $event['event_name']); ?>.xlsx`);
}

let previewData = [];
const importFileInput = document.getElementById('importFile');
if(importFileInput) {
    importFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            const ws = workbook.Sheets[workbook.SheetNames[0]];
            previewData = XLSX.utils.sheet_to_json(ws, {header: 1});
            displayPreview(previewData);
            document.getElementById('importBtn').disabled = false;
        };
        reader.readAsArrayBuffer(file);
    });
}

function displayPreview(data) {
    const container = document.getElementById('previewContainer');
    const headers = data[0] || [];
    const rows = data.slice(1);
    const requiredHeaders = ['name_ar', 'اسم الضيف'];
    const hasRequiredHeader = headers.some(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')));

    let validRows = 0;
    if (hasRequiredHeader) {
        validRows = rows.filter(row => row[headers.findIndex(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')))] && row[headers.findIndex(h => requiredHeaders.includes(h.toString().trim().toLowerCase().replace(/[`’]/g, '')))].toString().trim() !== '').length;
    }

    if (document.getElementById('totalRows')) {
        document.getElementById('totalRows').textContent = rows.length;
        document.getElementById('validRows').textContent = validRows;
        document.getElementById('invalidRows').textContent = rows.length - validRows;
        document.getElementById('importStats').classList.remove('d-none');
    }

    let html = `<div class="table-responsive"><table class="table table-sm"><thead><tr>`;
    headers.forEach(header => { html += `<th>${header}</th>`; });
    html += '</tr></thead><tbody>';
    rows.slice(0, 5).forEach(row => {
        html += '<tr>';
        row.forEach(cell => { html += `<td>${cell || ''}</td>`; });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

const importBtn = document.getElementById('importBtn');
if(importBtn){
    importBtn.addEventListener('click', function() {
        if (previewData.length < 2) {
            Swal.fire(translations.import_error, translations.invalid_import_data, 'warning');
            return;
        }
        Swal.fire({
            title: translations.confirm_import,
            text: `${translations.confirm_import_text} ${previewData.length - 1} ${translations.guests_plural}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: translations.yes,
            cancelButtonText: translations.cancel
        }).then((result) => {
            if (result.isConfirmed) importGuests();
        });
    });
}

function importGuests() {
    Swal.fire({ title: translations.importing, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    const headers = previewData[0].map(h => h.toString().trim());
    const rows = previewData.slice(1);
    const guestsToImport = rows.map(row => {
        let guest = {};
        const fieldMap = { 'name_ar': 'name_ar', 'اسم الضيف': 'name_ar', 'phone_number': 'phone_number', 'رقم الهاتف': 'phone_number', 'guests_count': 'guests_count', 'عدد الأشخاص': 'guests_count', 'table_number': 'table_number', 'رقم الطاولة': 'رقم الطاولة', 'assigned_location': 'assigned_location', 'الموقع': 'الموقع', 'notes': 'notes', 'ملاحظات': 'ملاحظات' };
        headers.forEach((header, i) => {
            const key = fieldMap[header];
            if (key) guest[key] = row[i];
        });
        return guest;
    });

    fetch('import_guests.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId, guests: guestsToImport })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire(translations.import_success, data.message, 'success').then(() => location.reload());
        } else {
            throw new Error(data.message);
        }
    })
    .catch(err => Swal.fire(translations.import_error, err.message, 'error'));
}

const downloadTemplateBtn = document.getElementById('downloadTemplate');
if(downloadTemplateBtn){
    downloadTemplateBtn.addEventListener('click', function() {
        const templateData = [['name_ar', 'phone_number', 'guests_count', 'table_number', 'assigned_location', 'notes']];
        const ws = XLSX.utils.aoa_to_sheet(templateData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, translations.download_template);
        XLSX.writeFile(wb, 'guest_import_template.xlsx');
    });
}

const messageTemplate = document.getElementById('messageTemplate');
const saveTemplateBtn = document.getElementById('saveTemplateBtn');
const sendInvitationsModal = document.getElementById('sendInvitationsModal');

if (sendInvitationsModal) {
    sendInvitationsModal.addEventListener('show.bs.modal', function () {
        fetch(`guests_api.php?event_id=${eventId}&action=get_template`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.template) {
                    messageTemplate.value = data.template;
                } else {
                    messageTemplate.value = translations[`default_message_${language}`];
                }
            });
    });
}

if (saveTemplateBtn) {
    saveTemplateBtn.addEventListener('click', function() {
        const templateContent = messageTemplate.value;
        const formData = new FormData();
        formData.append('event_id', eventId);
        formData.append('action', 'save_template');
        formData.append('template_content', templateContent);
        formData.append('language', language);

        fetch(`guests_api.php?event_id=${eventId}`, {
        method: 'POST',
        body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(error => { throw new Error(error.message || 'Network response was not ok'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحفظ',
                        text: 'تم حفظ قالب الرسالة بنجاح.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    bootstrap.Modal.getInstance(sendInvitationsModal).hide();
                } else {
                    Swal.fire('خطأ', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('خطأ', `حدث خطأ: ${error.message}`, 'error');
            });
    });
}

document.querySelectorAll('.variable-tag').forEach(tag => {
    tag.addEventListener('click', function() {
        const variable = this.dataset.variable;
        const start = messageTemplate.selectionStart;
        const end = messageTemplate.selectionEnd;
        const text = messageTemplate.value;
        messageTemplate.value = text.substring(0, start) + variable + text.substring(end);
        messageTemplate.focus();
        messageTemplate.setSelectionRange(start + variable.length, start + variable.length);
    });
});

function generateInvitationLink(guestId) {
    const protocol = window.location.protocol;
    const host = window.location.host;
    return `${protocol}//${host}/rsvp.php?id=${guestId}`;
}

function prepareMessage(guestName, guestsCount, tableNumber, guestId) {
    const template = messageTemplate.value;
    const link = generateInvitationLink(guestId);
    const eventLocationLink = "<?php echo htmlspecialchars($event['Maps_link']); ?>";
    const venueName = "<?php echo htmlspecialchars($event['venue_' . $lang]); ?>";

    let locationText = '';
    if (venueName && eventLocationLink) {
        locationText = `${venueName} - ${eventLocationLink}`;
    } else if (venueName) {
        locationText = venueName;
    } else if (eventLocationLink) {
        locationText = eventLocationLink;
    } else {
        locationText = '-';
    }

    return template
        .replace(/\(guest_name\)/g, guestName)
        .replace(/\(guests_count\)/g, guestsCount)
        .replace(/\(table_number\)/g, tableNumber || '-')
        .replace(/\(invitation_link\)/g, link)
        .replace(/\(event_location_link\)/g, locationText);
}

function sendWhatsApp(phoneNumber, guestName, guestsCount, tableNumber, guestId) {
    if (!phoneNumber || phoneNumber.trim() === '-' || phoneNumber.trim() === '') {
        Swal.fire(translations.import_error, translations.invalid_phone, 'warning');
        return;
    }

    const message = prepareMessage(guestName, guestsCount, tableNumber, guestId);
    const encodedMessage = encodeURIComponent(message);

    let cleanPhoneNumber = phoneNumber.replace(/[-\s]/g, '');

    if (!cleanPhoneNumber.startsWith('+')) {
        cleanPhoneNumber = '962' + (cleanPhoneNumber.startsWith('0') ? cleanPhoneNumber.substring(1) : cleanPhoneNumber);
    } else {
        cleanPhoneNumber = cleanPhoneNumber.substring(1);
    }

    const url = `https://wa.me/${cleanPhoneNumber}?text=${encodedMessage}`;
    window.open(url, '_blank');
}

function copyInvitation(guestName, guestsCount, tableNumber, guestId) {
    const message = prepareMessage(guestName, guestsCount, tableNumber, guestId);
    navigator.clipboard.writeText(message).then(() => {
        Swal.fire({
            icon: 'success',
            title: translations.copy_tooltip,
            text: 'تم نسخ الرسالة بنجاح إلى الحافظة.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }).catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'فشل في نسخ الرسالة.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        console.error('Failed to copy text: ', err);
    });
}
