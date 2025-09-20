// This file can be used to add JavaScript enhancements to the registration page.
// For now, it will contain the countdown timer and phone input logic.

document.addEventListener('DOMContentLoaded', function() {
    const texts = window.texts || {};
    const lang = window.lang || 'ar';
    const eventDateString = window.eventDateString || '';
    let redirectUrl = window.redirectUrl || '';
    let countdownInterval;

    // Countdown timer
    function startCountdown() {
        function parseDate(dateStr) {
            let dateMatch = dateStr.match(/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/);
            if (dateMatch) {
                return new Date(dateMatch[1], dateMatch[2] - 1, dateMatch[3], dateMatch[4], dateMatch[5], dateMatch[6]);
            }
            dateMatch = dateStr.match(/(\d{1,2})\.(\d{1,2})\.(\d{4})/);
            if (dateMatch) {
                return new Date(dateMatch[3], dateMatch[2] - 1, dateMatch[1], 20, 0, 0);
            }
            return new Date(eventDateString);
        }

        const eventDateTime = parseDate(eventDateString);

        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = eventDateTime.getTime() - now;

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
                        <h3>${texts.event_time_reached}</h3>
                        <p>${texts.enjoy_time}</p>
                    `;
                }
            }
        }

        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
    }

    if (document.getElementById('countdown-timer')) {
        startCountdown();
    }

    <?php if ($registration_successful && $status === 'confirmed'): ?>
        showSuccessModal('confirm');
    <?php elseif ($registration_successful && $status === 'canceled'): ?>
        showSuccessModal('cancel');
    <?php endif; ?>

    // Success modal
    function showSuccessModal(status) {
        const modal = document.getElementById('successModal');
        const title = document.getElementById('successTitle');
        const message = document.getElementById('successMessage');
        const button = document.querySelector('.success-button');

        if (status === 'confirm') {
            title.textContent = texts.registration_success_confirm;
            message.textContent = texts.registration_success_confirm;
            button.textContent = 'المتابعة للدعوة';
            button.style.display = 'block';
            setTimeout(() => {
                proceedToInvitation();
            }, 5000);
        } else if (status === 'cancel') {
            title.textContent = texts.registration_success_cancel;
            message.textContent = texts.registration_success_cancel;
            button.textContent = 'العودة للدعوة';
            button.style.display = 'block';
            setTimeout(() => {
                proceedToInvitation();
            }, 3000);
        }

        modal.classList.add('active');
    }

    function proceedToInvitation() {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else {
            document.getElementById('successModal').classList.remove('active');
        }
    }

    // Phone input management
    function updatePhonePlaceholder() {
        const countrySelect = document.getElementById('country_code');
        const phoneInput = document.getElementById('phone_number');
        const helpText = document.getElementById('phone-help-text');

        if (!countrySelect || !phoneInput || !helpText) return;

        const selectedValue = countrySelect.value;

        if (selectedValue === 'other') {
            phoneInput.placeholder = '+96279123456';
            helpText.textContent = texts['enter_full_number'] || 'Enter full number with country code';
        } else if (selectedValue === '+962') {
            phoneInput.placeholder = '791234567';
            helpText.textContent = lang === 'ar' ? 'أدخل رقم الجوال الأردني (مثال: 791234567)' : 'Enter Jordanian mobile number';
        } else if (selectedValue === '+966') {
            phoneInput.placeholder = '501234567';
            helpText.textContent = lang === 'ar' ? 'أدخل رقم الجوال السعودي (مثال: 501234567)' : 'Enter Saudi mobile number';
        } else if (selectedValue === '+971') {
            phoneInput.placeholder = '501234567';
            helpText.textContent = lang === 'ar' ? 'أدخل رقم الجوال الإماراتي (مثال: 501234567)' : 'Enter UAE mobile number';
        } else if (selectedValue) {
            phoneInput.placeholder = '12345678';
            helpText.textContent = texts['enter_local_number'] || 'Enter local number';
        } else {
            phoneInput.placeholder = '';
            helpText.textContent = texts['choose_country_first'] || 'Choose country first';
        }
    }

    // Form validation
    const rsvpForm = document.getElementById('rsvpForm');
    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name_ar').value.trim();
            const countryField = document.getElementById('country_code');
            const phoneField = document.getElementById('phone_number');

            const nameWords = name.split(/\s+/).filter(word => word.length > 0);
            if (nameWords.length < 3) {
                e.preventDefault();
                alert(texts.invalid_name_format);
                return;
            }

            if (countryField && phoneField) {
                const country = countryField.value;
                const phone = phoneField.value.trim();
                const phoneRequired = countryField.hasAttribute('required');

                if (phoneRequired && !country) {
                e.preventDefault();
                alert(lang === 'ar' ? 'يرجى اختيار الدولة' : 'Please select country');
                return;
                }

                const phoneLatin = phone.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));

                if (phoneRequired && phoneLatin.length < 7) {
                    e.preventDefault();
                    alert(lang === 'ar' ? 'يرجى إدخال رقم هاتف صحيح' : 'Please enter a valid phone number');
                    return;
                }
            }
        });
    }

    // Initialize phone placeholder
    updatePhonePlaceholder();

    const countryCodeSelect = document.getElementById('country_code');
    if(countryCodeSelect) {
        countryCodeSelect.addEventListener('change', updatePhonePlaceholder);
    }

    window.addEventListener('beforeunload', function() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('successModal');
            if (modal.classList.contains('active')) {
                modal.classList.remove('active');
            }
        }
    });
});
