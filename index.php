<?php
session_start();
require_once 'config/koneksi.php';
 
if (!isset($_SESSION['user'])) {
    header('Location: views/user/login.php');
    exit;
}
 
if ($_SESSION['user']['role'] === 'admin') {
    header('Location: views/admin/dashboard.php');
    exit;
} else {
    header('Location: views/user/beranda.php');
    exit;
}
?>
 