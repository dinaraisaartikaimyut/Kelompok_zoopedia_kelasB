<?php
session_start();
$timeout = 30;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
        session_unset();
        session_destroy();

        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'timeout']);
        exit;
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Kuis.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$kuisModel = new Kuis($conn);
$userId    = $_SESSION['user']['id'];
$skor      = intval($data['skor']);
$totalSoal = intval($data['total_soal']);
$jawaban   = $data['jawaban'];

$hasilId = $kuisModel->simpanHasil($userId, $skor, $totalSoal);

foreach ($jawaban as $j) {
    $kuisModel->simpanDetail($hasilId, $j['soal_id'], $j['jawaban_user'], $j['benar']);
}

echo json_encode(['success' => true, 'hasil_id' => $hasilId]);
?>
