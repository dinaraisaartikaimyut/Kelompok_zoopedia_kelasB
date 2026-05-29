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
require_once __DIR__ . '/../../config/koneksi.php';
 
if (!isset($_SESSION['user'])) {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$title = 'Beranda - Zoopedia';
 
$query = mysqli_query($conn, "SELECT k.*, COUNT(h.id) AS jumlah_hewan 
                               FROM kategori k 
                               LEFT JOIN hewan h ON h.kategori_id = k.id 
                               GROUP BY k.id 
                               ORDER BY k.nama ASC");
$kategori = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
</head>
<body>
 
  <?php include '../partials/navbar.php'; ?>
 
  <div class="hero" style="display:flex; align-items:center; justify-content:space-between; gap:32px;">
    <div>
      <h1>Selamat Datang di<br>
        <span class="hl">Zoo</span><span class="hl2">pedia</span>
      </h1>
      <p>Pelajari fakta-fakta seru tentang dunia hewan lewat kuis mitos atau fakta yang interaktif dan menyenangkan.</p>
      <div class="hero-btns">
        <a href="/zoopedia/views/user/kuis.php" class="btn btn-primary">Mulai Kuis</a>
        <a href="/zoopedia/views/user/kategori.php" class="btn btn-outline">Lihat Kategori Hewan</a>
      </div>
    </div>
    <div style="flex-shrink:0;">
      <img src="/zoopedia/public/images/dashboard_user/tampilan_awal.png"
           alt="Zoopedia"
           class="hero-img"
           onerror="this.style.display='none'" />
    </div>
  </div>
 
  <div class="section">
    <p class="section-title">Eksplorasi Zoopedia</p>
    <div class="cards-grid">
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/eksplorasi_zoopedia/mitos-fakta.png" alt="Kuis" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Kuis Mitos atau Fakta</h3>
          <p>Jawab pertanyaan seru tentang hewan. Mana yang mitos, mana yang fakta?</p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/eksplorasi_zoopedia/kategori-hewan.png" alt="Kategori" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Kategori Hewan</h3>
          <p>Jelajahi Mamalia, Reptil, Amfibi, Burung, Ikan, dan Serangga.</p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/eksplorasi_zoopedia/fakta-unik.png" alt="Fakta" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Fakta Unik</h3>
          <p>Tiap jawaban dilengkapi penjelasan ilmiah yang mudah dipahami.</p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/eksplorasi_zoopedia/belajar&main.png" alt="Belajar" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Belajar Sambil Main</h3>
          <p>Sistem kuis interaktif yang bikin belajar jadi nggak membosankan.</p>
        </div>
      </div>
 
    </div>
  </div>
 
  <hr class="divider" />
 
  <div class="section">
    <p class="section-title">Kategori Populer</p>
    <div class="cards-grid">
      <?php foreach (array_slice($kategori, 0, 4) as $kat): ?>
        <a href="/zoopedia/views/user/detail_kategori.php?id=<?= $kat['id'] ?>" class="card feature-card">
          <img src="/zoopedia/public/images/kategori/<?= htmlspecialchars($kat['gambar']) ?>"
               alt="<?= htmlspecialchars($kat['nama']) ?>"
               class="feature-img"
               onerror="this.style.display='none'" />
          <div class="card-content">
            <h3><?= $kat['nama'] ?></h3>
            <p><?= $kat['deskripsi'] ?></p>
            <span class="card-arrow">Lihat →</span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
 
</body>
</html>
