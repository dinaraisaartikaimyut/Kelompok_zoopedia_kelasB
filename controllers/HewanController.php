<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Hewan.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$hewanModel = new Hewan($conn);
$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $kategoriId = $_POST['kategori_id'];
    $nama       = trim($_POST['nama']);
    $info       = trim($_POST['info']);
    $gambar     = '';

    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/hewan/' . $gambar);
    }

    $hewanModel->create($kategoriId, $nama, $info, $gambar);
    $_SESSION['success'] = 'Hewan berhasil ditambahkan!';
}

if ($action === 'update') {
    $id         = $_POST['id'];
    $kategoriId = $_POST['kategori_id'];
    $nama       = trim($_POST['nama']);
    $info       = trim($_POST['info']);
    $gambar     = $_POST['gambar_lama'];

    if (!empty($_FILES['gambar']['name'])) {
        $ext        = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambarBaru = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;

        if (!empty($_POST['gambar_lama']) && $_POST['gambar_lama'] !== $gambarBaru) {
            $fileLama = __DIR__ . '/../public/images/hewan/' . $_POST['gambar_lama'];
            if (file_exists($fileLama)) {
                unlink($fileLama);
            }
        }

        $gambar = $gambarBaru;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/hewan/' . $gambar);
    }

    $hewanModel->update($id, $kategoriId, $nama, $info, $gambar);
    $_SESSION['success'] = 'Hewan berhasil diupdate!';
}

if ($action === 'delete') {
    $hewan = $hewanModel->findById($_POST['id']); // ← ambil data dulu

    if (!empty($hewan['gambar'])) {
        $filePath = __DIR__ . '/../public/images/hewan/' . $hewan['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath); // ← hapus file gambar dari folder
        }
    }

    $hewanModel->delete($_POST['id']);
    $_SESSION['success'] = 'Hewan berhasil dihapus!';
}

header('Location: /zoopedia/views/admin/hewan.php');
exit;
?>