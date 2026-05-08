<?php
class Produk {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function semua() {
        $result = $this->conn->query('SELECT * FROM products ORDER BY id DESC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function stokMenipis($limit = 5) {
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE stock < ? ORDER BY stock ASC');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function tambah($name, $category, $stock, $price) {
        if ($stock < 0) return false;
        $stmt = $this->conn->prepare('INSERT INTO products (name, category, stock, price) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssid', $name, $category, $stock, $price);
        return $stmt->execute();
    }

    public function cari($id) {
        $stmt = $this->conn->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStok($id, $stock) {
        $stmt = $this->conn->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $stmt->bind_param('ii', $stock, $id);
        return $stmt->execute();
    }
}
