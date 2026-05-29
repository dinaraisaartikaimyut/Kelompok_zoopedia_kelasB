<div class="navbar">
  <div class="logo">
    <a href="/zoopedia/views/user/beranda.php">
      <span class="z">Zoo</span><span class="p">pedia</span>
    </a>
  </div>
  <nav>
    <a href="/zoopedia/views/user/beranda.php" 
       class="<?= basename($_SERVER['PHP_SELF']) == 'beranda.php' ? 'active' : '' ?>">
      Beranda
    </a>
    <a href="/zoopedia/views/user/kategori.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : '' ?>">
      Kategori
    </a>
    <a href="/zoopedia/views/user/kuis.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'kuis.php' ? 'active' : '' ?>">
      Kuis
    </a>
  </nav>
  <div class="user-info">
    <?php if (isset($_SESSION['user'])): ?>
      Halo, <span class="uname"><?= $_SESSION['user']['nama'] ?></span>
      <a href="/zoopedia/controllers/AuthController.php?action=logout" class="btn-logout">Keluar</a>
    <?php else: ?>
      <a href="/zoopedia/views/user/login.php" class="btn btn-primary" style="padding:6px 16px; font-size:13px;">Masuk</a>
    <?php endif; ?>
  </div>
</div>
