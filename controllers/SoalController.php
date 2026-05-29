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
 
    if (empty($pertanyaan) || empty($jawaban) || empty($penjelasan) || empty($_FILES['gambar']['name'])) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/soal.php');
        exit;
    }
 
    $gambar = '';
 
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
 
    if (empty($pertanyaan) || empty($jawaban) || empty($penjelasan)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/soal.php');
        exit;
    }
 
    $gambar = $_POST['gambar_lama'];
 
    if (!empty($_FILES['gambar']['name'])) {
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
