<?php
class Transaksi {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function semua() {
        $sql = 'SELECT t.id, p.name, t.qty, t.created_at FROM transactions t JOIN products p ON t.product_id = p.id ORDER BY t.id DESC';
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function kurangi($productId, $qty) {
        if ($qty <= 0) return false;
        $stmt = $this->conn->prepare('SELECT stock FROM products WHERE id = ?');
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row || $row['stock'] < $qty) return false;
        $newStock = $row['stock'] - $qty;
        $stmt2 = $this->conn->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $stmt2->bind_param('ii', $newStock, $productId);
        if (!$stmt2->execute()) return false;
        $stmt3 = $this->conn->prepare('INSERT INTO transactions (product_id, qty) VALUES (?, ?)');
        $stmt3->bind_param('ii', $productId, $qty);
        return $stmt3->execute();
    }
}
