/* Shared helpers for static auth pages (login.html / otp_verification.html / test.html).
 * Talks to php/ JSON proxies so PHP sessions + api_helper stay server-side. */
(function () {
  function base() {
    return (window.APP_CONFIG && window.APP_CONFIG.PHP_BASE_URL) || 'php';
  }

  function showError(boxId, message) {
    var box = document.getElementById(boxId);
    if (!box) return;
    var msg = document.getElementById(boxId + '-msg');
    // Toggle the hidden ATTRIBUTE (not a class) - the consolidated CSS now has
    // [hidden] { display: none !important; } so attribute toggling is required.
    box.hidden = !message;
    if (msg) msg.textContent = message || '';
  }

  async function postJSON(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    });
    let data = {};
    try { data = await res.json(); } catch (e) { data = { status: 'error', message: 'Unexpected server response.' }; }
    if (!res.ok && !data.message) data.message = 'Request failed (' + res.status + ').';
    return data;
  }

  function redirectFor(data, fallback) {
    if (data && data.redirect) { window.location.href = data.redirect; return; }
    if (data && data.role) {
      var map = {
        sales: 'src/views/sales_agent/dashboard.php',
        sales_agent: 'src/views/sales_agent/dashboard.php',
        admin: 'src/views/admin/dashboard.php',
        administrator: 'src/views/admin/dashboard.php',
        super_admin: 'src/views/super_admin/dashboard.php',
        customer: 'src/views/customer/dashboard.php'
      };
      if (map[data.role]) { window.location.href = map[data.role]; return; }
    }
    if (fallback) window.location.href = fallback;
  }

  function rememberRole(data) {
    try {
      if (data && data.role) sessionStorage.setItem('role', data.role);
    } catch (e) { /* storage unavailable */ }
  }

  function bindLogin(formId, boxId) {
    var form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      showError(boxId, '');
      var btn = form.querySelector('[type="submit"]');
      if (btn) { btn.disabled = true; btn.classList.add('opacity-60'); }
      var email = (form.querySelector('[name="email"]') || {}).value || '';
      var password = (form.querySelector('[name="password"]') || {}).value || '';
      try {
        const data = await postJSON(base() + '/login.php', { email: email.trim(), password: password });
        if (data.status === 'otp_sent') { window.location.href = data.redirect || 'otp_verification.html'; return; }
        if (data.status === 'ok') { rememberRole(data); redirectFor(data, 'src/views/customer/dashboard.php'); return; }
        showError(boxId, data.message || 'Wrong email or Password.');
      } catch (err) {
        showError(boxId, 'Cannot reach server. Check that Apache/PHP is running.');
      } finally {
        if (btn) { btn.disabled = false; btn.classList.remove('opacity-60'); }
      }
    });
  }

  function bindOtp(formId, boxId) {
    var form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      showError(boxId, '');
      var btn = form.querySelector('[type="submit"]');
      if (btn) { btn.disabled = true; btn.classList.add('opacity-60'); }
      var code = (form.querySelector('[name="otp_code"]') || {}).value || '';
      try {
        const data = await postJSON(base() + '/otp_verify.php', { otp_code: code.trim() });
        if (data.status === 'ok') { rememberRole(data); redirectFor(data, 'src/views/customer/dashboard.php'); return; }
        if (data.redirect && data.status === 'error' && data.redirect.indexOf('login.html') !== -1) {
          window.location.href = data.redirect;
          return;
        }
        showError(boxId, data.message || 'Invalid OTP or Expired.');
      } catch (err) {
        showError(boxId, 'Cannot reach server. Check that Apache/PHP is running.');
      } finally {
        if (btn) { btn.disabled = false; btn.classList.remove('opacity-60'); }
      }
    });
  }

  function showStatusFromQuery(boxId) {
    try {
      var q = new URLSearchParams(window.location.search);
      if (q.get('status') === 'session_expired') showError(boxId, 'Session expired. Please sign in again.');
    } catch (e) { /* ignore */ }
  }

  window.AuthPages = { showError: showError, postJSON: postJSON, bindLogin: bindLogin, bindOtp: bindOtp, showStatusFromQuery: showStatusFromQuery, redirectFor: redirectFor };
})();
