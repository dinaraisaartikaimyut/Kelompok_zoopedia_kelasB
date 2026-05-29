<div class="navbar">
  <div class="logo">
    <a href="/zoopedia/views/admin/dashboard.php">
      <span class="z">Zoo</span><span class="p">pedia</span>
      <span style="font-size:11px; color:var(--danger); font-weight:600;">ADMIN</span>
    </a>
  </div>
  <nav>
    <a href="/zoopedia/views/admin/dashboard.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
      Dashboard
    </a>
    <a href="/zoopedia/views/admin/kategori.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : '' ?>">
      Kategori
    </a>
    <a href="/zoopedia/views/admin/hewan.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'hewan.php' ? 'active' : '' ?>">
      Hewan
    </a>
    <a href="/zoopedia/views/admin/soal.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'soal.php' ? 'active' : '' ?>">
      Soal Kuis
    </a>
    <a href="/zoopedia/views/admin/users.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
      Users
    </a>
    <a href="/zoopedia/views/admin/hasil_kuis.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'hasil_kuis.php' ? 'active' : '' ?>">
      Hasil Kuis
    </a>
  </nav>
  <div class="user-info">
    <span class="uname"><?= $_SESSION['user']['nama'] ?></span>
    <a href="/zoopedia/controllers/AuthController.php?action=logout" class="btn-logout">Keluar</a>
  </div>
</div>
