<?php
session_start();
include 'config.php'; // Memuat koneksi database

// Pastikan staf sudah login dan memiliki hak akses yang sesuai
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit();
}

// Handle aksi dari tombol
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['order_id'])) {
        $action = $_POST['action'];
        $order_id = intval($_POST['order_id']);

        // Ambil order_sembako berdasarkan ID
        $stmtOrder = $pdo->prepare("SELECT * FROM order_sembako WHERE id = ?");
        $stmtOrder->execute([$order_id]);
        $order = $stmtOrder->fetch();

        if ($order) {
            if ($action == 'terima') {
                // Update status menjadi 'menunggu jemput sampah'
                $stmtUpdate = $pdo->prepare("UPDATE order_sembako SET status = 'menunggu jemput sampah' WHERE id = ?");
                $stmtUpdate->execute([$order_id]);
            } elseif ($action == 'pickup') {
                // Update status menjadi 'pickup'
                $stmtUpdate = $pdo->prepare("UPDATE order_sembako SET status = 'pickup' WHERE id = ?");
                $stmtUpdate->execute([$order_id]);
            } elseif ($action == 'selesai') {
                // Update status menjadi 'selesai'
                $stmtUpdate = $pdo->prepare("UPDATE order_sembako SET status = 'selesai' WHERE id = ?");
                $stmtUpdate->execute([$order_id]);
            } elseif ($action == 'tolak') {
                // Update status menjadi 'cancel'
                $stmtUpdate = $pdo->prepare("UPDATE order_sembako SET status = 'cancel' WHERE id = ?");
                $stmtUpdate->execute([$order_id]);

                // Kembalikan poin jika order ditolak
                try {
                    // Mulai transaksi
                    $pdo->beginTransaction();

                    // Ambil poin yang akan dikembalikan
                    $jumlah_poin = $order['jumlah_poin'];
                    $user_id = $order['user_id'];

                    // Tambahkan poin kembali ke user
                    $stmtRefund = $pdo->prepare("UPDATE users SET poin_terkumpul = poin_terkumpul + ? WHERE id = ?");
                    $stmtRefund->execute([$jumlah_poin, $user_id]);

                    // Commit transaksi
                    $pdo->commit();
                } catch (Exception $e) {
                    // Rollback jika terjadi error
                    $pdo->rollBack();
                    die("Terjadi kesalahan saat mengembalikan poin: " . $e->getMessage());
                }
            }

            // Redirect untuk menghindari resubmission
            header('Location: order_tukarPoin.php');
            exit();
        }
    }
}

// Ambil semua order_sembako beserta informasi pengguna dan sembako
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
    WHERE os.status IN ('pending', 'menunggu jemput sampah', 'pickup')
    ORDER BY os.created_at DESC
");
$orders = $stmtOrders->fetchAll();
?>

<!DOCTYPE html> 
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tukar Poin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
    <?php include 'staff_sidebar.php'; ?>

    <div class="flex-1 mx-auto p-5">
        <header class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold">Order Tukar Poin</h1>
        </header>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2 border">Nama Pelanggan</th>
                        <th class="px-4 py-2 border">Nomor Telepon</th>
                        <th class="px-4 py-2 border">Lokasi</th>
                        <th class="px-4 py-2 border">Tanggal dan Waktu</th>
                        <th class="px-4 py-2 border">Sembako</th>
                        <th class="px-4 py-2 border">Jumlah Poin</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
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
                                <td class="px-4 py-2 border"><?= htmlspecialchars($order['status']); ?></td>
                                <td class="px-4 py-2 border">
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <input type="hidden" name="action" value="terima">
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded mr-2">Terima</button>
                                        </form>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <input type="hidden" name="action" value="tolak">
                                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Tolak</button>
                                        </form>
                                    <!-- Jika status menunggu jemput sampah -->
                                    <?php elseif ($order['status'] == 'menunggu jemput sampah'): ?>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <input type="hidden" name="action" value="selesai">
                                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Selesai</button>
                                        </form>

                                    <!-- Jika status sudah pickup, tampilkan tombol Selesai saja -->
                                    <?php elseif ($order['status'] == 'pickup'): ?>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <input type="hidden" name="action" value="selesai">
                                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Selesai</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-2 border text-center">Tidak ada order yang ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
