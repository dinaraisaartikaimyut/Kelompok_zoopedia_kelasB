<?php
require_once __DIR__ . '/../config/koneksi.php';

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findAll() {
        $query = mysqli_query($this->conn, "SELECT * FROM users ORDER BY created_at DESC");
        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function findByUsername($username) {
        $username = mysqli_real_escape_string($this->conn, $username);
        $query = mysqli_query($this->conn, "SELECT * FROM users WHERE username = '$username'");
        return mysqli_fetch_assoc($query);
    }

    public function create($nama, $username, $password) {
        $nama     = mysqli_real_escape_string($this->conn, $nama);
        $username = mysqli_real_escape_string($this->conn, $username);
        $password = mysqli_real_escape_string($this->conn, $password);
        return mysqli_query($this->conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', 'user')");
    }

    public function delete($id) {
        $id = intval($id);
        return mysqli_query($this->conn, "DELETE FROM users WHERE id = $id AND role != 'admin'");
    }
}
?>