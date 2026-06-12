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

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$keywordSafe = mysqli_real_escape_string($conn, $keyword);

if ($keyword !== '') {
    $qHewan = mysqli_query($conn, "SELECT * FROM hewan 
                                   WHERE kategori_id = $id 
                                   AND nama LIKE '%$keywordSafe%' 
                                   ORDER BY nama ASC");
} else {
    $qHewan = mysqli_query($conn, "SELECT * FROM hewan 
                                   WHERE kategori_id = $id 
                                   ORDER BY nama ASC");
}

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
      <h2 class="kategori-title"><?= htmlspecialchars($kategori['nama']) ?></h2>
      <p class="kategori-desc"><?= htmlspecialchars($kategori['deskripsi']) ?></p>
    </div>

    <p class="section-title">Hewan dalam Kategori Ini</p>

    <form method="GET" action="" class="search-form">
      <input type="hidden" name="id" value="<?= $id ?>">

      <input
        type="text"
        name="search"
        placeholder="Cari hewan..."
        value="<?= htmlspecialchars($keyword) ?>"
        class="search-input"
      >

      <button type="submit" class="btn btn-primary">
        Cari
      </button>

      <?php if ($keyword !== ''): ?>
        <a href="detail_kategori.php?id=<?= $id ?>" class="btn btn-primary">
          Reset
        </a>
      <?php endif; ?>
    </form>

    <?php if (empty($hewan)): ?>
      <div class="table-empty">
        <?= $keyword !== '' ? 'Hewan tidak ditemukan' : 'Belum ada hewan' ?>
      </div>
    <?php else: ?>
      <div class="hewan-grid">
        <?php foreach ($hewan as $h): ?>
          <div class="hewan-card">
            <div class="hewan-top">
              <img src="/zoopedia/public/images/hewan/<?= htmlspecialchars($h['gambar']) ?>"
                   alt="<?= htmlspecialchars($h['nama']) ?>"
                   class="img-hewan"
                   onerror="this.style.display='none'" />
            </div>
            <div class="hewan-body">
              <h4><?= htmlspecialchars($h['nama']) ?></h4>
              <p><?= htmlspecialchars($h['info']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
