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
require_once __DIR__ . '/../../models/Hewan.php';
require_once __DIR__ . '/../../models/Kategori.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$title = 'Kelola Hewan - Zoopedia';
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$hewanModel    = new Hewan($conn);
$kategoriModel = new Kategori($conn);
$hewan    = $hewanModel->findAll();
$kategori = $kategoriModel->findAll();
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
        <h2 class="admin-title">Kelola Hewan</h2>
        <p class="admin-subtitle">Total <?= count($hewan) ?> hewan</p>
      </div>
      <button onclick="document.getElementById('modalTambah').classList.add('show')" class="btn btn-primary">
        + Tambah Hewan
      </button>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success">
        ✓ <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error">
        ✕ <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Info</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($hewan)): ?>
            <tr><td colspan="6" class="table-empty">Belum ada hewan</td></tr>
          <?php else: ?>
            <?php foreach ($hewan as $i => $h): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <?php if (!empty($h['gambar'])): ?>
                    <img src="/zoopedia/public/images/hewan/<?= htmlspecialchars($h['gambar']) ?>"
                         alt="<?= htmlspecialchars($h['nama']) ?>"
                         class="img-hewan-tabel" 
                  <?php else: ?>
                    <span class="text-muted text-sm">—</span> 
                  <?php endif; ?>
                </td>
                <td class="font-weight-bold"><?= htmlspecialchars($h['nama']) ?></td>
                <td>
                  <span class="badge badge-info">
                    <?= htmlspecialchars($h['nama_kategori']) ?>
                  </span>
                </td>
                <td class="text-muted text-truncate">
                  <?= htmlspecialchars($h['info']) ?>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-sm btn-edit" onclick="editHewan(<?= htmlspecialchars(json_encode($h)) ?>)">Edit</button>
                    <form action="/zoopedia/controllers/HewanController.php" method="POST" onsubmit="return confirm('Hapus hewan ini?')">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $h['id'] ?>">
                      <button type="submit" class="btn-sm btn-delete">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>


  <div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
      <h3>Tambah Hewan</h3>
      <form action="/zoopedia/controllers/HewanController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori as $kat): ?>
              <option value="<?= $kat['id'] ?>"><?= $kat['nama'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Hewan</label>
          <input type="text" name="nama" placeholder="Contoh: Singa" required />
        </div>
        <div class="form-group">
          <label>Info / Deskripsi</label>
          <textarea name="info" placeholder="Fakta unik tentang hewan ini..." required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Hewan</label>
          <input type="file" name="gambar" accept="image/*" required />
        </div>
        <div class="modal-btns">
          <button type="button" onclick="document.getElementById('modalTambah').classList.remove('show')" class="btn btn-outline">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>


  <div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
      <h3>Edit Hewan</h3>
      <form action="/zoopedia/controllers/HewanController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="gambar_lama" id="edit-gambar-lama">
        <div class="form-group">
          <label>Kategori</label>
          <select name="kategori_id" id="edit-kategori-id" required>
            <?php foreach ($kategori as $kat): ?>
              <option value="<?= $kat['id'] ?>"><?= $kat['nama'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Hewan</label>
          <input type="text" name="nama" id="edit-nama" required />
        </div>
        <div class="form-group">
          <label>Info / Deskripsi</label>
          <textarea name="info" id="edit-info" required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Hewan <span class="text-muted text-xs">(kosongkan jika tidak ingin mengubah)</span></label>
          <div id="edit-preview-wrapper" class="edit-preview-wrapper"> 
            <img id="edit-gambar-preview"
                 src=""
                 alt="Gambar saat ini"
                 class="img-hewan-tabel" 
          </div>
          <input type="file" name="gambar" accept="image/*" />
        </div>
        <div class="modal-btns">
          <button type="button" onclick="document.getElementById('modalEdit').classList.remove('show')" class="btn btn-outline">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function editHewan(h) {
      document.getElementById('edit-id').value = h.id;
      document.getElementById('edit-nama').value = h.nama;
      document.getElementById('edit-info').value = h.info;
      document.getElementById('edit-gambar-lama').value = h.gambar;
      document.getElementById('edit-kategori-id').value = h.kategori_id;

      const previewWrapper = document.getElementById('edit-preview-wrapper');
      const previewImg     = document.getElementById('edit-gambar-preview');
      if (h.gambar) {
        previewImg.src = '/zoopedia/public/images/hewan/' + h.gambar;
        previewWrapper.style.display = 'block';
      } else {
        previewWrapper.style.display = 'none';
      }

      document.getElementById('modalEdit').classList.add('show');
    }

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
      });
    });
  </script>

</body>
</html>
