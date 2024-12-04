<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f6fb] min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md w-full mx-4">
        <img src="assets/image/success.png" alt="Success" class="w-24 h-24 mx-auto mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Permintaan Jemput Berhasil Dibuat!</h1>
        <p class="text-gray-600 mb-6">Mohon tunggu sebentar, Itrashy Picker akan segera menghubungi Anda.</p>
        <a href="dashboard.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg inline-block hover:bg-blue-600 transition-colors">Kembali ke Beranda</a>
    </div>
</body>
</html>