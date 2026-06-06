<?php
require_once __DIR__ . '/../config/koneksi.php';

class Hewan {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findAll() {
        $query = mysqli_query($this->conn, "SELECT h.*, k.nama AS nama_kategori 
                                            FROM hewan h 
                                            JOIN kategori k ON h.kategori_id = k.id 
                                            ORDER BY k.nama, h.nama ASC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function search($keyword) {
    $keyword = mysqli_real_escape_string($this->conn, $keyword);

    $query = mysqli_query($this->conn, "SELECT h.*, k.nama AS nama_kategori
                                        FROM hewan h
                                        LEFT JOIN kategori k ON h.kategori_id = k.id
                                        WHERE h.nama LIKE '%$keyword%'
                                        ORDER BY h.nama ASC");

    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

    public function findByKategori($kategoriId) {
        $kategoriId = intval($kategoriId);
        $query = mysqli_query($this->conn, "SELECT * FROM hewan WHERE kategori_id = $kategoriId ORDER BY nama ASC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findById($id) {
        $id = intval($id);
        $query = mysqli_query($this->conn, "SELECT h.*, k.nama AS nama_kategori 
                                            FROM hewan h 
                                            JOIN kategori k ON h.kategori_id = k.id 
                                            WHERE h.id = $id");
        return mysqli_fetch_assoc($query);
    }

    public function create($kategoriId, $nama, $info, $gambar) {
        $kategoriId = intval($kategoriId);
        $nama       = mysqli_real_escape_string($this->conn, $nama);
        $info       = mysqli_real_escape_string($this->conn, $info);
        $gambar     = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "INSERT INTO hewan (kategori_id, nama, info, gambar) VALUES ($kategoriId, '$nama', '$info', '$gambar')");
    }

    public function update($id, $kategoriId, $nama, $info, $gambar) {
        $id         = intval($id);
        $kategoriId = intval($kategoriId);
        $nama       = mysqli_real_escape_string($this->conn, $nama);
        $info       = mysqli_real_escape_string($this->conn, $info);
        $gambar     = mysqli_real_escape_string($this->conn, $gambar);
        return mysqli_query($this->conn, "UPDATE hewan SET kategori_id=$kategoriId, nama='$nama', info='$info', gambar='$gambar' WHERE id=$id");
    }

    public function delete($id) {
        $id = intval($id);
        return mysqli_query($this->conn, "DELETE FROM hewan WHERE id=$id");
    }
}
?>
