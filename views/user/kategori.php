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
 
$title = 'Kategori Hewan - Zoopedia';
 
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
 
  <div class="page-header">
    <h2>Kategori Hewan</h2>
    <p>Pilih kategori untuk menjelajahi hewan-hewan beserta info uniknya</p>
  </div>
 
  <div class="section">
    <div class="kategori-grid">
      <?php foreach ($kategori as $kat): ?>
        <a href="/zoopedia/views/user/detail_kategori.php?id=<?= $kat['id'] ?>" class="kategori-card">
          <div class="kat-banner">
            <?php if (!empty($kat['gambar'])): ?>
              <img src="/zoopedia/public/images/kategori/<?= htmlspecialchars($kat['gambar']) ?>"
                   alt="<?= htmlspecialchars($kat['nama']) ?>"
                   onerror="this.style.display='none'" />
            <?php endif; ?>
          </div>
          <div class="kat-info">
            <h3><?= $kat['nama'] ?></h3>
            <p><?= $kat['deskripsi'] ?></p>
            <span class="kat-tag"><?= $kat['jumlah_hewan'] ?> hewan</span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
 
</body>
</html>
