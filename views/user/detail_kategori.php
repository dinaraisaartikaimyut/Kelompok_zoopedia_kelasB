<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: /zoopedia/views/user/kategori.php');
    exit;
}

$qKat = mysqli_query($conn, "SELECT * FROM kategori WHERE id = $id");
$kategori = mysqli_fetch_assoc($qKat);

if (!$kategori) {
    header('Location: /zoopedia/views/user/kategori.php');
    exit;
}

$qHewan = mysqli_query($conn, "SELECT * FROM hewan WHERE kategori_id = $id ORDER BY nama ASC");
$hewan = mysqli_fetch_all($qHewan, MYSQLI_ASSOC);

$title = $kategori['nama'] . ' - Zoopedia';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
</head>
<body>

  <?php include '../partials/navbar.php'; ?>

  <div class="section">
    <a class="back-link" href="/zoopedia/views/user/kategori.php">← Kembali ke Kategori</a>

    <div class="kategori-header">
      <h2 class="kategori-title"><?= $kategori['nama'] ?></h2>
      <p class="kategori-desc"><?= $kategori['deskripsi'] ?></p>
    </div>

    <p class="section-title">Hewan dalam Kategori Ini</p>
    <div class="hewan-grid">
      <?php foreach ($hewan as $h): ?>
        <div class="hewan-card">
          <div class="hewan-top">
            <img src="/zoopedia/public/images/hewan/<?= $h['gambar'] ?>"
                 alt="<?= $h['nama'] ?>"
                 class="img-hewan"
                 onerror="this.style.display='none'" />
          </div>
          <div class="hewan-body">
            <h4><?= $h['nama'] ?></h4>
            <p><?= $h['info'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>