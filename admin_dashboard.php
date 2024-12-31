<?php
session_start();

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
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
    <?php include 'admin_sidebar.php'; ?>
    
    <!-- Konten Utama -->
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <div class="flex items-center">
                <span class="mr-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <img src="assets/image/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full">
            </div>
        </header>

        <!-- Konten Dashboard -->
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Total Order Hari Ini</h2>
                <p class="text-3xl font-bold text-blue-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Order Dalam Proses</h2>
                <p class="text-3xl font-bold text-yellow-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold mb-4">Order Selesai</h2>
                <p class="text-3xl font-bold text-green-600">0</p>
            </div>
        </div>
    </div>

    
</body>
</html>