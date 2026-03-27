<?php
session_start();
require_once __DIR__ . '/config/db.php';

define('ADMIN_URL', '/nayagara-tours/admin');
define('SITE_URL',  '/nayagara-tours');

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

$logoPath = __DIR__ . '/assets/images/logo.png';
$logoUrl  = ADMIN_URL . '/assets/images/logo.png';
$hasLogo  = file_exists($logoPath);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $pdo = getPDO();

        // Check if username column exists; if not, redirect to setup
        $cols = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'username'")->fetchAll();
        if (empty($cols)) {
            header('Location: ' . ADMIN_URL . '/setup.php');
            exit;
        }

        $stmt = $pdo->prepare('SELECT id, name, password, role FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: ' . ADMIN_URL . '/index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

// Slider slides
$slides = [
    [
        'image'    => SITE_URL . '/assets/images/hero/slide-1.jpg',
        'heading'  => 'Discover Sri Lanka',
        'subtext'  => 'Pristine beaches, ancient temples & lush highlands',
    ],
    [
        'image'    => SITE_URL . '/assets/images/hero/slide-2.jpg',
        'heading'  => 'Unforgettable Journeys',
        'subtext'  => 'Tailor-made tours crafted just for your adventure',
    ],
    [
        'image'    => SITE_URL . '/assets/images/hero/slide-3.jpg',
        'heading'  => 'Manage With Ease',
        'subtext'  => 'Your powerful dashboard for Nayagara Tours',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | Nayagara Tours</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, sans-serif;
      display: flex;
      background: #03045E;
      overflow: hidden;
    }

    /* LEFT — SLIDER PANEL*/
    .login-left {
      flex: 1;
      position: relative;
      overflow: hidden;
    }

    /* Each slide */
    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      animation: slideShow 18s infinite;
      background-size: cover;
      background-position: center;
    }

    .slide:nth-child(1) { animation-delay: 0s; }
    .slide:nth-child(2) { animation-delay: 6s; }
    .slide:nth-child(3) { animation-delay: 12s; }

    @keyframes slideShow {
      0%   { opacity: 0;   transform: scale(1.08); }
      5%   { opacity: 1;   transform: scale(1.05); }
      28%  { opacity: 1;   transform: scale(1); }
      33%  { opacity: 0;   transform: scale(1); }
      100% { opacity: 0;   transform: scale(1); }
    }

    /* Dark overlay on each slide */
    .slide::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(
        160deg,
        rgba(3,4,94,.15) 0%,
        rgba(3,4,94,.78) 100%
      );
    }

    /* Slide text */
    .slide-content {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      padding: 3rem;
      z-index: 2;
      opacity: 0;
      transform: translateY(20px);
      animation: textFade 18s infinite;
    }

    .slide:nth-child(1) .slide-content { animation-delay: 0s; }
    .slide:nth-child(2) .slide-content { animation-delay: 6s; }
    .slide:nth-child(3) .slide-content { animation-delay: 12s; }

    @keyframes textFade {
      0%   { opacity: 0; transform: translateY(20px); }
      8%   { opacity: 1; transform: translateY(0); }
      28%  { opacity: 1; transform: translateY(0); }
      33%  { opacity: 0; transform: translateY(-10px); }
      100% { opacity: 0; transform: translateY(20px); }
    }

    .slide-content h2 {
      font-size: 2.4rem;
      font-weight: 800;
      color: #fff;
      line-height: 1.2;
      margin-bottom: .6rem;
      text-shadow: 0 2px 12px rgba(0,0,0,.4);
    }

    .slide-content p {
      font-size: 1rem;
      color: rgba(255,255,255,.8);
      max-width: 380px;
    }

    /* Slide dots */
    .slide-dots {
      position: absolute;
      bottom: 1.5rem;
      right: 2rem;
      display: flex;
      gap: .45rem;
      z-index: 10;
    }

    .dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: rgba(255,255,255,.35);
      animation: dotActive 18s infinite;
    }

    .dot:nth-child(1) { animation-delay: 0s; }
    .dot:nth-child(2) { animation-delay: 6s; }
    .dot:nth-child(3) { animation-delay: 12s; }

    @keyframes dotActive {
      0%   { background: rgba(255,255,255,.35); width: 8px; }
      5%   { background: #00B4D8; width: 22px; border-radius: 4px; }
      28%  { background: #00B4D8; width: 22px; border-radius: 4px; }
      33%  { background: rgba(255,255,255,.35); width: 8px; border-radius: 50%; }
      100% { background: rgba(255,255,255,.35); width: 8px; }
    }

    /* Brand badge top-left */
    .slide-brand {
      position: absolute;
      top: 2rem; left: 2rem;
      z-index: 10;
      display: flex;
      align-items: center;
      gap: .65rem;
      color: #fff;
    }

    .slide-brand-icon {
      width: 40px; height: 40px;
      background: rgba(255,255,255,.15);
      backdrop-filter: blur(8px);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
      border: 1px solid rgba(255,255,255,.25);
    }

    .slide-brand-text {
      font-size: .95rem;
      font-weight: 700;
      text-shadow: 0 1px 6px rgba(0,0,0,.4);
    }

    /* Feature pills */
    .slide-features {
      position: absolute;
      bottom: 6rem; left: 3rem;
      z-index: 10;
      display: flex;
      gap: .6rem;
      flex-wrap: wrap;
    }

    .feature-pill {
      background: rgba(255,255,255,.12);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 999px;
      padding: .3rem .85rem;
      color: rgba(255,255,255,.9);
      font-size: .78rem;
      display: flex;
      align-items: center;
      gap: .35rem;
    }

    /* RIGHT — LOGIN FORM */
    .login-right {
      width: 460px;
      min-height: 100vh;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem;
      flex-shrink: 0;
      position: relative;
      z-index: 5;
      box-shadow: -12px 0 40px rgba(3,4,94,.25);
    }

    /* Animated left border */
    .login-right::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, #03045E 0%, #0077B6 50%, #00B4D8 100%);
      background-size: 100% 200%;
      animation: borderFlow 3s ease infinite alternate;
    }

    @keyframes borderFlow {
      0%   { background-position: 0% 0%; }
      100% { background-position: 0% 100%; }
    }

    .login-box { width: 100%; }

    /* Logo */
    .login-logo-wrap {
      display: flex;
      align-items: center;
      gap: .75rem;
      margin-bottom: 2rem;
    }

    .login-logo-icon {
      width: 50px; height: 50px;
      background: linear-gradient(135deg, #0077B6, #00B4D8);
      border-radius: 13px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem;
      color: #fff;
      flex-shrink: 0;
      box-shadow: 0 4px 14px rgba(0,119,182,.35);
    }

    .login-logo-img { max-height: 48px; max-width: 180px; object-fit: contain; }

    /* Headings */
    .login-heading {
      font-size: 1.65rem;
      font-weight: 800;
      color: #03045E;
      margin-bottom: .3rem;
    }

    .login-sub {
      font-size: .875rem;
      color: #718096;
      margin-bottom: 2rem;
    }

    /* Field */
    .field-group { margin-bottom: 1.25rem; }

    .field-label {
      display: block;
      font-size: .78rem;
      font-weight: 700;
      color: #4a5568;
      margin-bottom: .4rem;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .field-input-wrap { position: relative; }

    .field-input-wrap .fi-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
      font-size: 1rem;
      pointer-events: none;
      transition: color .2s;
    }

    .field-input {
      width: 100%;
      padding: .78rem 1rem .78rem 2.75rem;
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      font-size: .9rem;
      color: #2d3748;
      background: #f7fafc;
      transition: border-color .2s, background .2s, box-shadow .2s;
      outline: none;
    }

    .field-input:focus {
      border-color: #0077B6;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(0,119,182,.12);
    }

    .field-input:focus + .fi-icon,
    .field-input-wrap:focus-within .fi-icon {
      color: #0077B6;
    }

    .toggle-pw {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #a0aec0;
      font-size: 1rem;
      padding: 0;
      transition: color .2s;
    }
    .toggle-pw:hover { color: #0077B6; }

    /* Error */
    .login-error {
      background: #fff5f5;
      border: 1px solid #feb2b2;
      border-radius: 10px;
      padding: .75rem 1rem;
      display: flex;
      align-items: center;
      gap: .5rem;
      color: #c53030;
      font-size: .875rem;
      margin-bottom: 1.25rem;
      animation: shake .4s ease;
    }

    @keyframes shake {
      0%,100% { transform: translateX(0); }
      20%      { transform: translateX(-6px); }
      40%      { transform: translateX(6px); }
      60%      { transform: translateX(-4px); }
      80%      { transform: translateX(4px); }
    }

    /* Submit */
    .btn-signin {
      width: 100%;
      padding: .85rem;
      background: linear-gradient(135deg, #0077B6 0%, #00B4D8 100%);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: .95rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      transition: opacity .2s, transform .15s, box-shadow .2s;
      margin-top: 1.5rem;
      box-shadow: 0 4px 14px rgba(0,119,182,.4);
      position: relative;
      overflow: hidden;
    }

    .btn-signin::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
      opacity: 0;
      transition: opacity .2s;
    }

    .btn-signin:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,119,182,.5); }
    .btn-signin:hover::after { opacity: 1; }
    .btn-signin:active { transform: translateY(0); }

    /* Note */
    .login-note {
      text-align: center;
      margin-top: 1.75rem;
      font-size: .78rem;
      color: #a0aec0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .35rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .login-left { display: none; }
      .login-right { width: 100%; padding: 2rem 1.5rem; }
    }
  </style>
</head>
<body>

  <!-- LEFT — Animated Slider -->
  <div class="login-left">

    <!-- Brand badge -->
    <div class="slide-brand">
      <div class="slide-brand-icon"><i class="bi bi-compass"></i></div>
      <div class="slide-brand-text">Nayagara Tours</div>
    </div>

    <!-- Feature pills -->
    <div class="slide-features">
      <div class="feature-pill"><i class="bi bi-suitcase-lg"></i> Packages</div>
      <div class="feature-pill"><i class="bi bi-calendar-check"></i> Bookings</div>
      <div class="feature-pill"><i class="bi bi-images"></i> Gallery</div>
      <div class="feature-pill"><i class="bi bi-star"></i> Reviews</div>
    </div>

    <!-- Slides -->
    <?php foreach ($slides as $slide): ?>
      <div class="slide" style="background-image:url('<?= $slide['image'] ?>')">
        <div class="slide-content">
          <h2><?= $slide['heading'] ?></h2>
          <p><?= $slide['subtext'] ?></p>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Dots -->
    <div class="slide-dots">
      <?php foreach ($slides as $_): ?>
        <div class="dot"></div>
      <?php endforeach; ?>
    </div>

  </div>

  <!-- RIGHT — Login Form -->
  <div class="login-right">
    <div class="login-box">

      <!-- Logo -->
      <div class="login-logo-wrap">
        <?php if ($hasLogo): ?>
          <img src="<?= $logoUrl ?>" alt="Nayagara Tours" class="login-logo-img" />
        <?php else: ?>
          <div class="login-logo-icon"><i class="bi bi-compass"></i></div>
          <div>
            <div style="font-weight:800;font-size:1.05rem;color:#03045E;line-height:1.1;">Nayagara Tours</div>
            <div style="font-size:.75rem;color:#718096;">Admin Panel</div>
          </div>
        <?php endif; ?>
      </div>

      <h1 class="login-heading">Welcome back</h1>
      <p class="login-sub">Sign in to your admin account</p>

      <?php if ($error): ?>
        <div class="login-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">

        <div class="field-group">
          <label class="field-label" for="username">Username</label>
          <div class="field-input-wrap">
            <input
              type="text"
              id="username"
              name="username"
              class="field-input"
              placeholder="Enter your username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              autocomplete="username"
              required
              autofocus
            />
            <i class="bi bi-person fi-icon"></i>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label" for="password">Password</label>
          <div class="field-input-wrap">
            <input
              type="password"
              id="password"
              name="password"
              class="field-input"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            />
            <i class="bi bi-lock fi-icon"></i>
            <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1">
              <i class="bi bi-eye" id="pwEyeIcon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-signin">
          <i class="bi bi-box-arrow-in-right"></i> Sign In
        </button>

      </form>

      <div class="login-note">
        <i class="bi bi-shield-lock-fill"></i>
        Secured admin area &mdash; Nayagara Tours
      </div>

    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('pwEyeIcon');
      input.type  = input.type === 'password' ? 'text' : 'password';
      icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }
  </script>

</body>
</html>
