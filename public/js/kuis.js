let currentSoal = 0;
let skor = 0;
let sudahJawab = false;
let riwayat = [];

function tampilSoal() {
    const s = soalKuis[currentSoal];
    const gambar = document.getElementById('gambar-soal');
    gambar.src = '/zoopedia/public/images/hewan/' + s.gambar;
    gambar.alt = s.pertanyaan;
    gambar.style.display = 'block';
    document.getElementById('soal-pertanyaan').textContent = s.pertanyaan;
    document.getElementById('soal-num').textContent = (currentSoal + 1) + ' / ' + soalKuis.length;
    document.getElementById('btn-mitos').disabled = false;
    document.getElementById('btn-fakta').disabled = false;
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
        ? 'Benar! Itu ' + s.jawaban.toUpperCase() + '.'
        : 'Salah! Jawaban yang benar adalah ' + s.jawaban.toUpperCase() + '.';
    document.getElementById('sb-penjelasan').textContent = s.penjelasan;
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
    document.getElementById('snackbar').classList.remove('show');

    const hasilBox = document.getElementById('hasil-box');
    hasilBox.classList.add('show');

    const salah = soalKuis.length - skor;
    document.getElementById('skor-angka').textContent = skor + '/' + soalKuis.length;
    document.getElementById('stat-benar').textContent = skor;
    document.getElementById('stat-salah').textContent = salah;

    let pesan, sub;
    if (skor === soalKuis.length) {
        pesan = '🎉 Sempurna!';
        sub = 'Luar biasa! Kamu menjawab semua soal dengan benar!';
    } else if (skor >= Math.ceil(soalKuis.length / 2)) {
        pesan = '👍 Bagus!';
        sub = 'Kamu cukup tahu tentang dunia hewan. Terus belajar ya!';
    } else {
        pesan = '📖 Terus Belajar!';
        sub = 'Masih banyak fakta seru tentang hewan yang bisa kamu pelajari!';
    }
    document.getElementById('hasil-pesan').textContent = pesan;
    document.getElementById('hasil-sub').textContent = sub;

    simpanHasil();
    tampilRekap();
}

function tampilRekap() {
    const rekapList = document.getElementById('rekap-list');
    rekapList.innerHTML = '';
    riwayat.forEach((r, i) => {
        const div = document.createElement('div');
        div.className = 'rekap-item ' + (r.benar ? 'rekap-benar' : 'rekap-salah');
        div.innerHTML = `
            <div class="rekap-nomor">${i + 1}</div>
            <div class="rekap-isi">
                <div class="rekap-badge">${r.benar ? '✓ Benar' : '✕ Salah'}</div>
                <div class="rekap-soal">${r.pertanyaan}</div>
                <div class="rekap-keterangan">
                    Jawabanmu: <strong>${r.jawaban_user.toUpperCase()}</strong> &nbsp;·&nbsp;
                    Jawaban benar: <strong>${r.jawaban_benar.toUpperCase()}</strong>
                </div>
                <div class="rekap-penjelasan">${r.penjelasan}</div>
            </div>
        `;
        rekapList.appendChild(div);
    });
}

function simpanHasil() {
    fetch('/zoopedia/controllers/KuisController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            skor: skor,
            total_soal: soalKuis.length,
            jawaban: riwayat
        })
    });
}

function ulangiKuis() {
    currentSoal = 0;
    skor = 0;
    sudahJawab = false;
    riwayat = [];
    document.getElementById('kuis-topbar').style.display = 'flex';
    document.getElementById('soal-box').style.display = 'block';
    document.getElementById('hasil-box').classList.remove('show');
    tampilSoal();
}
