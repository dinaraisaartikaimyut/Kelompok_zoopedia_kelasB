<?php
require_once __DIR__ . '/../config/koneksi.php';

class Kuis {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findAll() {
        $query = mysqli_query($this->conn, "SELECT h.*, u.nama AS nama_user, u.username 
                                            FROM hasil_kuis h 
                                            JOIN users u ON h.user_id = u.id 
                                            ORDER BY h.created_at DESC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findById($id) {
        $id = intval($id);
        $query = mysqli_query($this->conn, "SELECT h.*, u.nama AS nama_user, u.username 
                                            FROM hasil_kuis h 
                                            JOIN users u ON h.user_id = u.id 
                                            WHERE h.id = $id");
        return mysqli_fetch_assoc($query);
    }

    public function findByUser($userId) {
        $userId = intval($userId);
        $query = mysqli_query($this->conn, "SELECT * FROM hasil_kuis WHERE user_id = $userId ORDER BY created_at DESC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findDetail($hasilId) {
        $hasilId = intval($hasilId);
        $query = mysqli_query($this->conn, "SELECT d.*, s.pertanyaan, s.jawaban AS jawaban_benar, s.penjelasan, s.gambar
                                            FROM detail_hasil d
                                            JOIN soal s ON d.soal_id = s.id
                                            WHERE d.hasil_id = $hasilId");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function simpanHasil($userId, $skor, $totalSoal) {
        $userId    = intval($userId);
        $skor      = intval($skor);
        $totalSoal = intval($totalSoal);
        mysqli_query($this->conn, "INSERT INTO hasil_kuis (user_id, skor, total_soal) VALUES ($userId, $skor, $totalSoal)");
        return mysqli_insert_id($this->conn);
    }

    public function simpanDetail($hasilId, $soalId, $jawabanUser, $adalahBenar) {
        $hasilId     = intval($hasilId);
        $soalId      = intval($soalId);
        $jawabanUser = mysqli_real_escape_string($this->conn, $jawabanUser);
        $adalahBenar = $adalahBenar ? 1 : 0;
        return mysqli_query($this->conn, "INSERT INTO detail_hasil (hasil_id, soal_id, jawaban_user, adalah_benar) 
                                          VALUES ($hasilId, $soalId, '$jawabanUser', $adalahBenar)");
    }
}
?>