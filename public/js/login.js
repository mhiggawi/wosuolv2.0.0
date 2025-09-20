function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggle');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
        toggleIcon.title = texts['hide_password'];
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
        toggleIcon.title = texts['show_password'];
    }
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const button = document.getElementById('loginButton');
    const buttonText = document.getElementById('buttonText');

    button.disabled = true;
    buttonText.textContent = texts['logging_in'];
});

document.addEventListener('DOMContentLoaded', function() {
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        usernameField.focus();
    } else {
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.focus();
        }
    }

    const countdownTimer = document.getElementById('countdown-timer');
    if (countdownTimer) {
        let timeRemaining = parseInt(countdownTimer.dataset.time, 10);

        function updateCountdown() {
            if (timeRemaining <= 0) {
                location.reload();
                return;
            }
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;

            if (minutes > 0) {
                countdownTimer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            } else {
                countdownTimer.textContent = `${seconds}s`;
            }
            timeRemaining--;
        }

        if (!isNaN(timeRemaining)) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    }
});
