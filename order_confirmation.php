<?php
// Menghubungkan ke database
require 'config.php';

if (!isset($_GET['id'])) {
    die("ID pesanan tidak ditemukan.");
}

$order_id = $_GET['id'];

// Mengambil data pesanan berdasarkan ID
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Mengambil data item pesanan
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Pesanan Anda Telah Berhasil Diterima</h2>
        <p>Pesanan ID: <?= $order['id']; ?></p>
        <p>Lokasi Penjemputan: <?= $order['location']; ?></p>
        <p>Waktu Penjemputan: <?= $order['pickup_date']; ?>, <?= $order['pickup_time']; ?></p>
        
        <h3>Daftar Item Sampah:</h3>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li><?= $item['name']; ?> - Rp. <?= number_format($item['total_price']); ?> (<?= $item['quantity']; ?> kg)</li>
            <?php endforeach; ?>
        </ul>

        <p>Terima kasih telah menggunakan layanan kami!</p>
    </div>
</body>
</html>
