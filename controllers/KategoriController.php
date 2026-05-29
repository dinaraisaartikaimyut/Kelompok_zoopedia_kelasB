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
require_once __DIR__ . '/../models/Kategori.php';
 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}
 
$kategoriModel = new Kategori($conn);
$action = $_POST['action'] ?? '';
 
if ($action === 'create') {
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
 
    if (empty($nama) || empty($deskripsi) || empty($_FILES['gambar']['name'])) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/kategori.php');
        exit;
    }
 
    $gambar = '';
 
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/kategori/' . $gambar);
    }
 
    $kategoriModel->create($nama, $deskripsi, $gambar);
    $_SESSION['success'] = 'Kategori berhasil ditambahkan!';
}
 
if ($action === 'update') {
    $id        = $_POST['id'];
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
 
    if (empty($nama) || empty($deskripsi)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/kategori.php');
        exit;
    }
 
    $gambar = $_POST['gambar_lama'];
 
    if (!empty($_FILES['gambar']['name'])) {
        $ext        = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambarBaru = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext; 
 
        if (!empty($_POST['gambar_lama']) && $_POST['gambar_lama'] !== $gambarBaru) {
            $fileLama = __DIR__ . '/../public/images/kategori/' . $_POST['gambar_lama'];
            if (file_exists($fileLama)) {
                unlink($fileLama);
            }
        }
 
        $gambar = $gambarBaru;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/kategori/' . $gambar);
    }
 
    $kategoriModel->update($id, $nama, $deskripsi, $gambar);
    $_SESSION['success'] = 'Kategori berhasil diupdate!';
}
 
if ($action === 'delete') {
    $kategori = $kategoriModel->findById($_POST['id']); 
 
    if (!empty($kategori['gambar'])) {
        $filePath = __DIR__ . '/../public/images/kategori/' . $kategori['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }
 
    $kategoriModel->delete($_POST['id']);
    $_SESSION['success'] = 'Kategori berhasil dihapus!';
}
 
header('Location: /zoopedia/views/admin/kategori.php');
exit;
?>
