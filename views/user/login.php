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
$title = 'Login - Zoopedia';
 
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: /zoopedia/views/admin/dashboard.php');
    } else {
        header('Location: /zoopedia/views/user/beranda.php');
    }
    exit;
}
 
$error   = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
<body>
  <div class="auth-page">
    <div class="auth-box">
      <div class="auth-logo"><span class="z">Zoo</span><span class="p">pedia</span></div>
      <h2>Masuk ke Akun</h2>
      <p class="sub">Selamat datang kembali! Yuk lanjut belajar.</p>
 
      <form action="/zoopedia/controllers/AuthController.php?action=login" method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Masukkan username" />
        </div>
 
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Masukkan password" />
        </div>
 
        <?php if ($error): ?>
          <p class="error-msg" style="display:block"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
 
        <?php if ($success): ?>
          <p class="success-msg" style="display:block"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
 
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:6px;">Masuk</button>
      </form>
 
      <div class="auth-link">
        Belum punya akun? <a href="/zoopedia/views/user/register.php">Daftar di sini</a>
      </div>
    </div>
  </div>
</body>
</html>
 
