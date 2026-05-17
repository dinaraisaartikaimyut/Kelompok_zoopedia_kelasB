<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/Kuis.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$title = 'Hasil Kuis - Zoopedia';

$kuisModel = new Kuis($conn);
$hasil = $kuisModel->findAll();
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
        <h2 class="admin-title">Hasil Kuis</h2>
        <p class="admin-subtitle">Total <?= count($hasil) ?> hasil kuis</p>
      </div>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>User</th>
            <th>Username</th>
            <th>Skor</th>
            <th>Total Soal</th>
            <th>Persentase</th>
            <th>Tanggal</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($hasil)): ?>
            <tr><td colspan="8" class="table-empty">Belum ada hasil kuis</td></tr>
          <?php else: ?>
            <?php foreach ($hasil as $i => $h): ?>
              <?php $persen = round(($h['skor'] / $h['total_soal']) * 100); ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td class="font-weight-bold"><?= htmlspecialchars($h['nama_user']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($h['username']) ?></td>
                <td class="font-weight-bold" style="color:var(--accent2);"><?= $h['skor'] ?></td>
                <td><?= $h['total_soal'] ?></td>
                <td>
                  <span class="font-weight-bold" style="color:<?= $persen >= 60 ? 'var(--accent2)' : 'var(--danger)' ?>;">
                    <?= $persen ?>%
                  </span>
                </td>
                <td class="text-muted"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
                <td>
                  <a href="/zoopedia/views/admin/detail_hasil.php?id=<?= $h['id'] ?>" class="btn-sm btn-detail">
                    Lihat Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>