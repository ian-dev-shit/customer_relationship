
const INACTIVITY_LIMIT = 30 * 60 * 1000; 
let inactivityTimer;

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(triggerAutoLogout, INACTIVITY_LIMIT);
}

function triggerAutoLogout() {
    // 1. Burahin ang nakatagong data sa browser
    localStorage.clear();
    sessionStorage.clear();

    // 2. Tawagin ang iyong frontend/logout.php
    fetch('../logout.php', { method: 'POST' })
        .then(() => {
            // 3. Automatic redirect sa login page
            window.location.href = '../login.php?status=session_expired';
        })
        .catch(() => {
            // Fallback redirect kung sakaling mag-fail ang fetch
            window.location.href = '../login.php?status=session_expired';
        });
}

// Event listeners para sa aksyon ng user
window.addEventListener('load', resetInactivityTimer);
document.addEventListener('mousemove', resetInactivityTimer);
document.addEventListener('keydown', resetInactivityTimer);
document.addEventListener('click', resetInactivityTimer);
document.addEventListener('scroll', resetInactivityTimer, true);
document.addEventListener('touchstart', resetInactivityTimer);