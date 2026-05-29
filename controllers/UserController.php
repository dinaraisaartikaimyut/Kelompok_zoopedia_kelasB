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
