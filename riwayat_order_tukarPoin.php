<?php
session_start();
include 'config.php'; // Memuat koneksi database

// Pastikan staf sudah login
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil semua order_sembako yang sudah selesai (dan/atau dibatalkan)
// Sesuaikan dengan kebutuhan Anda: 
// - Jika ingin menampilkan 'cancel' juga, gunakan "IN ('selesai', 'cancel')"
// - Jika hanya 'selesai', gunakan "= 'selesai'"

$stmtOrders = $pdo->query("
    SELECT 
        os.id, 
        os.user_id, 
        os.sembako, 
        os.jumlah_poin, 
        os.status, 
        os.pickup_date,
        os.pickup_time,
        os.pickup_location,
        os.pickup_order_id,
        os.created_at,
        u.username AS nama_pelanggan,
        u.phone_number AS nomor_telepon,
        s.title AS nama_sembako,
        o.pickup_date AS waste_pickup_date,
        o.pickup_time AS waste_pickup_time,
        o.pickup_location AS waste_pickup_location
    FROM order_sembako os
    JOIN users u ON os.user_id = u.id
    JOIN sembako s ON os.sembako = s.id
    LEFT JOIN orders o ON os.pickup_order_id = o.id
    WHERE os.status IN ('selesai', 'cancel')
    ORDER BY os.created_at DESC
");
$orders = $stmtOrders->fetchAll();
?>

<!DOCTYPE html> 
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Order Tukar Poin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
    <?php include 'staff_sidebar.php'; ?>

    <div class="flex-1 mx-auto p-5">
        <header class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold">Riwayat Order Tukar Poin</h1>
        </header>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2 border">Nama Pelanggan</th>
                        <th class="px-4 py-2 border">Nomor Telepon</th>
                        <th class="px-4 py-2 border">Lokasi</th>
                        <th class="px-4 py-2 border">Tanggal & Waktu</th>
                        <th class="px-4 py-2 border">Sembako</th>
                        <th class="px-4 py-2 border">Jumlah Poin</th>
                        <th class="px-4 py-2 border">Status</th>
                        <!-- Kolom Aksi dihilangkan, karena ini riwayat -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($order['nama_pelanggan']); ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($order['nomor_telepon']); ?></td>
                                <td class="px-4 py-2 border">
                                    <?= htmlspecialchars($order['pickup_location'] ?? 'Belum Ditentukan'); ?>
                                </td>
                                <td class="px-4 py-2 border">
                                    <?php 
                                        if ($order['pickup_date'] && $order['pickup_time']) {
                                            echo htmlspecialchars($order['pickup_date'] . ' ' . $order['pickup_time']);
                                        } else {
                                            echo 'Belum Dijadwalkan';
                                        }
                                    ?>
                                </td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($order['nama_sembako']); ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($order['jumlah_poin']); ?></td>
                                <td class="px-4 py-2 border">
                                    <span class="px-2 py-1 rounded-full text-sm
                                        <?php 
                                            if ($order['status'] == 'selesai') {
                                                echo 'bg-green-100 text-green-800';
                                            } elseif ($order['status'] == 'cancel') {
                                                echo 'bg-red-100 text-red-800';
                                            } else {
                                                echo 'bg-gray-100 text-gray-800';
                                            }
                                        ?>">
                                        <?= htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-2 border text-center">Belum ada riwayat tukar poin yang selesai.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
