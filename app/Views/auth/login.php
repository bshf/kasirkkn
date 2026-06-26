<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CashFlow — Login</title>
  <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('fontawesome/css/all.min.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>" />
  <style>
    :root {
      --bg: #f4f6f9;
      --surface: #ffffff;
      --card: #ffffff;
      --border: #e2e8f0;
      --accent: #f0a500;
      --accent2: #2ec4b6;
      --accent3: #6c5ce7;
      --danger: #e74c3c;
      --text: #2d3748;
      --muted: #718096;
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* ── BACKGROUND DECORATION ── */
    .bg-grid {
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(var(--border) 1px, transparent 1px),
        linear-gradient(90deg, var(--border) 1px, transparent 1px);
      background-size: 48px 48px;
      opacity: .3;
      pointer-events: none;
    }

    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(90px);
      pointer-events: none;
      animation: drift 12s ease-in-out infinite alternate;
    }

    .orb-1 {
      width: 500px;
      height: 500px;
      background: rgba(240, 180, 41, .12);
      top: -160px;
      left: -160px;
    }

    .orb-2 {
      width: 400px;
      height: 400px;
      background: rgba(124, 106, 247, .10);
      bottom: -120px;
      right: -120px;
      animation-delay: -5s;
    }

    @keyframes drift {
      from {
        transform: translate(0, 0) scale(1);
      }

      to {
        transform: translate(30px, 40px) scale(1.06);
      }
    }

    /* ── LOGIN CARD ── */
    .login-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      margin: 24px 16px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 40px 36px 32px;
      box-shadow: 0 24px 64px rgba(0, 0, 0, .45);
      animation: fadeUp .5s ease;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(24px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ── BRAND ── */
    .brand {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 11px;
      margin-bottom: 28px;
    }

    .brand-icon {
      width: 42px;
      height: 42px;
      background: var(--accent);
      border-radius: 12px;
      display: grid;
      place-items: center;
      font-size: 17px;
      color: #000;
      flex-shrink: 0;
      box-shadow: 0 0 0 5px rgba(240, 180, 41, .15);
    }

    .brand-name {
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: 1.45rem;
      letter-spacing: .01em;
      color: var(--text);
    }

    .brand-name span {
      color: var(--accent);
    }

    /* ── DIVIDER ── */
    .card-divider {
      height: 1px;
      background: var(--border);
      margin: 0 -36px 28px;
    }

    /* ── HEADER ── */
    .login-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .login-header h2 {
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: 1.3rem;
      color: var(--text);
      margin-bottom: 5px;
    }

    .login-header p {
      font-size: .82rem;
      color: var(--muted);
    }

    /* ── FORM ── */
    .cf-group {
      margin-bottom: 18px;
    }

    .cf-label {
      display: block;
      font-size: .72rem;
      font-weight: 600;
      color: var(--muted);
      letter-spacing: .06em;
      text-transform: uppercase;
      margin-bottom: 7px;
    }

    .cf-input-wrap {
      position: relative;
    }

    .cf-icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: .82rem;
      pointer-events: none;
      transition: color .2s;
    }

    .cf-eye {
      position: absolute;
      right: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: .82rem;
      cursor: pointer;
      transition: color .2s;
    }

    .cf-eye:hover {
      color: var(--accent);
    }

    .cf-input {
      width: 100%;
      background: var(--card);
      border: 1.5px solid var(--border);
      color: var(--text);
      border-radius: 10px;
      padding: 11px 13px 11px 38px;
      font-family: 'DM Sans', sans-serif;
      font-size: .88rem;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }

    .cf-input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(240, 180, 41, .12);
    }

    .cf-input.is-error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(240, 82, 82, .10);
    }

    .cf-input-wrap:focus-within .cf-icon {
      color: var(--accent);
    }

    .cf-input::placeholder {
      color: var(--muted);
      opacity: .65;
    }

    .error-msg {
      font-size: .71rem;
      color: var(--danger);
      margin-top: 5px;
      display: none;
    }

    .error-msg i {
      margin-right: 3px;
    }

    /* ── EXTRAS ── */
    .form-extras {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 22px;
    }

    .cf-check {
      display: flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
    }

    .cf-check input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: var(--accent);
      cursor: pointer;
    }

    .cf-check span {
      font-size: .79rem;
      color: var(--muted);
    }

    /* ── BUTTON ── */
    .btn-login {
      width: 100%;
      background: var(--accent);
      border: none;
      color: #000;
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: .92rem;
      padding: 12px;
      border-radius: 10px;
      cursor: pointer;
      letter-spacing: .01em;
      transition: opacity .2s, transform .15s, box-shadow .2s;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-login:hover {
      opacity: .88;
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(240, 180, 41, .28);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login.loading .btn-text {
      opacity: 0;
    }

    .btn-login .spinner {
      display: none;
      position: absolute;
      width: 18px;
      height: 18px;
      border: 2px solid rgba(0, 0, 0, .25);
      border-top-color: #000;
      border-radius: 50%;
      animation: spin .65s linear infinite;
    }

    .btn-login.loading .spinner {
      display: block;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(0, 0, 0, .14);
      transform: scale(0);
      animation: rippleAnim .5s linear;
      pointer-events: none;
    }

    @keyframes rippleAnim {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }

    /* ── ALERT ── */
    .cf-alert {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 11px 14px;
      border-radius: 9px;
      font-size: .79rem;
      margin-bottom: 18px;
      animation: fadeUp .25s ease;
    }

    .cf-alert.error {
      background: rgba(240, 82, 82, .1);
      border: 1px solid rgba(240, 82, 82, .22);
      color: var(--danger);
    }

    .cf-alert.success {
      background: rgba(62, 207, 142, .1);
      border: 1px solid rgba(62, 207, 142, .22);
      color: var(--accent2);
    }

    /* ── FOOTER ── */
    .login-footer {
      text-align: center;
      margin-top: 22px;
      font-size: .73rem;
      color: var(--muted);
    }

    ::-webkit-scrollbar {
      width: 5px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--border);
      border-radius: 3px;
    }
  </style>
</head>

<body>

  <!-- Background -->
  <div class="bg-grid"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>

  <!-- ══ LOGIN CARD ══ -->
  <div class="login-card">

    <!-- Brand / Project Title -->
    <div class="brand">
      <div class="brand-icon"><i class="fa-solid fa-cash-register"></i></div>
      <span class="brand-name">Cash<span>Flow</span></span>
    </div>

    <div class="card-divider"></div>

    <!-- Header -->
    <div class="login-header">
      <h2>Selamat datang 👋</h2>
      <p>Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Flash message dari CI4 -->
    <?php if (session()->getFlashdata('error')): ?>
      <div class="cf-alert error">
        <i class="fa-solid fa-circle-xmark"></i>
        <span><?= session()->getFlashdata('error') ?></span>
      </div>
    <?php endif; ?>

    <!-- Alert AJAX -->
    <div id="alertBox" style="display:none"></div>

    <!-- Form -->
    <form id="loginForm" novalidate>
      <?= csrf_field() ?>

      <div class="cf-group">
        <label class="cf-label">Username</label>
        <div class="cf-input-wrap">
          <i class="cf-icon fa-regular fa-user"></i>
          <input class="cf-input" type="text" id="usernameInput" name="username"
            placeholder="Masukkan username" autocomplete="username"
            value="<?= old('username') ?>" />
        </div>
        <div class="error-msg" id="usernameError">
          <i class="fa-solid fa-circle-exclamation"></i>Username tidak boleh kosong.
        </div>
      </div>

      <div class="cf-group">
        <label class="cf-label">Password</label>
        <div class="cf-input-wrap">
          <i class="cf-icon fa-solid fa-lock"></i>
          <input class="cf-input" type="password" id="passwordInput" name="password"
            placeholder="Masukkan password" autocomplete="current-password" />
          <i class="cf-eye fa-regular fa-eye" id="togglePwd"></i>
        </div>
        <div class="error-msg" id="pwdError">
          <i class="fa-solid fa-circle-exclamation"></i>Password minimal 3 karakter.
        </div>
      </div>

      <div class="form-extras">
        <label class="cf-check">
          <input type="checkbox" id="rememberMe" />
          <span>Ingat saya</span>
        </label>
      </div>

      <button type="submit" class="btn-login" id="loginBtn">
        <div class="spinner"></div>
        <span class="btn-text">
          <i class="fa-solid fa-arrow-right-to-bracket me-1"></i>Masuk
        </span>
      </button>

    </form>

    <div class="login-footer">
      © <?= date('Y') ?> CashFlow &mdash; Hubungi admin jika tidak bisa masuk.
    </div>

  </div><!-- /.login-card -->

    <script src="<?= base_url('js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

  <script>
    const BASE_URL = '<?= base_url() ?>';

    /* ── Toggle password ── */
    $('#togglePwd').on('click', function() {
      const inp = $('#passwordInput');
      inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
      $(this).toggleClass('fa-eye fa-eye-slash');
    });

    /* ── Remember me: restore username ── */
    $(function() {
      const saved = localStorage.getItem('cf_username');
      if (saved) {
        $('#usernameInput').val(saved);
        $('#rememberMe').prop('checked', true);
      }
    });

    /* ── Alert helpers ── */
    function showAlert(type, msg) {
      const icon = type === 'error' ? 'fa-circle-xmark' : 'fa-circle-check';
      $('#alertBox')
        .html(`<div class="cf-alert ${type}"><i class="fa-solid ${icon}"></i><span>${msg}</span></div>`)
        .show();
    }

    function clearAlert() {
      $('#alertBox').hide().html('');
    }

    /* ── Ripple on button ── */
    $('#loginBtn').on('click', function(e) {
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const r = document.createElement('span');
      r.className = 'ripple';
      r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size/2}px;top:${e.clientY - rect.top - size/2}px`;
      this.appendChild(r);
      setTimeout(() => r.remove(), 550);
    });

    /* ── Form submit ── */
    $('#loginForm').on('submit', function(e) {
      e.preventDefault();
      clearAlert();

      const username = $('#usernameInput').val().trim();
      const pw = $('#passwordInput').val();
      let valid = true;

      if (!username) {
        $('#usernameInput').addClass('is-error');
        $('#usernameError').show();
        valid = false;
      } else {
        $('#usernameInput').removeClass('is-error');
        $('#usernameError').hide();
      }

      if (pw.length < 3) {
        $('#passwordInput').addClass('is-error');
        $('#pwdError').show();
        valid = false;
      } else {
        $('#passwordInput').removeClass('is-error');
        $('#pwdError').hide();
      }

      if (!valid) return;

      /* Remember me */
      $('#rememberMe').is(':checked') ?
        localStorage.setItem('cf_username', username) :
        localStorage.removeItem('cf_username');

      /* Loading */
      const $btn = $('#loginBtn');
      $btn.addClass('loading').prop('disabled', true);

      $.ajax({
        url: BASE_URL + 'login/attempt',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            showAlert('success', 'Login berhasil. Mengalihkan…');
            setTimeout(() => window.location.href = res.redirect, 800);
          } else {
            $btn.removeClass('loading').prop('disabled', false);
            showAlert('error', res.message || 'Login gagal, coba lagi.');
            $('#passwordInput').addClass('is-error');
          }
        },
        error: function(xhr) {
          $btn.removeClass('loading').prop('disabled', false);
          showAlert('error', xhr.responseJSON?.message || 'Terjadi kesalahan server.');
        }
      });
    });

    /* ── Clear error saat mengetik ── */
    $('#usernameInput').on('input', function() {
      $(this).removeClass('is-error');
      $('#usernameError').hide();
      clearAlert();
    });
    $('#passwordInput').on('input', function() {
      $(this).removeClass('is-error');
      $('#pwdError').hide();
      clearAlert();
    });
  </script>
</body>

</html>