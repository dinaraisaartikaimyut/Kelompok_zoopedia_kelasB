<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/User.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /zoopedia/views/user/login.php');
    exit;
}

$userModel = new User($conn);
$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $userModel->delete($_POST['id']);
    $_SESSION['success'] = 'User berhasil dihapus!';
}

header('Location: /zoopedia/views/admin/users.php');
exit;
?>