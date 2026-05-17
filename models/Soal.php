<?php
require_once __DIR__ . '/../config/koneksi.php';

class Soal {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findAll() {
        $query = mysqli_query($this->conn, "SELECT * FROM soal ORDER BY created_at DESC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findRandom($limit = 10) {
        $limit = intval($limit);
        $query = mysqli_query($this->conn, "SELECT * FROM soal ORDER BY RAND() LIMIT $limit");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findById($id) {
        $id = intval($id);
        $query = mysqli_query($this->conn, "SELECT * FROM soal WHERE id = $id");
        return mysqli_fetch_assoc($query);
    }

    public function create($pertanyaan, $jawaban, $penjelasan, $gambar) {
        $pertanyaan = mysqli_real_escape_string($this->conn, $pertanyaan);
        $jawaban    = mysqli_real_escape_string($this->conn, $jawaban);
        $penjelasan = mysqli_real_escape_string($this->conn, $penjelasan);
        $gambar     = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "INSERT INTO soal (pertanyaan, jawaban, penjelasan, gambar) VALUES ('$pertanyaan', '$jawaban', '$penjelasan', '$gambar')");
    }

    public function update($id, $pertanyaan, $jawaban, $penjelasan, $gambar) {
        $id         = intval($id);
        $pertanyaan = mysqli_real_escape_string($this->conn, $pertanyaan);
        $jawaban    = mysqli_real_escape_string($this->conn, $jawaban);
        $penjelasan = mysqli_real_escape_string($this->conn, $penjelasan);
        $gambar     = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "UPDATE soal SET pertanyaan='$pertanyaan', jawaban='$jawaban', penjelasan='$penjelasan', gambar='$gambar' WHERE id=$id");
    }

    public function delete($id) {
        $id = intval($id);
        return mysqli_query($this->conn, "DELETE FROM soal WHERE id=$id");
    }
}
?>