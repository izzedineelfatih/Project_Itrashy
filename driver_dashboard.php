<?php
session_start();
include 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'driver') {
    header("Location: staff_login.php");
    exit();
}

// Ambil data total order berdasarkan status dan tanggal
$currentDate = date('Y-m-d');

// Total order hari ini
$stmtTodayOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(pickup_date) = :currentDate");
$stmtTodayOrders->execute(['currentDate' => $currentDate]);
$totalOrdersToday = $stmtTodayOrders->fetchColumn();

// Order dengan status pending atau pickup
$stmtInProgressOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'pickup')");
$stmtInProgressOrders->execute();
$totalInProgressOrders = $stmtInProgressOrders->fetchColumn();

// Order dengan status done
$stmtCompletedOrders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE status = 'done'");
$stmtCompletedOrders->execute();
$totalCompletedOrders = $stmtCompletedOrders->fetchColumn();

// Query untuk mengambil order hari ini (hanya status pending dan pickup)
$stmtTodayOrdersList = $pdo->prepare("
    SELECT 
        o.id,
        u.username,
        u.phone_number,
        o.pickup_location,
        o.pickup_time,
        o.status,
        GROUP_CONCAT(s.title SEPARATOR ', ') AS sembako_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_sembako os ON o.id = os.pickup_order_id
    LEFT JOIN sembako s ON os.sembako = s.id
    WHERE DATE(o.pickup_date) = :currentDate
    AND o.status IN ('pending', 'pickup')
    GROUP BY o.id
    ORDER BY o.pickup_time ASC
");
$stmtTodayOrdersList->execute(['currentDate' => $currentDate]);
$todayOrders = $stmtTodayOrdersList->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
    <?php include 'staff_sidebar.php'; ?>
    
    <!-- Konten Utama -->
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <div class="flex items-center">
                <span class="mr-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['staff_username']); ?></span>
                <img src="assets/image/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full">
            </div>
        </header>

        <!-- Konten Dashboard -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <!-- Total Order Hari Ini -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Total Order Hari Ini</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $totalOrdersToday; ?></p>
            </div>

            <!-- Order Dalam Proses -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Order Dalam Proses</h2>
                <p class="text-3xl font-bold text-yellow-600"><?php echo $totalInProgressOrders; ?></p>
            </div>

            <!-- Order Selesai -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Order Selesai</h2>
                <p class="text-3xl font-bold text-green-600"><?php echo $totalCompletedOrders; ?></p>
            </div>
        </div>

        <!-- Tabel Order Hari Ini (Pending & Pickup) -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Jadwal Pickup Hari Ini</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Jam</th>
                            <th class="px-4 py-2 text-left">Pelanggan</th>
                            <th class="px-4 py-2 text-left">No. Telepon</th>
                            <th class="px-4 py-2 text-left">Lokasi</th>
                            <th class="px-4 py-2 text-left">Tukar Poin</th> <!-- Tambahkan kolom baru -->
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($todayOrders) > 0): ?>
                            <?php foreach ($todayOrders as $order): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <!-- pickup_time langsung tampilkan -->
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($order['pickup_time']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($order['phone_number']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($order['pickup_location']); ?></td>
                                    
                                    <!-- Kolom Tukar Poin (nama sembako) jika ada, jika tidak ada tampilkan '-' -->
                                    <td class="px-4 py-2">
                                        <?php echo $order['sembako_name'] ? htmlspecialchars($order['sembako_name']) : '-'; ?>
                                    </td>

                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 rounded-full text-sm
                                            <?php 
                                            echo $order['status'] === 'pending' 
                                                ? 'bg-yellow-100 text-yellow-800' 
                                                : 'bg-blue-100 text-blue-800';
                                            ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada order pending atau pickup untuk hari ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>   
</body>
</html>