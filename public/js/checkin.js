// Configuration and Data
const CONFIG = {
    guestData: {},
    eventData: {},
    texts: {},
    lang: 'ar',
    csrfToken: '',
    eventDateTimeISO: ''
};

// Global state
let qrCodeGenerated = false;
let countdownInterval;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // The CONFIG object will be populated by the inline script in the view
    if (window.CONFIG) {
        Object.assign(CONFIG, window.CONFIG);
    }

    checkInitialStatus();
    // Start countdown only if the setting is enabled
    if (CONFIG.eventData.rsvp_show_countdown) {
        startCountdown();
    }
});

// Countdown Timer Function
function startCountdown() {
    const eventDate = new Date(CONFIG.eventDateTimeISO);

    function updateCountdown() {
        const now = new Date().getTime();
        const timeLeft = eventDate.getTime() - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            const els = {
                days: document.getElementById('days'),
                hours: document.getElementById('hours'),
                minutes: document.getElementById('minutes'),
                seconds: document.getElementById('seconds')
            };

            if (els.days) els.days.textContent = days.toString().padStart(2, '0');
            if (els.hours) els.hours.textContent = hours.toString().padStart(2, '0');
            if (els.minutes) els.minutes.textContent = minutes.toString().padStart(2, '0');
            if (els.seconds) els.seconds.textContent = seconds.toString().padStart(2, '0');
        } else {
            clearInterval(countdownInterval);
            const countdownSection = document.querySelector('.countdown-section');
            if (countdownSection) {
                countdownSection.innerHTML = `
                    <h3 style="font-size:1.125rem;font-weight:bold;margin-bottom:10px">
                        ${CONFIG.texts.event_time_reached}
                    </h3>
                    <p>${CONFIG.texts.enjoy_time}</p>
                `;
            }
        }
    }

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

// Check initial guest status
function checkInitialStatus() {
    const status = CONFIG.guestData.status;

    if (status === 'confirmed') {
        showSuccessState('confirmed');
    } else if (status === 'canceled') {
        showSuccessState('canceled');
    }
}

// Handle RSVP response
async function handleRSVP(status) {
    const confirmBtn = document.getElementById('confirm-button');
    const cancelBtn = document.getElementById('cancel-button');
    const spinner = document.getElementById(status === 'confirmed' ? 'confirm-spinner' : 'cancel-spinner');

    confirmBtn.disabled = true;
    cancelBtn.disabled = true;
    spinner.style.display = 'inline-block';

    try {
        const formData = new FormData();
        formData.append('ajax_rsvp', '1');
        formData.append('status', status);
        formData.append('guest_id', CONFIG.guestData.guest_id);
        formData.append('csrf_token', CONFIG.csrfToken);

        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showSuccessState(status);
            showToast(result.message, 'success');
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('RSVP Error:', error);
        showToast(error.message || CONFIG.texts.connection_error, 'error');
        confirmBtn.disabled = false;
        cancelBtn.disabled = false;
    } finally {
        spinner.style.display = 'none';
    }
}

// Show success state
function showSuccessState(status) {
    const actionButtons = document.getElementById('action-buttons-section');
    const responseMessage = document.getElementById('response-message');
    const qrSection = document.getElementById('qr-code-section');

    actionButtons.style.display = 'none';

    if (status === 'confirmed') {
        responseMessage.style.background = 'rgba(255, 255, 255, 0.9)';
        responseMessage.style.backdropFilter = 'blur(10px)';
        responseMessage.style.border = '2px solid rgba(45, 74, 34, 0.3)';
        responseMessage.style.color = '#2d4a22';
        responseMessage.style.boxShadow = '0 4px 15px rgba(45, 74, 34, 0.1)';
        responseMessage.innerHTML = `✓ ${CONFIG.texts.already_confirmed}`;
        responseMessage.style.display = 'block';

        if (qrSection) {
            qrSection.classList.add('active');
            generateQRCode();
        }
    } else {
        responseMessage.style.background = 'rgba(255, 240, 240, 0.9)';
        responseMessage.style.backdropFilter = 'blur(10px)';
        responseMessage.style.border = '2px solid rgba(239, 68, 68, 0.3)';
        responseMessage.style.color = '#dc2626';
        responseMessage.style.boxShadow = '0 4px 15px rgba(239, 68, 68, 0.1)';
        responseMessage.innerHTML = `✗ ${CONFIG.texts.already_declined}`;
        responseMessage.style.display = 'block';
    }
}

// Generate QR Code
async function generateQRCode() {
    if (qrCodeGenerated) return;

    const qrcodeContainer = document.getElementById('qrcode');
    if (!qrcodeContainer) return;

    qrcodeContainer.innerHTML = '';

    try {
        await loadQRLibrary();
        new QRCode(qrcodeContainer, {
            text: CONFIG.guestData.guest_id,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
        qrCodeGenerated = true;
    } catch (error) {
        console.error('QR Generation Error:', error);
        qrcodeContainer.innerHTML = '<div style="color:#ef4444">QR Code generation failed</div>';
    }
}

// Download QR Code
function downloadQR() {
    try {
        const qrCanvas = document.querySelector('#qrcode canvas');
        if (!qrCanvas) {
            showToast('QR Code not generated yet', 'error');
            return;
        }

        const link = document.createElement('a');
        link.download = `invitation-qr-${CONFIG.guestData.guest_id}.png`;
        link.href = qrCanvas.toDataURL('image/png');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast('QR Code downloaded successfully!', 'success');
    } catch (error) {
        console.error('Download Error:', error);
        showToast('Download failed', 'error');
    }
}

// Share QR Code
async function shareQR() {
    try {
        const qrCanvas = document.querySelector('#qrcode canvas');
        if (!qrCanvas) {
            showToast('QR Code not generated yet', 'error');
            return;
        }

        if (navigator.share && navigator.canShare) {
            qrCanvas.toBlob(async (blob) => {
                const file = new File([blob], 'invitation-qr.png', { type: 'image/png' });

                if (navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        title: CONFIG.eventData.event_name,
                        text: `${CONFIG.texts.share_invitation} - ${CONFIG.eventData.event_name}`,
                        files: [file]
                    });
                } else {
                    fallbackShare();
                }
            });
        } else {
            fallbackShare();
        }
    } catch (error) {
        console.error('Share Error:', error);
        fallbackShare();
    }
}

// Share invitation
async function shareInvitation() {
    const shareData = {
        title: CONFIG.eventData.event_name,
        text: `${CONFIG.texts.share_invitation} - ${CONFIG.eventData.event_name}`,
        url: window.location.href
    };

    try {
        if (navigator.share) {
            await navigator.share(shareData);
        } else {
            await navigator.clipboard.writeText(window.location.href);
            showToast('Link copied to clipboard!', 'success');
        }
    } catch (error) {
        console.error('Share Error:', error);
        fallbackShare();
    }
}

// Fallback share method
function fallbackShare() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link copied to clipboard!', 'success');
    }).catch(() => {
        prompt('Copy this link:', url);
    });
}

function addToCalendar() {
    const eventData = CONFIG.eventData;
    const lang = CONFIG.lang;

    const startDate = new Date(CONFIG.eventDateTimeISO);
    if (isNaN(startDate)) {
        showToast('Invalid event date.', 'error');
        return;
    }

    const endDate = new Date(startDate.getTime() + 3 * 60 * 60 * 1000);

    const toUTCFormat = (date) => {
        return date.toISOString().replace(/[-:.]/g, '').slice(0, 15) + 'Z';
    };

    const startTimeUTC = toUTCFormat(startDate);
    const endTimeUTC = toUTCFormat(endDate);

    const messages = {
        ar: { opening: 'جاري فتح التقويم...', fileCreated: 'تم إنشاء ملف التقويم!' },
        en: { opening: 'Opening calendar...', fileCreated: 'Calendar file created!' }
    };

    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

    const eventTitle = eventData.event_name || (lang === 'ar' ? 'حدث' : 'Event');
    const eventDescription = eventData.event_paragraph_ar || (lang === 'ar' ? 'دعوة خاصة' : 'Special Invitation');
    const eventLocation = eventData.venue_ar || '';

    if (isIOS) {
        const icsContent = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Wosuol//Event//EN',
            'BEGIN:VEVENT',
            `UID:${Date.now()}@wosuol.com`,
            `DTSTAMP:${startTimeUTC}`,
            `DTSTART:${startTimeUTC}`,
            `DTEND:${endTimeUTC}`,
            `SUMMARY:${eventTitle}`,
            `DESCRIPTION:${eventDescription}`,
            `LOCATION:${eventLocation}`,
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\r\n');

        const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `${eventTitle}.ics`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast(messages[lang].fileCreated, 'success');
    } else {
        const googleUrl = new URL('https://calendar.google.com/calendar/render');
        googleUrl.searchParams.append('action', 'TEMPLATE');
        googleUrl.searchParams.append('text', eventTitle);
        googleUrl.searchParams.append('dates', `${startTimeUTC}/${endTimeUTC}`);
        googleUrl.searchParams.append('details', eventDescription);
        googleUrl.searchParams.append('location', eventLocation);

        window.open(googleUrl.toString(), '_blank');
        showToast(messages[lang].opening, 'success');
    }
}

function openLocation() {
    if (CONFIG.eventData.Maps_link) {
        window.open(CONFIG.eventData.Maps_link, '_blank');
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    toastMessage.textContent = message;
    toast.className = `toast ${type === 'error' ? 'error' : ''}`;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

window.addEventListener('error', function(e) {
    console.error('Global Error:', e.error);
    showToast(CONFIG.texts.error_occurred, 'error');
});

window.addEventListener('beforeunload', function() {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const toast = document.getElementById('toast');
        if (toast.classList.contains('show')) {
            toast.classList.remove('show');
        }
    }
});
