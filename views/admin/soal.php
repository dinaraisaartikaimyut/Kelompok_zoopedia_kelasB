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
require_once __DIR__ . '/../../models/Soal.php';
 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$title = 'Kelola Soal - Zoopedia';
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
 
$soalModel = new Soal($conn);
$soal = $soalModel->findAll();
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
        <h2 class="admin-title">Kelola Soal Kuis</h2>
        <p class="admin-subtitle">Total <?= count($soal) ?> soal</p>
      </div>
      <button onclick="document.getElementById('modalTambah').classList.add('show')" class="btn btn-primary">
        + Tambah Soal
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
            <th>Pertanyaan</th>
            <th>Jawaban</th>
            <th>Penjelasan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($soal)): ?>
            <tr><td colspan="6" class="table-empty">Belum ada soal</td></tr>
          <?php else: ?>
            <?php foreach ($soal as $i => $s): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <?php if (!empty($s['gambar'])): ?>
                    <img src="/zoopedia/public/images/soal/<?= htmlspecialchars($s['gambar']) ?>"
                         alt="Gambar soal"
                         style="width:48px; height:48px; object-fit:cover; border-radius:6px;" />
                  <?php else: ?>
                    <span class="text-muted" style="font-size:12px;">—</span>
                  <?php endif; ?>
                </td>
                <td class="text-truncate"><?= htmlspecialchars($s['pertanyaan']) ?></td>
                <td><span class="badge badge-<?= $s['jawaban'] ?>"><?= strtoupper($s['jawaban']) ?></span></td>
                <td class="text-muted text-truncate">
                  <?= htmlspecialchars($s['penjelasan']) ?>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn-sm btn-edit" onclick="editSoal(<?= htmlspecialchars(json_encode($s)) ?>)">Edit</button>
                    <form action="/zoopedia/controllers/SoalController.php" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
      <h3>Tambah Soal</h3>
      <form action="/zoopedia/controllers/SoalController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
          <label>Pertanyaan</label>
          <textarea name="pertanyaan" placeholder="Tulis pertanyaan mitos atau fakta..." required></textarea>
        </div>
        <div class="form-group">
          <label>Jawaban Benar</label>
          <select name="jawaban" required>
            <option value="">-- Pilih Jawaban --</option>
            <option value="fakta">FAKTA</option>
            <option value="mitos">MITOS</option>
          </select>
        </div>
        <div class="form-group">
          <label>Penjelasan</label>
          <textarea name="penjelasan" placeholder="Jelaskan kenapa jawaban tersebut benar..." required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Soal</label>
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
      <h3>Edit Soal</h3>
      <form action="/zoopedia/controllers/SoalController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="gambar_lama" id="edit-gambar-lama">
        <div class="form-group">
          <label>Pertanyaan</label>
          <textarea name="pertanyaan" id="edit-pertanyaan" required></textarea>
        </div>
        <div class="form-group">
          <label>Jawaban Benar</label>
          <select name="jawaban" id="edit-jawaban" required>
            <option value="">-- Pilih Jawaban --</option>
            <option value="fakta">FAKTA</option>
            <option value="mitos">MITOS</option>
          </select>
        </div>
        <div class="form-group">
          <label>Penjelasan</label>
          <textarea name="penjelasan" id="edit-penjelasan" required></textarea>
        </div>
        <div class="form-group">
          <label>Gambar Soal <span class="text-muted" style="font-size:11px;">(kosongkan jika tidak ingin mengubah)</span></label>
          <div id="edit-preview-wrapper" style="margin-bottom:8px; display:none;">
            <img id="edit-gambar-preview"
                 src=""
                 alt="Gambar saat ini"
                 style="width:64px; height:64px; object-fit:cover; border-radius:6px;" />
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
    function editSoal(s) {
      document.getElementById('edit-id').value = s.id;
      document.getElementById('edit-pertanyaan').value = s.pertanyaan;
      document.getElementById('edit-jawaban').value = s.jawaban;
      document.getElementById('edit-penjelasan').value = s.penjelasan;
      document.getElementById('edit-gambar-lama').value = s.gambar;

      const previewWrapper = document.getElementById('edit-preview-wrapper');
      const previewImg     = document.getElementById('edit-gambar-preview');
      if (s.gambar) {
        previewImg.src = '/zoopedia/public/images/soal/' + s.gambar;
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
