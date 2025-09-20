const texts = window.texts || {};
const lang = window.lang || 'ar';
let currentEventId = null;
let allGuests = [];
let selectedGuests = [];

document.addEventListener('DOMContentLoaded', function() {
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        const eventId = card.dataset.eventId;
        loadEventStats(eventId);
        loadSendResults(eventId);
    });
});

async function loadEventStats(eventId) {
    try {
        const response = await fetch(`events.php?api=true&get_stats=true&event_id=${eventId}`);
        const stats = await response.json();

        const statsContainer = document.getElementById(`stats-${eventId}`);
        if (statsContainer) {
            statsContainer.querySelector('[data-stat="total"]').textContent = stats.total || 0;
            statsContainer.querySelector('[data-stat="confirmed"]').textContent = stats.confirmed || 0;
            statsContainer.querySelector('[data-stat="pending"]').textContent = stats.pending || 0;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadSendResults(eventId) {
    try {
        const response = await fetch(`events.php?api=true&get_send_results=true&event_id=${eventId}`);
        const results = await response.json();

        const resultsContainer = document.getElementById(`results-content-${eventId}`);
        if (resultsContainer && results.length > 0) {
            let html = '';
            results.slice(0, 3).forEach(result => {
                const date = new Date(result.created_at);
                const timeString = date.toLocaleString(lang === 'ar' ? 'ar-EG' : 'en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let actionText = '';
                switch(result.action_type) {
                    case 'send_global_all':
                        actionText = texts['send_global_all'];
                        break;
                    case 'send_event_all':
                        actionText = texts['send_event_all'];
                        break;
                    case 'send_selected':
                        actionText = texts['send_selected_guests'];
                        break;
                    default:
                        actionText = result.action_type;
                }

                const total = result.success_count + result.failed_count;
                const successRate = total > 0 ? Math.round((result.success_count / total) * 100) : 0;

                html += `
                    <div class="result-item">
                        <div class="result-meta">
                            <div class="result-action">${actionText}</div>
                            <div class="result-time">${timeString}</div>
                        </div>
                        <div class="result-numbers">
                            <span class="result-success">✓ ${result.success_count}</span>
                            ${result.failed_count > 0 ? `<span class="result-failed">✗ ${result.failed_count}</span>` : ''}
                            ${total > 0 ? `<span class="result-rate">(${successRate}%)</span>` : ''}
                        </div>
                    </div>
                `;
            });
            resultsContainer.innerHTML = html;
        } else if (resultsContainer) {
            resultsContainer.innerHTML = '<p class="text-sm text-gray-500">' + texts.no_send_history + '</p>';
        }
    } catch (error) {
        console.error('Error loading send results:', error);
    }
}

function refreshSendResults(eventId) {
    loadSendResults(eventId);
}

function handleSendSubmit(form, eventId) {
    const confirmed = confirm(texts['confirm_event_send']);
    if (confirmed) {
        showLoading(eventId);
        setTimeout(() => {
            hideLoading(eventId);
            loadSendResults(eventId);
        }, 2000);
    }
    return confirmed;
}

function showLoading(eventId) {
    const overlay = document.getElementById(`loading-${eventId}`);
    if (overlay) overlay.classList.add('active');
}

function hideLoading(eventId) {
    const overlay = document.getElementById(`loading-${eventId}`);
    if (overlay) overlay.classList.remove('active');
}

function copyRegistrationLink(eventId) {
    const baseUrl = window.location.origin + window.location.pathname.replace('events.php', '');
    const registrationUrl = `${baseUrl}register.php?event_id=${eventId}`;

    navigator.clipboard.writeText(registrationUrl).then(() => {
        const tooltip = document.getElementById(`tooltip-${eventId}`);
        tooltip.classList.add('show');
        setTimeout(() => {
            tooltip.classList.remove('show');
        }, 2000);
    });
}

async function openGuestSelection(eventId) {
    currentEventId = eventId;
    document.getElementById('guestSelectionModal').classList.add('active');
    document.getElementById('guest-list').innerHTML = '<div class="text-center p-8 text-gray-500">' + texts.processing + '</div>';

    try {
        const response = await fetch(`events.php?api=true&get_guests=true&event_id=${eventId}`);
        allGuests = await response.json();
        selectedGuests = [];
        renderGuestList();
        updateSelectionCount();
    } catch (error) {
        console.error('Error loading guests:', error);
        document.getElementById('guest-list').innerHTML = '<div class="text-center p-8 text-red-500">' +
            (lang === 'ar' ? 'خطأ في تحميل الضيوف' : 'Error loading guests') + '</div>';
    }
}

function renderGuestList(searchTerm = '') {
    const filteredGuests = allGuests.filter(guest =>
        guest.name_ar.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (guest.phone_number && guest.phone_number.includes(searchTerm))
    );

    const guestListHTML = filteredGuests.map(guest => {
        const isSelected = selectedGuests.includes(guest.id);
        const statusClass = `status-${guest.status}`;
        const statusText = guest.status === 'confirmed' ? texts.confirmed :
                         guest.status === 'canceled' ? texts.canceled : texts.pending;

        return `
            <div class="guest-item">
                <input type="checkbox" ${isSelected ? 'checked' : ''}
                       onchange="toggleGuestSelection(${guest.id})"
                       class="mr-2">
                <div class="flex-grow">
                    <div class="font-medium">${guest.name_ar}</div>
                    <div class="text-sm text-gray-500">${guest.phone_number || (lang === 'ar' ? 'لا يوجد هاتف' : 'No phone')}</div>
                </div>
                <span class="status-badge ${statusClass}">${statusText}</span>
            </div>
        `;
    }).join('');

    document.getElementById('guest-list').innerHTML = guestListHTML ||
        '<div class="text-center p-8 text-gray-500">' + (lang === 'ar' ? 'لا يوجد ضيوف' : 'No guests') + '</div>';
}

function toggleGuestSelection(guestId) {
    const index = selectedGuests.indexOf(guestId);
    if (index > -1) {
        selectedGuests.splice(index, 1);
    } else {
        selectedGuests.push(guestId);
    }
    updateSelectionCount();
}

function selectAllGuests() {
    selectedGuests = allGuests.map(guest => guest.id);
    renderGuestList(document.getElementById('guest-search').value);
    updateSelectionCount();
}

function clearGuestSelection() {
    selectedGuests = [];
    renderGuestList(document.getElementById('guest-search').value);
    updateSelectionCount();
}

function updateSelectionCount() {
    const count = selectedGuests.length;
    document.getElementById('selection-count').textContent = `${count} ${texts.guests_selected}`;
    document.getElementById('send-selected-btn').disabled = count === 0;
}

function closeGuestSelection() {
    document.getElementById('guestSelectionModal').classList.remove('active');
    currentEventId = null;
    allGuests = [];
    selectedGuests = [];
}

function sendToSelectedGuests() {
    if (selectedGuests.length === 0) {
        alert(lang === 'ar' ? 'يرجى تحديد الضيوف المراد إرسال الدعوات لهم' : 'Please select guests to send invitations to');
        return;
    }

    const confirmMessage = lang === 'ar' ?
        `إرسال دعوات لـ ${selectedGuests.length} ضيف؟` :
        `Send invitations to ${selectedGuests.length} guests?`;

    if (confirm(confirmMessage)) {
        showLoading(currentEventId);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'events.php';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= htmlspecialchars($_SESSION['csrf_token']) ?>';

        const messagingAction = document.createElement('input');
        messagingAction.type = 'hidden';
        messagingAction.name = 'messaging_action';
        messagingAction.value = 'send_to_selected';

        const eventIdInput = document.createElement('input');
        eventIdInput.type = 'hidden';
        eventIdInput.name = 'event_id';
        eventIdInput.value = currentEventId;

        const selectedGuestsInput = document.createElement('input');
        selectedGuestsInput.type = 'hidden';
        selectedGuestsInput.name = 'selected_guests';
        selectedGuestsInput.value = JSON.stringify(selectedGuests);

        form.appendChild(csrfToken);
        form.appendChild(messagingAction);
        form.appendChild(eventIdInput);
        form.appendChild(selectedGuestsInput);

        document.body.appendChild(form);
        form.submit();

        closeGuestSelection();
    }
}

document.getElementById('guest-search').addEventListener('input', function(e) {
    renderGuestList(e.target.value);
});

document.getElementById('guestSelectionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGuestSelection();
    }
});

setInterval(() => {
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        const eventId = card.dataset.eventId;
        loadEventStats(eventId);
        loadSendResults(eventId);
    });
}, 120000);
