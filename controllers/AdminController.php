<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Kategori.php';
require_once __DIR__ . '/../models/Hewan.php';
require_once __DIR__ . '/../models/Soal.php';
require_once __DIR__ . '/../models/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$action   = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? '/zoopedia/views/admin/dashboard.php';

if ($action === 'kategori_create') {
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar    = '';
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/kategori/' . $gambar);
    }
    Kategori::create($conn, $nama, $deskripsi, $gambar);
    $_SESSION['success'] = 'Kategori berhasil ditambahkan!';
    header('Location: /zoopedia/views/admin/kategori.php');
    exit;
}

if ($action === 'kategori_update') {
    $id        = intval($_POST['id']);
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar    = $_POST['gambar_lama'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/kategori/' . $gambar);
    }
    Kategori::update($conn, $id, $nama, $deskripsi, $gambar);
    $_SESSION['success'] = 'Kategori berhasil diupdate!';
    header('Location: /zoopedia/views/admin/kategori.php');
    exit;
}

if ($action === 'kategori_delete') {
    $id = intval($_POST['id']);
    Kategori::delete($conn, $id);
    $_SESSION['success'] = 'Kategori berhasil dihapus!';
    header('Location: /zoopedia/views/admin/kategori.php');
    exit;
}

if ($action === 'hewan_create') {
    $kategoriId = intval($_POST['kategori_id']);
    $nama       = trim($_POST['nama']);
    $info       = trim($_POST['info']);
    $gambar     = '';
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/hewan/' . $gambar);
    }
    Hewan::create($conn, $kategoriId, $nama, $info, $gambar);
    $_SESSION['success'] = 'Hewan berhasil ditambahkan!';
    header('Location: /zoopedia/views/admin/hewan.php');
    exit;
}

if ($action === 'hewan_update') {
    $id         = intval($_POST['id']);
    $kategoriId = intval($_POST['kategori_id']);
    $nama       = trim($_POST['nama']);
    $info       = trim($_POST['info']);
    $gambar     = $_POST['gambar_lama'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = strtolower(str_replace(' ', '-', $nama)) . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/hewan/' . $gambar);
    }
    Hewan::update($conn, $id, $kategoriId, $nama, $info, $gambar);
    $_SESSION['success'] = 'Hewan berhasil diupdate!';
    header('Location: /zoopedia/views/admin/hewan.php');
    exit;
}

if ($action === 'hewan_delete') {
    $id = intval($_POST['id']);
    Hewan::delete($conn, $id);
    $_SESSION['success'] = 'Hewan berhasil dihapus!';
    header('Location: /zoopedia/views/admin/hewan.php');
    exit;
}

if ($action === 'soal_create') {
    $pertanyaan = trim($_POST['pertanyaan']);
    $jawaban    = $_POST['jawaban'];
    $penjelasan = trim($_POST['penjelasan']);
    $gambar     = '';
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'soal-' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/soal/' . $gambar);
    }
    Soal::create($conn, $pertanyaan, $jawaban, $penjelasan, $gambar);
    $_SESSION['success'] = 'Soal berhasil ditambahkan!';
    header('Location: /zoopedia/views/admin/soal.php');
    exit;
}

if ($action === 'soal_update') {
    $id         = intval($_POST['id']);
    $pertanyaan = trim($_POST['pertanyaan']);
    $jawaban    = $_POST['jawaban'];
    $penjelasan = trim($_POST['penjelasan']);
    $gambar     = $_POST['gambar_lama'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext    = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = 'soal-' . time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../public/images/soal/' . $gambar);
    }
    Soal::update($conn, $id, $pertanyaan, $jawaban, $penjelasan, $gambar);
    $_SESSION['success'] = 'Soal berhasil diupdate!';
    header('Location: /zoopedia/views/admin/soal.php');
    exit;
}

if ($action === 'soal_delete') {
    $id = intval($_POST['id']);
    Soal::delete($conn, $id);
    $_SESSION['success'] = 'Soal berhasil dihapus!';
    header('Location: /zoopedia/views/admin/soal.php');
    exit;
}

if ($action === 'user_delete') {
    $id = intval($_POST['id']);
    User::delete($conn, $id);
    $_SESSION['success'] = 'User berhasil dihapus!';
    header('Location: /zoopedia/views/admin/users.php');
    exit;
}
?>