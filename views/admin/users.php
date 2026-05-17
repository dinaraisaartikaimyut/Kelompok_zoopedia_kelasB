<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$title = 'Kelola Users - Zoopedia';
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$userModel = new User($conn);
$users = $userModel->findAll();
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
        <h2 class="admin-title">Kelola Users</h2>
        <p class="admin-subtitle">Total <?= count($users) ?> user</p>
      </div>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success">
        ✓ <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>Tanggal Daftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $u): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td class="font-weight-bold"><?= htmlspecialchars($u['nama']) ?></td>
              <td class="text-muted"><?= htmlspecialchars($u['username']) ?></td>
              <td>
                <?php if ($u['role'] === 'admin'): ?>
                  <span class="badge badge-danger">ADMIN</span>
                <?php else: ?>
                  <span class="badge badge-info">USER</span>
                <?php endif; ?>
              </td>
              <td class="text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <?php if ($u['role'] !== 'admin'): ?>
                  <form action="/zoopedia/controllers/UserController.php" method="POST" onsubmit="return confirm('Hapus user ini?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn-sm btn-delete">Hapus</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted text-small">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>