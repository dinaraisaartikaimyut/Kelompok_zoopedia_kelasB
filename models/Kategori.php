<?php
require_once __DIR__ . '/../config/koneksi.php';

class Kategori {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findAll() {
        $query = mysqli_query($this->conn, "SELECT k.*, COUNT(h.id) AS jumlah_hewan 
                                            FROM kategori k 
                                            LEFT JOIN hewan h ON h.kategori_id = k.id 
                                            GROUP BY k.id 
                                            ORDER BY k.nama ASC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function search($keyword) {
    $keyword = mysqli_real_escape_string($this->conn, $keyword);

    $query = mysqli_query($this->conn, "SELECT k.*, COUNT(h.id) AS jumlah_hewan 
                                        FROM kategori k 
                                        LEFT JOIN hewan h ON h.kategori_id = k.id 
                                        WHERE k.nama LIKE '%$keyword%' 
                                        OR k.deskripsi LIKE '%$keyword%'
                                        GROUP BY k.id 
                                        ORDER BY k.nama ASC");

    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

    public function findById($id) {
        $id = intval($id);
        $query = mysqli_query($this->conn, "SELECT * FROM kategori WHERE id = $id");
        return mysqli_fetch_assoc($query);
    }

    public function create($nama, $deskripsi, $gambar) {
        $nama      = mysqli_real_escape_string($this->conn, $nama);
        $deskripsi = mysqli_real_escape_string($this->conn, $deskripsi);
        $gambar    = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "INSERT INTO kategori (nama, deskripsi, gambar) VALUES ('$nama', '$deskripsi', '$gambar')");
    }

    public function update($id, $nama, $deskripsi, $gambar) {
        $id        = intval($id);
        $nama      = mysqli_real_escape_string($this->conn, $nama);
        $deskripsi = mysqli_real_escape_string($this->conn, $deskripsi);
        $gambar    = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "UPDATE kategori SET nama='$nama', deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id");
    }

    public function delete($id) {
        $id = intval($id);
        return mysqli_query($this->conn, "DELETE FROM kategori WHERE id=$id");
    }
}
?>
