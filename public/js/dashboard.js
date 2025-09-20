const dashboardApiUrl = `dashboard.php?event_id=${eventId}&fetch_data=true`;
const texts = window.texts || {};
const lang = window.lang || 'ar';

const totalGuestsEl = document.getElementById('total-guests');
const confirmedGuestsEl = document.getElementById('confirmed-guests');
const canceledGuestsEl = document.getElementById('canceled-guests');
const pendingGuestsEl = document.getElementById('pending-guests');
const checkedInGuestsEl = document.getElementById('checked-in-guests');
const confirmedListEl = document.getElementById('confirmed-list');
const canceledListEl = document.getElementById('canceled-list');
const pendingListEl = document.getElementById('pending-list');
const checkedInListEl = document.getElementById('checked-in-list');
const guestSearchInput = document.getElementById('guest-search');
const refreshButton = document.getElementById('refresh-button');

let allGuestsData = [];
let filteredGuestsData = [];
let previousStats = {
    total: 0,
    confirmed: 0,
    canceled: 0,
    pending: 0,
    checkedIn: 0
};
let miniCharts = {};
let isPresentationMode = false;
let isFullscreen = false;
let isSearching = false;

function initMiniCharts() {
    const chartIds = ['totalChart', 'confirmedChart', 'checkedinChart', 'canceledChart', 'pendingChart'];
    chartIds.forEach(id => {
        const ctx = document.getElementById(id);
        if (ctx) {
            miniCharts[id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array(10).fill(''),
                    datasets: [{
                        data: Array(10).fill(0),
                        borderColor: '#2d4a22',
                        backgroundColor: 'rgba(45, 74, 34, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            });
        }
    });
}

function updateMiniChart(chartId, newValue) {
    const chart = miniCharts[chartId];
    if (chart) {
        chart.data.datasets[0].data.shift();
        chart.data.datasets[0].data.push(newValue);
        chart.update('none');
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');

    toastMessage.textContent = message;
    toast.className = `toast-notification ${type === 'error' ? 'error' : ''} show`;

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function animateNumber(element, targetNumber, skipAnimation = false) {
    if (skipAnimation) {
        element.textContent = targetNumber;
        return;
    }

    const currentNumber = parseInt(element.textContent) || 0;
    const difference = targetNumber - currentNumber;
    const duration = 500;
    const steps = 20;
    const stepSize = difference / steps;
    const stepDuration = duration / steps;

    let currentStep = 0;

    const timer = setInterval(() => {
        currentStep++;
        if (currentStep >= steps) {
            element.textContent = targetNumber;
            clearInterval(timer);
        } else {
            const newValue = Math.round(currentNumber + (stepSize * currentStep));
            element.textContent = newValue;
        }
    }, stepDuration);
}

let startY = 0;
let pullDistance = 0;
let isRefreshing = false;

function initPullToRefresh() {
    const pullIndicator = document.getElementById('pull-indicator');
    const guestLists = document.getElementById('guest-lists');

    guestLists.addEventListener('touchstart', (e) => {
        if (guestLists.scrollTop === 0) {
            startY = e.touches[0].pageY;
        }
    });

    guestLists.addEventListener('touchmove', (e) => {
        if (guestLists.scrollTop === 0 && !isRefreshing) {
            pullDistance = e.touches[0].pageY - startY;
            if (pullDistance > 0) {
                e.preventDefault();
                const progress = Math.min(pullDistance / 80, 1);
                pullIndicator.style.transform = `translateX(-50%) translateY(${Math.min(pullDistance * 0.5, 40)}px)`;
                pullIndicator.querySelector('i').style.transform = `rotate(${progress * 180}deg)`;

                if (pullDistance > 80) {
                    pullIndicator.classList.add('active');
                } else {
                    pullIndicator.classList.remove('active');
                }
            }
        }
    });

    guestLists.addEventListener('touchend', () => {
        if (pullDistance > 80 && !isRefreshing) {
            triggerRefresh();
        }
        resetPullIndicator();
    });
}

function resetPullIndicator() {
    const pullIndicator = document.getElementById('pull-indicator');
    pullIndicator.style.transform = 'translateX(-50%) translateY(-60px)';
    pullIndicator.querySelector('i').style.transform = 'rotate(0deg)';
    pullIndicator.classList.remove('active');
    pullDistance = 0;
}

function triggerRefresh() {
    isRefreshing = true;
    const pullIndicator = document.getElementById('pull-indicator');
    pullIndicator.classList.add('loading');
    pullIndicator.querySelector('i').className = 'fas fa-spinner';

    fetchAndDisplayData().finally(() => {
        setTimeout(() => {
            isRefreshing = false;
            pullIndicator.classList.remove('loading');
            pullIndicator.querySelector('i').className = 'fas fa-arrow-down';
            resetPullIndicator();
            showToast(texts['refresh_success']);
        }, 1000);
    });
}

function togglePresentationMode() {
    isPresentationMode = !isPresentationMode;
    document.body.classList.toggle('presentation-mode', isPresentationMode);
    const btn = document.getElementById('presentation-mode-btn');
    btn.innerHTML = `<i class="fas fa-${isPresentationMode ? 'times' : 'tv'}"></i> ${isPresentationMode ? texts['exit_presentation'] : texts['presentation_mode']}`;
}

function toggleFullscreen() {
    const container = document.querySelector('.container');
    if (!isFullscreen) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.msRequestFullscreen) {
            document.documentElement.msRequestFullscreen();
        }
        container.classList.add('fullscreen-mode');
        isFullscreen = true;
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        container.classList.remove('fullscreen-mode');
        isFullscreen = false;
    }

    const btn = document.getElementById('fullscreen-btn');
    btn.innerHTML = `<i class="fas fa-${isFullscreen ? 'compress' : 'expand'}"></i> ${isFullscreen ? texts['exit_fullscreen'] : texts['fullscreen']}`;
}

function exportToExcel() {
    const csvContent = generateCSVContent();
    const blob = new Blob([csvContent], {
        type: 'application/vnd.ms-excel;charset=utf-8'
    });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `guest_report_${new Date().toISOString().split('T')[0]}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    showToast(texts['export_excel_success']);
}

function printDashboard() {
    window.print();
}

function generateCSVContent() {
    const headers = [texts.name, texts.guests_count, texts.table, texts.status].join(',') + '\n';
    const dataToExport = isSearching ? filteredGuestsData : allGuestsData;
    const rows = dataToExport.map(guest => {
        const status = guest.status === 'confirmed' ? texts.status_confirmed :
            guest.status === 'canceled' ? texts.status_declined : texts.status_pending;
        return [
            `"${guest.name_ar || ''}"`,
            guest.guests_count || '1',
            guest.table_number || '',
            status
        ].join(',');
    }).join('\n');
    return headers + rows;
}

async function fetchAndDisplayData() {
    try {
        refreshButton.disabled = true;
        const refreshIcon = refreshButton.querySelector('i');
        refreshIcon.style.animation = 'spin 1s linear infinite';

        const response = await fetch(dashboardApiUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        if (data.error) {
            throw new Error(data.error);
        }

        allGuestsData = data.guests;

        if (isSearching && guestSearchInput.value.trim()) {
            filterGuests(guestSearchInput.value.trim());
        } else {
            isSearching = false;
            filteredGuestsData = allGuestsData;
            updateDashboard(allGuestsData, false, data.stats);
        }

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        showToast(texts['error_fetching_data'] + ': ' + error.message, 'error');
    } finally {
        refreshButton.disabled = false;
        const refreshIcon = refreshButton.querySelector('i');
        refreshIcon.style.animation = '';
    }
}

function filterGuests(searchTerm) {
    const lowerSearchTerm = searchTerm.toLowerCase();
    filteredGuestsData = allGuestsData.filter(guest => {
        const name = (guest.name_ar || '').toLowerCase();
        const table = (guest.table_number || '').toString().toLowerCase();
        return name.includes(lowerSearchTerm) || table.includes(lowerSearchTerm);
    });

    isSearching = searchTerm.length > 0;
    updateDashboard(filteredGuestsData, true);
}

function updateDashboard(guests, skipAnimation = false, serverStats = null) {
    let stats;
    if (serverStats && !isSearching) {
        stats = {
            total: serverStats.total_guests,
            totalPeople: serverStats.total_people,
            confirmed: serverStats.confirmed_guests,
            confirmedPeople: serverStats.confirmed_people,
            canceled: serverStats.canceled_guests,
            canceledPeople: serverStats.canceled_people,
            pending: serverStats.pending_guests,
            pendingPeople: serverStats.pending_people,
            checkedIn: serverStats.checkedIn_guests,
            checkedInPeople: serverStats.checkedIn_people
        };
    } else {
        let total = guests.length;
        let totalPeople = 0;
        let confirmed = 0,
            confirmedPeople = 0;
        let canceled = 0,
            canceledPeople = 0;
        let pending = 0,
            pendingPeople = 0;
        let checkedIn = 0,
            checkedInPeople = 0;

        guests.forEach(guest => {
            const guestCount = parseInt(guest.guests_count) || 1;
            totalPeople += guestCount;

            if (guest.checkin_status === 'checked_in') {
                checkedIn++;
                checkedInPeople += guestCount;
            }

            if (guest.status === 'confirmed') {
                confirmed++;
                confirmedPeople += guestCount;
            } else if (guest.status === 'canceled') {
                canceled++;
                canceledPeople += guestCount;
            } else {
                pending++;
                pendingPeople += guestCount;
            }
        });

        stats = {
            total,
            totalPeople,
            confirmed,
            confirmedPeople,
            canceled,
            canceledPeople,
            pending,
            pendingPeople,
            checkedIn,
            checkedInPeople
        };
    }

    confirmedListEl.innerHTML = '';
    canceledListEl.innerHTML = '';
    pendingListEl.innerHTML = '';
    checkedInListEl.innerHTML = '';

    guests.forEach(guest => {
        const guestName = guest.name_ar || 'ضيف';
        const peopleText = guest.guests_count > 1 ? texts['people_plural'] : texts['people_singular'];
        const guestCount = guest.guests_count ? `(${guest.guests_count} ${peopleText})` : '';
        const tableNumber = guest.table_number ? `${texts['table_number']}: ${guest.table_number}` : '';

        const guestItem = document.createElement('div');
        guestItem.className = 'guest-item';
        guestItem.innerHTML = `
            <div>
                <div class="guest-name">${guestName}</div>
                <div class="guest-details">${guestCount} ${tableNumber}</div>
            </div>
        `;

        if (guest.checkin_status === 'checked_in') {
            checkedInListEl.appendChild(guestItem.cloneNode(true));
        }

        if (guest.status === 'confirmed') {
            if (guest.checkin_status !== 'checked_in') {
                confirmedListEl.appendChild(guestItem.cloneNode(true));
            }
        } else if (guest.status === 'canceled') {
            canceledListEl.appendChild(guestItem.cloneNode(true));
        } else {
            pendingListEl.appendChild(guestItem.cloneNode(true));
        }
    });

    if (checkedInListEl.children.length === 0) {
        checkedInListEl.innerHTML = `<div class="empty-state">${texts['no_guests']}</div>`;
    }
    if (confirmedListEl.children.length === 0) {
        confirmedListEl.innerHTML = `<div class="empty-state">${texts['no_guests']}</div>`;
    }
    if (canceledListEl.children.length === 0) {
        canceledListEl.innerHTML = `<div class="empty-state">${texts['no_guests']}</div>`;
    }
    if (pendingListEl.children.length === 0) {
        pendingListEl.innerHTML = `<div class="empty-state">${texts['no_guests']}</div>`;
    }

    if (!isSearching && previousStats.total > 0) {
        if (stats.checkedIn > previousStats.checkedIn) {
            showToast(texts['new_guest_checked_in']);
        }
        if (stats.confirmed > previousStats.confirmed) {
            showToast(texts['new_guest_confirmed']);
        }
    }

    if (!isSearching) {
        updateMiniChart('totalChart', stats.total);
        updateMiniChart('confirmedChart', stats.confirmed);
        updateMiniChart('checkedinChart', stats.checkedIn);
        updateMiniChart('canceledChart', stats.canceled);
        updateMiniChart('pendingChart', stats.pending);
        previousStats = stats;
    }

    animateNumber(totalGuestsEl, stats.total, skipAnimation);
    animateNumber(confirmedGuestsEl, stats.confirmed, skipAnimation);
    animateNumber(canceledGuestsEl, stats.canceled, skipAnimation);
    animateNumber(pendingGuestsEl, stats.pending, skipAnimation);
    animateNumber(checkedInGuestsEl, stats.checkedIn, skipAnimation);

    const peoplePlural = texts['people_plural'];
    const peopleSingular = texts['people_singular'];
    document.getElementById('total-people').textContent = `${stats.totalPeople} ${stats.totalPeople === 1 ? peopleSingular : peoplePlural}`;
    document.getElementById('confirmed-people').textContent = `${stats.confirmedPeople} ${stats.confirmedPeople === 1 ? peopleSingular : peoplePlural}`;
    document.getElementById('canceled-people').textContent = `${stats.canceledPeople} ${stats.canceledPeople === 1 ? peopleSingular : peoplePlural}`;
    document.getElementById('pending-people').textContent = `${stats.pendingPeople} ${stats.pendingPeople === 1 ? peopleSingular : peoplePlural}`;
    document.getElementById('checked-in-people').textContent = `${stats.checkedInPeople} ${stats.checkedInPeople === 1 ? peopleSingular : peoplePlural}`;
}

guestSearchInput.addEventListener('input', () => {
    const searchTerm = guestSearchInput.value.trim();
    if (searchTerm.length === 0) {
        isSearching = false;
        filteredGuestsData = allGuestsData;
        updateDashboard(allGuestsData, true);
    } else {
        filterGuests(searchTerm);
    }
});

refreshButton.addEventListener('click', fetchAndDisplayData);
document.getElementById('presentation-mode-btn').addEventListener('click', togglePresentationMode);
document.getElementById('fullscreen-btn').addEventListener('click', toggleFullscreen);
document.getElementById('export-excel-btn').addEventListener('click', exportToExcel);
document.getElementById('print-btn').addEventListener('click', printDashboard);

document.addEventListener('keydown', (e) => {
    if (e.ctrlKey || e.metaKey) {
        switch (e.key) {
            case 'f':
                e.preventDefault();
                toggleFullscreen();
                break;
            case 'p':
                e.preventDefault();
                togglePresentationMode();
                break;
            case 'r':
                e.preventDefault();
                fetchAndDisplayData();
                break;
        }
    }
    if (e.key === 'Escape') {
        if (isPresentationMode) togglePresentationMode();
        if (isFullscreen) toggleFullscreen();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initMiniCharts();
    initPullToRefresh();
    fetchAndDisplayData();
});

setInterval(fetchAndDisplayData, 30000);
