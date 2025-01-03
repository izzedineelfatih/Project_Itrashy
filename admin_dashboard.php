<?php
session_start();
include 'config.php'; // Sambungkan ke database

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
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
        <div class="grid grid-cols-3 gap-6">
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
    </div>   
</body>
</html>