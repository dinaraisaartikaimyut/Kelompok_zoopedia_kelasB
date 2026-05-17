<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/User.php';

$userModel = new User($conn);
$action = $_GET['action'] ?? '';

if ($action === 'logout') {
    session_destroy();
    header('Location: /kode_zoopedia/views/user/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password tidak boleh kosong';
        header('Location: /kode_zoopedia/views/user/login.php');
        exit;
    }

    $user = $userModel->findByUsername($username);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Username atau password salah';
        header('Location: /kode_zoopedia/views/user/login.php');
        exit;
    }

    $_SESSION['user'] = [
        'id'       => $user['id'],
        'nama'     => $user['nama'],
        'username' => $user['username'],
        'role'     => $user['role']
    ];

    if ($user['role'] === 'admin') {
        header('Location: /kode_zoopedia/views/admin/dashboard.php');
    } else {
        header('Location: /kode_zoopedia/views/user/beranda.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $nama       = trim($_POST['nama'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $konfirmasi = trim($_POST['konfirmasi'] ?? '');

    if (empty($nama) || empty($username) || empty($password) || empty($konfirmasi)) {
        $_SESSION['error'] = 'Semua kolom harus diisi';
        $_SESSION['error_field'] = '';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if (strlen($nama) < 3) {
        $_SESSION['error'] = 'Nama lengkap minimal 3 karakter';
        $_SESSION['error_field'] = 'nama';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if (preg_match('/[0-9]/', $nama)) {
        $_SESSION['error'] = 'Nama lengkap tidak boleh mengandung angka';
        $_SESSION['error_field'] = 'nama';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if (strlen($username) < 3) {
        $_SESSION['error'] = 'Username minimal 3 karakter';
        $_SESSION['error_field'] = 'username';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $_SESSION['error'] = 'Username hanya boleh huruf dan angka';
        $_SESSION['error_field'] = 'username';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if (!preg_match('/^[0-9]{6}$/', $password)) {
        $_SESSION['error'] = 'Password harus tepat 6 digit angka';
        $_SESSION['error_field'] = 'password';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    if ($password !== $konfirmasi) {
        $_SESSION['error'] = 'Password dan konfirmasi tidak cocok';
        $_SESSION['error_field'] = 'konfirmasi';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    $existing = $userModel->findByUsername($username);
    if ($existing) {
        $_SESSION['error'] = 'Username sudah digunakan';
        $_SESSION['error_field'] = 'username';
        header('Location: /kode_zoopedia/views/user/register.php');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $userModel->create($nama, $username, $hashed);

    $_SESSION['success'] = 'Akun berhasil dibuat! Silakan login.';
    header('Location: /kode_zoopedia/views/user/login.php');
    exit;
}
?>