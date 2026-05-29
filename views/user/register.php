<?php
session_start();
$timeout = 30; 

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
        session_unset();
        session_destroy();

        header("Location: /zoopedia/views/user/login.php?pesan=timeout");
        exit;
    }
}

$_SESSION['LAST_ACTIVITY'] = time();
$title = 'Register - Zoopedia';

if (isset($_SESSION['user'])) {
    header('Location: /zoopedia/views/user/beranda.php');
    exit;
}

$error       = $_SESSION['error'] ?? '';
$error_field = $_SESSION['error_field'] ?? '';
$success     = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['error_field']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
</head>
<body>
  <div class="auth-page">
    <div class="auth-box">
      <div class="auth-logo"><span class="z">Zoo</span><span class="p">pedia</span></div>
      <h2>Buat Akun Baru</h2>
      <p class="sub">Daftar gratis dan mulai bermain kuis hewan!</p>

      <?php if ($success): ?>
        <p class="success-msg" style="display:block"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <?php if ($error && !$error_field): ?>
        <p class="error-msg" style="display:block"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="/zoopedia/controllers/AuthController.php?action=register" method="POST">

        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama" placeholder="Masukkan nama kamu" />
          <?php if ($error_field === 'nama'): ?>
            <p style="color:var(--danger); font-size:12px; margin-top:4px;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Minimal 3 karakter, huruf & angka" />
          <?php if ($error_field === 'username'): ?>
            <p style="color:var(--danger); font-size:12px; margin-top:4px;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="6 digit angka" />
          <?php if ($error_field === 'password'): ?>
            <p style="color:var(--danger); font-size:12px; margin-top:4px;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label>Konfirmasi Password</label>
          <input type="password" name="konfirmasi" placeholder="Ulangi password" />
          <?php if ($error_field === 'konfirmasi'): ?>
            <p style="color:var(--danger); font-size:12px; margin-top:4px;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:6px;">Daftar Sekarang</button>
      </form>

      <div class="auth-link">
        Sudah punya akun? <a href="/zoopedia/views/user/login.php">Masuk di sini</a>
      </div>
    </div>
  </div>
</body>
</html>
