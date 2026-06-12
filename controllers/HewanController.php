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

    if (empty($kategoriId) || empty($nama) || empty($info) || empty($_FILES['gambar']['name'])) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/hewan.php');
        exit;
    }

    $gambar = '';

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

    if (empty($kategoriId) || empty($nama) || empty($info)) {
        $_SESSION['error'] = 'Semua field wajib diisi!';
        header('Location: /zoopedia/views/admin/hewan.php');
        exit;
    }

    $gambar = $_POST['gambar_lama'];

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
    $hewan = $hewanModel->findById($_POST['id']);

    if (!empty($hewan['gambar'])) {
        $filePath = __DIR__ . '/../public/images/hewan/' . $hewan['gambar'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $hewanModel->delete($_POST['id']);
    $_SESSION['success'] = 'Hewan berhasil dihapus!';
}

header('Location: /zoopedia/views/admin/hewan.php');
exit;
?>
