<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/class/Produk.php';
require __DIR__ . '/class/Transaksi.php';
$db = new Database;
$produk = new Produk($db->conn);
$transaksi = new Transaksi($db->conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_produk'])) {
        $nama = trim($_POST['name'] ?? '');
        $kategori = $_POST['category'] ?? 'Laptop';
        $stok = (int) ($_POST['stock'] ?? 0);
        $harga = (float) ($_POST['price'] ?? 0);
        if ($nama !== '' && in_array($kategori, ['Laptop', 'Smartphone']) && $stok >= 0) {
            $produk->tambah($nama, $kategori, $stok, $harga);
        }
    }
    if (isset($_POST['kurangi_stok'])) {
        $produkId = (int) ($_POST['product_id'] ?? 0);
        $qty = (int) ($_POST['qty'] ?? 0);
        if ($produkId > 0 && $qty > 0) {
            $transaksi->kurangi($produkId, $qty);
        }
    }
    header('Location: index.php');
    exit;
}

$semuaProduk = $produk->semua();
$semuaTransaksi = $transaksi->semua();
$stokMenipis = $produk->stokMenipis(5);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaris</title>
</head>
<body>
    <h1>Inventaris</h1>
    <h2>Dashboard</h2>
    <p>Total produk: <?= count($semuaProduk) ?></p>
    <p>Total transaksi: <?= count($semuaTransaksi) ?></p>
    <?php if ($stokMenipis): ?>
        <h3 style="color:red">Stok Menipis</h3>
        <ul>
            <?php foreach ($stokMenipis as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> - <?= $item['stock'] ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <h2>Daftar Produk</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Harga</th></tr>
        <?php foreach ($semuaProduk as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['category'] ?></td>
                <td><?= $item['stock'] ?></td>
                <td><?= $item['price'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h2>Tambah Produk</h2>
    <form method="post">
        <input name="name" placeholder="Nama produk" required>
        <select name="category">
            <option>Laptop</option>
            <option>Smartphone</option>
        </select>
        <input type="number" name="stock" min="0" value="0" required>
        <input type="number" name="price" min="0" step="0.01" value="0.00" required>
        <button name="tambah_produk">Simpan</button>
    </form>
    <h2>Kurangi Stok</h2>
    <form method="post">
        <select name="product_id">
            <?php foreach ($semuaProduk as $item): ?>
                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?> (<?= $item['stock'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="qty" min="1" required>
        <button name="kurangi_stok">Kurangi</button>
    </form>
    <h2>Rekap Transaksi</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr><th>ID</th><th>Produk</th><th>Qty</th><th>Tanggal</th></tr>
        <?php foreach ($semuaTransaksi as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= $item['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
