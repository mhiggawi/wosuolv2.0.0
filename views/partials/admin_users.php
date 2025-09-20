<h3 class="text-xl font-bold mb-4"><?= $t['add_user'] ?></h3>
<form method="POST" action="admin.php?event_id=<?= $event_id ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="user_action" value="add">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="form-group">
            <label><?= $t['username'] ?>:</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label><?= $t['password'] ?>:</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label><?= $t['role'] ?>:</label>
            <select name="role" required onchange="toggleEventSelect(this.value, 'add_event_select_container')">
                <option value="admin">مدير</option>
                <option value="viewer">مشاهد</option>
                <option value="checkin_user">مسجل دخول</option>
            </select>
        </div>
        <div class="form-group" id="add_event_select_container" style="display:none;">
            <label><?= $t['custom_event'] ?>:</label>
            <select name="user_event_id">
                <option value="">-- اختر الحفل --</option>
                <?php foreach($all_events as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['event_name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-green mt-4">
        <i class="fas fa-user-plus"></i>
        <?= $t['add'] ?>
    </button>
</form>

<hr class="my-8 border-t border-gray-300">

<h3 class="text-xl font-bold mt-8 mb-4"><?= $t['current_users'] ?></h3>
<div class="overflow-x-auto">
    <table class="user-table">
        <thead>
            <tr>
                <th><?= $t['username'] ?></th>
                <th><?= $t['role'] ?></th>
                <th><?= $t['custom_event'] ?></th>
                <th><?= $t['actions'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($user['role'] === 'admin'): ?>
                        <em>(كل الحفلات)</em>
                    <?php else: ?>
                        <?php
                        $event_name = 'غير محدد';
                        foreach ($all_events as $e) {
                            if ($e['id'] == $user['event_id']) {
                                $event_name = $e['event_name'];
                                break;
                            }
                        }
                        echo htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8');
                        ?>
                    <?php endif; ?>
                </td>
                <td class="actions-cell">
                    <button type="button" class="btn btn-yellow btn-small" onclick='openEditModal(<?= json_encode($user) ?>)'>
                        <i class="fas fa-edit"></i> <?= $t['edit'] ?>
                    </button>
                    <?php if ($_SESSION['username'] !== $user['username']): ?>
                    <form method="POST" action="admin.php?event_id=<?= $event_id ?>" onsubmit="return confirm('<?= $t['confirm_delete_user'] ?>');" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="user_action" value="delete">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-danger btn-small">
                            <i class="fas fa-trash"></i> <?= $t['delete'] ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="editUserModal" class="modal">
    <div class="modal-content">
        <h2 class="text-2xl font-bold mb-4"><?= $t['edit'] ?> <?= $t['username'] ?></h2>
        <form method="POST" action="admin.php?event_id=<?= $event_id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="user_action" value="edit">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group">
                <label><?= $t['username'] ?>:</label>
                <input type="text" name="username" id="edit_username" required>
            </div>
            <div class="form-group">
                <label><?= $t['password'] ?> (<?= $t['leave_empty'] ?>):</label>
                <input type="password" name="password" id="edit_password">
            </div>
            <div class="form-group">
                <label><?= $t['role'] ?>:</label>
                <select name="role" id="edit_role" required onchange="toggleEventSelect(this.value, 'edit_event_select_container')">
                    <option value="admin">مدير</option>
                    <option value="viewer">مشاهد</option>
                    <option value="checkin_user">مسجل دخول</option>
                </select>
            </div>
            <div class="form-group" id="edit_event_select_container" style="display:none;">
                <label><?= $t['custom_event'] ?>:</label>
                <select name="user_event_id" id="edit_user_event_id">
                    <option value="">-- اختر الحفل --</option>
                    <?php foreach($all_events as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['event_name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="closeEditModal()" class="btn">
                    <?= $t['cancel'] ?>
                </button>
                <button type="submit" class="btn btn-blue">
                    <?= $t['save_changes'] ?>
                </button>
            </div>
        </form>
    </div>
</div>
