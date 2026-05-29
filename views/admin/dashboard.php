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
 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$title = 'Dashboard Admin - Zoopedia';
 
$totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$totalKategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total'];
$totalHewan    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hewan"))['total'];
$totalSoal     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM soal"))['total'];
$totalHasil    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hasil_kuis"))['total'];
 
$qHasil = mysqli_query($conn, "SELECT h.*, u.nama AS nama_user 
                                FROM hasil_kuis h 
                                JOIN users u ON h.user_id = u.id 
                                ORDER BY h.created_at DESC 
                                LIMIT 5");
$hasilTerbaru = mysqli_fetch_all($qHasil, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
</head>
<body>
 
  <?php include '../partials/navbar_admin.php'; ?>
 
  <div class="section">
    <div class="admin-header">
      <div>
        <h2 class="admin-title">Dashboard Admin</h2>
        <p class="admin-subtitle">Selamat datang, <?= $_SESSION['user']['nama'] ?>!</p>
      </div>
    </div>
 
    <p class="section-title">Statistik Zoopedia</p>
    <div class="cards-grid-5">
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/Statistik Zoopedia/total_User.png" alt="Total User" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Total User</h3>
          <p class="card-value"><?= $totalUser ?></p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/Statistik Zoopedia/total_Kategori.png" alt="Total Kategori" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Total Kategori</h3>
          <p class="card-value"><?= $totalKategori ?></p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/Statistik Zoopedia/total_Hewan.png" alt="Total Hewan" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Total Hewan</h3>
          <p class="card-value"><?= $totalHewan ?></p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/Statistik Zoopedia/total_Soal.png" alt="Total Soal" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Total Soal</h3>
          <p class="card-value"><?= $totalSoal ?></p>
        </div>
      </div>
 
      <div class="card feature-card">
        <img src="/zoopedia/public/images/Statistik Zoopedia/total_HasilKuis.png" alt="Total Hasil Kuis" class="feature-img" onerror="this.style.display='none'" />
        <div class="card-content">
          <h3>Total Hasil Kuis</h3>
          <p class="card-value"><?= $totalHasil ?></p>
        </div>
      </div>
 
    </div>
 
    <p class="section-title">Hasil Kuis Terbaru</p>
    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Skor</th>
            <th>Total Soal</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($hasilTerbaru)): ?>
            <tr>
              <td colspan="4" class="table-empty">
                Belum ada hasil kuis
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($hasilTerbaru as $h): ?>
              <tr>
                <td><?= htmlspecialchars($h['nama_user']) ?></td>
                <td class="font-weight-bold" style="color:var(--accent2);"><?= $h['skor'] ?></td>
                <td><?= $h['total_soal'] ?></td>
                <td class="text-muted"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
 
</body>
</html>
