<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Soal.php';
 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$soalModel = new Soal($conn);
$action = $_POST['action'] ?? '';
 
if ($action === 'create') {
    $pertanyaan = trim($_POST['pertanyaan']);
    $jawaban    = $_POST['jawaban'];
    $penjelasan = trim($_POST['penjelasan']);
    $gambar     = '';
 
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = strtolower(str_replace(' ', '-', $_FILES['gambar']['name']));
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/soal/' . $gambar);
    }
 
    $soalModel->create($pertanyaan, $jawaban, $penjelasan, $gambar);
    $_SESSION['success'] = 'Soal berhasil ditambahkan!';
}
 
if ($action === 'update') {
    $id         = $_POST['id'];
    $pertanyaan = trim($_POST['pertanyaan']);
    $jawaban    = $_POST['jawaban'];
    $penjelasan = trim($_POST['penjelasan']);
    $gambar     = $_POST['gambar_lama'];
 
    if (!empty($_FILES['gambar']['name'])) {
        // Hapus file lama jika ada dan nama filenya berbeda
        $gambarBaru = strtolower(str_replace(' ', '-', $_FILES['gambar']['name']));
        if (!empty($_POST['gambar_lama']) && $_POST['gambar_lama'] !== $gambarBaru) {
            $fileLama = __DIR__ . '/../public/images/soal/' . $_POST['gambar_lama'];
            if (file_exists($fileLama)) {
                unlink($fileLama);
            }
        }
        $gambar = $gambarBaru;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/soal/' . $gambar);
    }
 
    $soalModel->update($id, $pertanyaan, $jawaban, $penjelasan, $gambar);
    $_SESSION['success'] = 'Soal berhasil diupdate!';
}
 
if ($action === 'delete') {
    $soalModel->delete($_POST['id']);
    $_SESSION['success'] = 'Soal berhasil dihapus!';
}
 
header('Location: /zoopedia/views/admin/soal.php');
exit;
?>
 