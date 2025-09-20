<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate('page_title'); ?> - <?php echo htmlspecialchars($event['event_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/guests.css">
</head>
<body dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">

<div class="container-fluid">
    <div class="wosuol-logo">
        <div class="wosuol-icon"><i class="fas fa-users"></i></div>
        <div class="wosuol-text"><?php echo translate('app_name'); ?></div>
    </div>

    <div class="page-header">
        <h1 class="h3 mb-0"><?php echo translate('event_management'); ?>: <?php echo htmlspecialchars($event['event_name']); ?></h1>
        <div class="header-buttons">
            <a href="?event_id=<?php echo $event_id; ?>&lang=<?php echo $lang === 'ar' ? 'en' : 'ar'; ?>" class="btn">
                <i class="fas fa-language"></i>
                <?php echo $lang === 'ar' ? 'English' : 'العربية'; ?>
            </a>
            <?php if ($user_role === 'admin'): ?>
                <a href="events.php" class="btn btn-outline-secondary"><i class="fas fa-list-ul me-1"></i>العودة للحفلات</a>
            <?php endif; ?>
            <?php if ($user_role === 'admin' || $user_role === 'viewer'): ?>
                <a href="dashboard.php?event_id=<?php echo $event_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-chart-bar me-1"></i><?php echo translate('dashboard'); ?></a>
            <?php endif; ?>
            <?php if ($user_role === 'viewer'): ?>
                 <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-1"></i><?php echo translate('logout'); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="stats-row">
        <div class="row">
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-primary"><?php echo $stats['total_guests']; ?></div><div class="stat-people"><?php echo $stats['total_people']; ?> <?php echo translate('total_people'); ?></div><div class="stat-label"><?php echo translate('total_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-success"><?php echo $stats['confirmed_guests']; ?></div><div class="stat-people"><?php echo $stats['confirmed_people']; ?> <?php echo translate('confirmed_people'); ?></div><div class="stat-label"><?php echo translate('confirmed_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-info"><?php echo $stats['checked_in_guests']; ?></div><div class="stat-people"><?php echo $stats['checked_in_people']; ?> <?php echo translate('checked_in_people'); ?></div><div class="stat-label"><?php echo translate('checked_in_guests'); ?></div></div></div>
            <div class="col-md-3 col-6"><div class="stat-item"><div class="stat-number text-warning"><?php echo $stats['pending_guests']; ?></div><div class="stat-people"><?php echo $stats['pending_people']; ?> <?php echo translate('pending_people'); ?></div><div class="stat-label"><?php echo translate('pending_guests'); ?></div></div></div>
        </div>
    </div>

    <div class="filters-section">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <div class="col-md-4"><label class="form-label"><?php echo translate('search_label'); ?></label><input type="text" class="form-control" name="search" placeholder="<?php echo translate('search_placeholder'); ?>" value="<?php echo htmlspecialchars($search); ?>"></div>
            <div class="col-md-3"><label class="form-label"><?php echo translate('status_label'); ?></label><select name="status" class="form-select"><option value=""><?php echo translate('all'); ?></option><option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>><?php echo translate('pending'); ?></option><option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>><?php echo translate('confirmed'); ?></option><option value="canceled" <?php echo $status_filter === 'canceled' ? 'selected' : ''; ?>><?php echo translate('canceled'); ?></option></select></div>
            <div class="col-md-3"><label class="form-label"><?php echo translate('table_label'); ?></label><select name="table" class="form-select"><option value=""><?php echo translate('all'); ?></option><?php foreach ($tables as $table): ?><option value="<?php echo htmlspecialchars($table['table_number']); ?>" <?php echo $table_filter === $table['table_number'] ? 'selected' : ''; ?>><?php echo translate('table_label'); ?> <?php echo htmlspecialchars($table['table_number']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><div class="d-grid"><button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i><?php echo translate('search_button'); ?></button></div></div>
        </form>
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2" role="group">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGuestModal"><i class="fas fa-user-plus me-1"></i><?php echo translate('add_button'); ?></button>
                    <button type="button" class="btn btn-info" onclick="exportGuests()"><i class="fas fa-download me-1"></i><?php echo translate('export_button'); ?></button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-1"></i><?php echo translate('import_button'); ?></button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendInvitationsModal"><i class="fas fa-paper-plane me-1"></i><?php echo translate('send_invitations_button'); ?></button>
                    <a href="?event_id=<?php echo $event_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-refresh me-1"></i><?php echo translate('refresh_button'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($success_message)): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($success_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if (!empty($error_message)): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($error_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <form id="bulkForm" method="POST">
        <div class="card">
            <div class="card-header">
                <div class="bulk-actions"><div class="row align-items-center"><div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" id="selectAll"><label class="form-check-label fw-bold" for="selectAll"><?php echo translate('select_all'); ?></label></div></div><div class="col-md-9"><div class="row g-2 align-items-center"><div class="col-md-5"><select name="bulk_action" id="bulkActionSelect" class="form-select" required><option value=""><?php echo translate('bulk_action'); ?></option><option value="confirm"><?php echo translate('confirm_presence'); ?></option><option value="cancel"><?php echo translate('cancel_presence'); ?></option><option value="checkin"><?php echo translate('checkin'); ?></option><option value="assign_table"><?php echo translate('assign_table'); ?></option><option value="delete"><?php echo translate('delete'); ?></option></select></div><div class="col-md-5"><div id="bulkTableInputContainer" style="display: none;"><input type="number" name="bulk_table_number" class="form-control" placeholder="<?php echo translate('enter_table_number'); ?>" min="1"></div></div><div class="col-md-2"><button type="submit" class="btn btn-warning w-100"><i class="fas fa-bolt me-1"></i><?php echo translate('execute'); ?></button></div></div></div></div></div>
            </div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th><input type="checkbox" id="selectAllHeader"></th><th><?php echo translate('guest_id_col'); ?></th><th><?php echo translate('name_col'); ?></th><th><?php echo translate('phone_col'); ?></th><th><?php echo translate('count_col'); ?></th><th><?php echo translate('table_col'); ?></th><th><?php echo translate('location_col'); ?></th><th><?php echo translate('status_col'); ?></th><th><?php echo translate('checkin_col'); ?></th><th><?php echo translate('notes_col'); ?></th><th><?php echo translate('actions_col'); ?></th><th><?php echo translate('send_invitation_col'); ?></th></tr></thead><tbody>
                <?php if (empty($guests)): ?>
                    <tr><td colspan="12" class="text-center py-5 text-muted"><i class="fas fa-users-slash fa-3x mb-3"></i><h5><?php echo translate('no_guests'); ?></h5></td></tr>
                <?php else: ?>
                    <?php foreach ($guests as $guest): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_guests[]" value="<?php echo htmlspecialchars($guest['guest_id']); ?>" class="guest-checkbox"></td>
                            <td>
                                <a href="rsvp.php?id=<?php echo htmlspecialchars($guest['guest_id']); ?>" target="_blank" class="guest-id-link" title="عرض الدعوة">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($guest['guest_id']); ?></span>
                                </a>
                            </td>
                            <td><strong><?php echo htmlspecialchars($guest['name_ar']); ?></strong><div class="people-count-display"><?php echo $guest['guests_count']; ?> <?php echo translate('total_people'); ?></div></td>
                            <td><?php echo htmlspecialchars($guest['phone_number'] ?: '-'); ?></td>
                            <td><span class="badge bg-info fs-6"><?php echo $guest['guests_count']; ?></span></td>
                            <td><?php echo !empty($guest['table_number']) ? '<span class="badge bg-primary">' . translate('table_label') . ' ' . htmlspecialchars($guest['table_number']) . '</span>' : '-'; ?></td>
                            <td><?php echo htmlspecialchars($guest['assigned_location'] ?: '-'); ?></td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-guest-id="<?php echo $guest['guest_id']; ?>" data-current-status="<?php echo $guest['status']; ?>">
                                    <option value="pending" <?php echo $guest['status'] === 'pending' ? 'selected' : ''; ?>><?php echo translate('pending'); ?></option>
                                    <option value="confirmed" <?php echo $guest['status'] === 'confirmed' ? 'selected' : ''; ?>><?php echo translate('confirmed'); ?></option>
                                    <option value="canceled" <?php echo $guest['status'] === 'canceled' ? 'selected' : ''; ?>><?php echo translate('canceled'); ?></option>
                                </select>
                            </td>
                            <td><?php echo $guest['checkin_status'] === 'checked_in' ? '<span class="badge bg-success">' . translate('checked_in') . '</span>' : '<span class="badge bg-secondary">' . translate('not_checked_in') . '</span>'; ?></td>
                            <td><?php if (!empty($guest['notes'])): ?><i class="fas fa-comment text-warning" title="<?php echo htmlspecialchars($guest['notes']); ?>" data-bs-toggle="tooltip"></i><?php endif; ?></td>
                            <td><div class="action-buttons"><button type="button" class="action-btn edit-btn" onclick='editGuest(<?php echo htmlspecialchars(json_encode($guest, JSON_UNESCAPED_UNICODE)); ?>)' title="<?php echo translate('edit_tooltip'); ?>"><i class="fas fa-edit"></i></button><?php if ($guest['checkin_status'] !== 'checked_in'): ?><button type="button" class="action-btn checkin-btn" onclick="quickCheckin('<?php echo $guest['guest_id']; ?>')" title="<?php echo translate('checkin_tooltip'); ?>"><i class="fas fa-check"></i></button><?php endif; ?><button type="button" class="action-btn delete-btn" onclick="deleteGuest('<?php echo $guest['guest_id']; ?>', '<?php echo htmlspecialchars($guest['name_ar']); ?>')" title="<?php echo translate('delete_tooltip'); ?>"><i class="fas fa-trash"></i></button></div></td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn btn-whatsapp" onclick="sendWhatsApp('<?php echo htmlspecialchars($guest['phone_number']); ?>', '<?php echo htmlspecialchars($guest['name_ar'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($guest['guests_count']); ?>', '<?php echo htmlspecialchars($guest['table_number']); ?>', '<?php echo htmlspecialchars($guest['guest_id']); ?>')" title="<?php echo translate('whatsapp_tooltip'); ?>">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button type="button" class="action-btn btn-copy" onclick="copyInvitation('<?php echo htmlspecialchars($guest['name_ar'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($guest['guests_count']); ?>', '<?php echo htmlspecialchars($guest['table_number']); ?>', '<?php echo htmlspecialchars($guest['guest_id']); ?>')" title="<?php echo translate('copy_tooltip'); ?>">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody></table></div></div>
        </div>
    </form>
</div>

<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title"><?php echo translate('add_guest_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label"><?php echo translate('guest_name'); ?></label><input type="text" class="form-control" name="name_ar" required></div>
                        <div class="col-md-6"><label class="form-label"><?php echo translate('phone_number'); ?></label><input type="tel" class="form-control" name="phone_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('guests_count'); ?></label><input type="number" class="form-control" name="guests_count" value="1" min="1"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('table_label'); ?></label><input type="text" class="form-control" name="table_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('status_col'); ?></label><select class="form-select" name="status"><option value="pending"><?php echo translate('pending'); ?></option><option value="confirmed"><?php echo translate('confirmed'); ?></option><option value="canceled"><?php echo translate('canceled'); ?></option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('location'); ?></label><select class="form-select" name="assigned_location"><option value=""><?php echo translate('all'); ?></option><option value="أهل العروس">أهل العروس</option><option value="أهل العريس">أهل العريس</option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('notes'); ?></label><textarea class="form-control" name="notes" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button><button type="submit" name="add_guest" class="btn btn-primary"><?php echo translate('add'); ?></button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title"><?php echo translate('edit_guest_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="guest_id" id="edit_guest_id">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label"><?php echo translate('guest_name'); ?></label><input type="text" class="form-control" name="name_ar" id="edit_name_ar" required></div>
                        <div class="col-md-6"><label class="form-label"><?php echo translate('phone_number'); ?></label><input type="tel" class="form-control" name="phone_number" id="edit_phone_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('guests_count'); ?></label><input type="number" class="form-control" name="guests_count" id="edit_guests_count" min="1"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('table_number'); ?></label><input type="text" class="form-control" name="table_number" id="edit_table_number"></div>
                        <div class="col-md-4"><label class="form-label"><?php echo translate('status_col'); ?></label><select class="form-select" name="status" id="edit_status"><option value="pending"><?php echo translate('pending'); ?></option><option value="confirmed"><?php echo translate('confirmed'); ?></option><option value="canceled"><?php echo translate('canceled'); ?></option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('location'); ?></label><select class="form-select" name="assigned_location" id="edit_assigned_location"><option value=""><?php echo translate('all'); ?></option><option value="أهل العروس">أهل العروس</option><option value="أهل العريس">أهل العريس</option></select></div>
                        <div class="col-md-12"><label class="form-label"><?php echo translate('notes'); ?></label><textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button><button type="submit" name="update_guest" class="btn btn-primary"><?php echo translate('save'); ?></button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><?php echo translate('import_title'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row"><div class="col-md-6"><div class="import-instructions"><h6><?php echo translate('import_instructions'); ?></h6><ul><li><?php echo translate('import_file_types'); ?></li><li><?php echo translate('header_row_required'); ?></li></ul><table class="table table-sm"><thead><tr><th><?php echo translate('header'); ?></th><th><?php echo translate('description'); ?></th><th><?php echo translate('required'); ?></th></tr></thead><tbody><tr><td><code>name_ar</code></td><td><?php echo translate('guest_name'); ?></td><td><?php echo translate('yes_required'); ?></td></tr><tr><td><code>phone_number</code></td><td><?php echo translate('phone_number'); ?></td><td><?php echo translate('no_required'); ?></td></tr><tr><td><code>guests_count</code></td><td><?php echo translate('guests_count'); ?></td><td><?php echo translate('no_required'); ?></td></tr></tbody></table></div><form id="importForm" class="mt-3"><div class="mb-3"><label for="importFile" class="form-label"><?php echo translate('choose_file'); ?></label><input type="file" class="form-control" id="importFile" accept=".xlsx,.xls,.csv" required></div></form></div><div class="col-md-6"><label class="form-label"><?php echo translate('preview'); ?></label><div id="previewContainer" class="border rounded p-3 bg-light" style="min-height: 300px;"></div><div id="importStats" class="d-none mt-2"><h6><?php echo translate('import_stats'); ?></h6><p><?php echo translate('total_rows'); ?>: <span id="totalRows">0</span>, <?php echo translate('valid_rows'); ?>: <span id="validRows">0</span>, <?php echo translate('invalid_rows'); ?>: <span id="invalidRows">0</span></p></div></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button>
                <button type="button" id="downloadTemplate" class="btn btn-outline-primary"><?php echo translate('download_template'); ?></button>
                <button type="button" id="importBtn" class="btn btn-success" disabled><?php echo translate('import_button'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sendInvitationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo translate('send_invitations_title'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6><?php echo translate('variables'); ?></h6>
                    <div class="variables-container">
                        <span class="variable-tag" data-variable="(guest_name)"><?php echo translate('variable_name'); ?></span>
                        <span class="variable-tag" data-variable="(guests_count)"><?php echo translate('variable_count'); ?></span>
                        <span class="variable-tag" data-variable="(table_number)"><?php echo translate('variable_table'); ?></span>
                        <span class="variable-tag" data-variable="(invitation_link)"><?php echo translate('variable_link'); ?></span>
                        <span class="variable-tag" data-variable="(event_location_link)"><?php echo translate('variable_location_link'); ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    <h6><?php echo translate('message_template_instructions'); ?></h6>
                    <textarea id="messageTemplate" class="form-control" rows="8" placeholder="مرحبا (guest_name)..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('close'); ?></button>
                <button type="button" class="btn btn-primary" id="saveTemplateBtn"><?php echo translate('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script src="js/guests.js"></script>

</body>
</html>
