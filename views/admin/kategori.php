<?php
session_start();
$timeout = 300;

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
require_once __DIR__ . '/../../models/Kategori.php';
require_once __DIR__ . '/../../models/Hewan.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$title = 'Kelola Kategori - Zoopedia';

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);

$kategoriModel = new Kategori($conn);

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($keyword !== '') {
    $kategori = $kategoriModel->search($keyword);
} else {
    $kategori = $kategoriModel->findAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
  <?php include __DIR__ . '/../partials/navbar_admin.php'; ?>

  <div class="section">
    <div class="admin-header">
      <div>
        <h2 class="admin-title">Kelola Kategori</h2>
        <p class="admin-subtitle">Total <?= count($kategori) ?> kategori</p>
      </div>
      <button
        onclick="document.getElementById('modalTambah').classList.add('show')"
        class="btn btn-primary"
      >
        + Tambah Kategori
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

    <form method="GET" action="" class="search-form">
  <input
    type="text"
    name="search"
    placeholder="Cari kategori..."
    value="<?= htmlspecialchars($keyword) ?>"
    class="search-input"
  >

  <button type="submit" class="btn btn-primary">
    Cari
  </button>

  <?php if ($keyword !== ''): ?>
    <a href="kategori.php" class="btn btn-outline">
      Reset
    </a>
  <?php endif; ?>
</form>


    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Jumlah Hewan</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($kategori)): ?>
            <tr>
              <td colspan="6" class="table-empty">
                <?= $keyword !== '' ? 'Kategori tidak ditemukan' : 'Belum ada kategori' ?>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($kategori as $i => $kat): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <?php if (!empty($kat['gambar'])): ?>
                    <img src="/zoopedia/public/images/kategori/<?= htmlspecialchars($kat['gambar']) ?>"
                         alt="<?= htmlspecialchars($kat['nama']) ?>"
                         class="img-hewan-tabel">
                  <?php else: ?>
                    <span class="text-muted text-sm">—</span>
                  <?php endif; ?>
                </td>
                <td class="font-weight-bold">
                  <?= htmlspecialchars($kat['nama']) ?>
                </td>
                <td class="text-muted text-truncate">
                  <?= htmlspecialchars($kat['deskripsi']) ?>
                </td>
                <td>
                  <span class="badge badge-success">
                    <?= $kat['jumlah_hewan'] ?> hewan
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      class="btn-sm btn-edit"
                      onclick='editKategori(<?= json_encode($kat) ?>)'
                    >
                      Edit
                    </button>

                    <form
                      action="/zoopedia/controllers/KategoriController.php"
                      method="POST"
                      onsubmit="return confirm('Hapus kategori ini?')"
                    >
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $kat['id'] ?>">
                      <button type="submit" class="btn-sm btn-delete">
                        Hapus
                      </button>
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
      <h3>Tambah Kategori</h3>
      <form action="/zoopedia/controllers/KategoriController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
          <label>Nama Kategori</label>
          <input type="text" name="nama" placeholder="Contoh: Mamalia" required />
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" placeholder="Deskripsi kategori..." required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Kategori</label>
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
      <h3>Edit Kategori</h3>
      <form action="/zoopedia/controllers/KategoriController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="gambar_lama" id="edit-gambar-lama">
        <div class="form-group">
          <label>Nama Kategori</label>
          <input type="text" name="nama" id="edit-nama" required />
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" id="edit-deskripsi" required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Kategori <span class="text-muted text-xs">(kosongkan jika tidak ingin mengubah)</span></label>
          <div id="edit-preview-wrapper" class="edit-preview-wrapper">
            <img id="edit-gambar-preview"
                 src=""
                 alt="Gambar saat ini"
                 class="img-hewan-tabel">
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
    function editKategori(kat) {
      document.getElementById('edit-id').value = kat.id;
      document.getElementById('edit-nama').value = kat.nama;
      document.getElementById('edit-deskripsi').value = kat.deskripsi;
      document.getElementById('edit-gambar-lama').value = kat.gambar;

      const previewWrapper = document.getElementById('edit-preview-wrapper');
      const previewImg     = document.getElementById('edit-gambar-preview');
      if (kat.gambar) {
        previewImg.src = '/zoopedia/public/images/kategori/' + kat.gambar;
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
