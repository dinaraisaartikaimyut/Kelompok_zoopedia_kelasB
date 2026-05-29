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
require_once __DIR__ . '/../../models/Kuis.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: /zoopedia/views/admin/hasil_kuis.php');
    exit;
}

$kuisModel = new Kuis($conn);
$hasil  = $kuisModel->findById($id);
$detail = $kuisModel->findDetail($id);

if (!$hasil) {
    header('Location: /zoopedia/views/admin/hasil_kuis.php');
    exit;
}

$title = 'Detail Hasil Kuis - Zoopedia';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <?php include '../partials/head.php'; ?>
</head>
<body>

  <?php include '../partials/navbar_admin.php'; ?>

  <div class="section">
    <a class="back-link" href="/zoopedia/views/admin/hasil_kuis.php">← Kembali ke Hasil Kuis</a>

    <div class="info-box">
      <div class="info-row">
        <div class="info-item">
          <span class="info-label">User</span>
          <p class="info-value"><?= htmlspecialchars($hasil['nama_user']) ?></p>
        </div>
        <div class="info-item">
          <span class="info-label">Username</span>
          <p class="info-value">@<?= htmlspecialchars($hasil['username']) ?></p>
        </div>
        <div class="info-item">
          <span class="info-label">Skor</span>
          <p class="info-value highlight"><?= $hasil['skor'] ?>/<?= $hasil['total_soal'] ?></p>
        </div>
        <div class="info-item">
          <span class="info-label">Tanggal</span>
          <p class="info-value"><?= date('d M Y H:i', strtotime($hasil['created_at'])) ?></p>
        </div>
      </div>
    </div>

    <p class="section-title">Detail Jawaban</p>
    <div class="rekap-list">
      <?php foreach ($detail as $i => $d): ?>
        <div class="rekap-item <?= $d['adalah_benar'] ? 'rekap-benar' : 'rekap-salah' ?>">
          <div class="rekap-nomor"><?= $i + 1 ?></div>
          <div class="rekap-isi">
            <div class="rekap-badge"><?= $d['adalah_benar'] ? '✓ Benar' : '✕ Salah' ?></div>
            <div class="rekap-soal"><?= htmlspecialchars($d['pertanyaan']) ?></div>
            <div class="rekap-keterangan">
              Jawaban user: <strong><?= strtoupper($d['jawaban_user']) ?></strong>
              &nbsp;·&nbsp;
              Jawaban benar: <strong><?= strtoupper($d['jawaban_benar']) ?></strong>
            </div>
            <div class="rekap-penjelasan"><?= htmlspecialchars($d['penjelasan']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
