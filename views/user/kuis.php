<?php
session_start();
 
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../models/Soal.php';
 
if (!isset($_SESSION['user'])) {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$title = 'Kuis - Zoopedia';
 
$soalModel = new Soal($conn);
$soal = $soalModel->findRandom(10);
?>
 
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body>
 
<?php include __DIR__ . '/../partials/navbar.php'; ?>
 
<div class="kuis-wrapper">
 
  <div class="kuis-topbar" id="kuis-topbar">
    <h2>Kuis Mitos atau Fakta</h2>
    <span class="soal-counter" id="soal-num">
      1 / <?= count($soal) ?>
    </span>
  </div>
 
  <div class="soal-box" id="soal-box">
 
    <div class="soal-img">
      <img
        id="gambar-soal"
        src=""
        alt=""
        onerror="this.style.display='none'"
      />
    </div>
 
    <div class="soal-content">
 
      <span class="soal-label">
        Mitos atau Fakta?
      </span>
 
      <h3 id="soal-pertanyaan">
        Memuat soal...
      </h3>
 
      <div class="jawaban-row">
 
        <button
          class="btn-jawab btn-mitos"
          id="btn-mitos"
          onclick="jawab('mitos')"
        >
          ✕ MITOS
        </button>
 
        <button
          class="btn-jawab btn-fakta"
          id="btn-fakta"
          onclick="jawab('fakta')"
        >
          ✓ FAKTA
        </button>
 
      </div>
    </div>
  </div>
 
  <div class="hasil-box" id="hasil-box" style="display:none;">
 
    <div class="hasil-top">
 
      <div class="skor-ring">
        <span class="skor-big" id="skor-angka">
          0/0
        </span>
      </div>
 
      <div class="hasil-info">
 
        <h3 id="hasil-pesan">
          Bagus!
        </h3>
 
        <p id="hasil-sub">
          Kamu sudah menyelesaikan kuis.
        </p>
 
        <div class="hasil-stat-row">
 
          <div class="hasil-stat benar-stat">
            <span class="stat-num" id="stat-benar">0</span>
            <span class="stat-label">✓ Benar</span>
          </div>
 
          <div class="hasil-stat salah-stat">
            <span class="stat-num" id="stat-salah">0</span>
            <span class="stat-label">✕ Salah</span>
          </div>
 
        </div>
      </div>
    </div>
 
    <div class="rekap-list" id="rekap-list"></div>
 
    <div class="hasil-btns">
 
      <button
        class="btn btn-primary"
        onclick="ulangiKuis()"
      >
        ↺ Ulangi Kuis
      </button>
 
      <a
        href="/zoopedia/views/user/beranda.php"
        class="btn btn-outline"
      >
        ← Ke Beranda
      </a>
 
    </div>
  </div>
</div>
 
<div class="snackbar" id="snackbar">
 
  <div class="sb-top">
    <span id="sb-icon">✅</span>
    <span id="sb-status">FAKTA!</span>
  </div>
 
  <p class="sb-penjelasan" id="sb-penjelasan"></p>
 
  <div class="sb-next" id="btn-lanjut" onclick="lanjut()">
    Soal berikutnya →
  </div>
 
</div>
 
<script>
 
const soalKuis = <?= json_encode($soal) ?>;
 
let currentSoal = 0;
let skor = 0;
let sudahJawab = false;
let riwayat = [];
 
function simpanHasil() {
  fetch('/zoopedia/controllers/KuisController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      skor: skor,
      total_soal: soalKuis.length,
      jawaban: riwayat
    })
  }).then(res => res.json())
    .then(data => console.log('Hasil tersimpan:', data))
    .catch(err => console.log('Error:', err));
}
 
function tampilSoal() {
 
  const s = soalKuis[currentSoal];
  const gambar = document.getElementById('gambar-soal');
 
  if (s.gambar) {
    gambar.src = '/zoopedia/public/images/soal/' + s.gambar;
    gambar.style.display = 'block';
  } else {
    gambar.style.display = 'none';
  }
 
  gambar.alt = s.pertanyaan;
 
  document.getElementById('soal-pertanyaan').textContent = s.pertanyaan;
  document.getElementById('soal-num').textContent = (currentSoal + 1) + ' / ' + soalKuis.length;
  document.getElementById('btn-mitos').disabled = false;
  document.getElementById('btn-fakta').disabled = false;
 
  // Reset teks tombol lanjut
  document.getElementById('btn-lanjut').textContent = 'Soal berikutnya →';
 
  sudahJawab = false;
}
 
function jawab(pilihan) {
 
  if (sudahJawab) return;
  sudahJawab = true;
 
  document.getElementById('btn-mitos').disabled = true;
  document.getElementById('btn-fakta').disabled = true;
 
  const s = soalKuis[currentSoal];
  const benar = pilihan === s.jawaban;
 
  if (benar) skor++;
 
  riwayat.push({
    soal_id: s.id,
    pertanyaan: s.pertanyaan,
    jawaban_user: pilihan,
    jawaban_benar: s.jawaban,
    penjelasan: s.penjelasan,
    benar: benar
  });
 
  const snackbar = document.getElementById('snackbar');
  snackbar.className = 'snackbar ' + (benar ? 'benar' : 'salah');
 
  document.getElementById('sb-icon').textContent = benar ? '✅' : '❌';
  document.getElementById('sb-status').textContent = benar
    ? 'Benar! Itu ' + s.jawaban.toUpperCase()
    : 'Salah! Jawaban benar: ' + s.jawaban.toUpperCase();
  document.getElementById('sb-penjelasan').textContent = s.penjelasan;
 
  // Ganti teks tombol jika soal terakhir
  if (currentSoal === soalKuis.length - 1) {
    document.getElementById('btn-lanjut').textContent = 'Lihat Hasil →';
  }
 
  snackbar.classList.add('show');
}
 
function lanjut() {
  document.getElementById('snackbar').classList.remove('show');
  setTimeout(() => {
    currentSoal++;
    if (currentSoal < soalKuis.length) {
      tampilSoal();
    } else {
      tampilHasil();
    }
  }, 300);
}
 
function tampilHasil() {
 
  document.getElementById('kuis-topbar').style.display = 'none';
  document.getElementById('soal-box').style.display = 'none';
 
  const hasilBox = document.getElementById('hasil-box');
  hasilBox.style.display = 'block';
 
  const salah = soalKuis.length - skor;
 
  document.getElementById('skor-angka').textContent = skor + '/' + soalKuis.length;
  document.getElementById('stat-benar').textContent = skor;
  document.getElementById('stat-salah').textContent = salah;
 
  let pesan = '';
  let sub = '';
 
  if (skor === soalKuis.length) {
    pesan = '🎉 Sempurna!';
    sub = 'Semua jawaban benar!';
  } else if (skor >= soalKuis.length / 2) {
    pesan = '👍 Bagus!';
    sub = 'Kamu cukup paham dunia hewan!';
  } else {
    pesan = '📖 Tetap Semangat!';
    sub = 'Yuk belajar lagi tentang hewan.';
  }
 
  document.getElementById('hasil-pesan').textContent = pesan;
  document.getElementById('hasil-sub').textContent = sub;
 
  simpanHasil();
 
  const rekapList = document.getElementById('rekap-list');
  rekapList.innerHTML = '';
 
  riwayat.forEach((r, i) => {
    const div = document.createElement('div');
    div.className = 'rekap-item ' + (r.benar ? 'rekap-benar' : 'rekap-salah');
    div.innerHTML = `
      <div class="rekap-nomor">${i + 1}</div>
      <div class="rekap-isi">
        <div class="rekap-badge">
          ${r.benar ? '✓ Benar' : '✕ Salah'}
        </div>
        <div class="rekap-soal">${r.pertanyaan}</div>
        <div class="rekap-keterangan">
          Jawabanmu: <strong>${r.jawaban_user.toUpperCase()}</strong>
          · Jawaban benar: <strong>${r.jawaban_benar.toUpperCase()}</strong>
        </div>
        <div class="rekap-penjelasan">${r.penjelasan}</div>
      </div>
    `;
    rekapList.appendChild(div);
  });
}
 
function ulangiKuis() {
  currentSoal = 0;
  skor = 0;
  sudahJawab = false;
  riwayat = [];
 
  document.getElementById('kuis-topbar').style.display = 'flex';
  document.getElementById('soal-box').style.display = 'block';
  document.getElementById('hasil-box').style.display = 'none';
 
  tampilSoal();
}
 
tampilSoal();
 
</script>
 
</body>
</html>