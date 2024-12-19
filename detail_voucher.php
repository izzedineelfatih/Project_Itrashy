<?php
session_start();
include 'config.php'; // Tambahkan ini untuk memuat koneksi database
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query database untuk mendapatkan detail voucher
$stmt = $pdo->prepare("SELECT * FROM voucher WHERE id = ?");
$stmt->execute([$id]);
$voucher = $stmt->fetch();

if (!$voucher) {
    die("Item tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Voucher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['THICCCBOI'],
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-[#f5f6fb] font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto p-7">
                <div class="flex flex-col bg-white max-w-md mx-auto p-5 rounded-lg shadow-lg">
                    <img src="assets/image/<?= htmlspecialchars($voucher['image']); ?>" alt="Gambar <?= htmlspecialchars($voucher['title']); ?>" class="mb-4 rounded-lg">
                    <div class="pr-5 pl-5">
                        <h1 class="font-bold text-lg mb-4"><?= htmlspecialchars($voucher['title']); ?></h1>
                        <p class="text-gray-600 mb-4 text-justify"><?= htmlspecialchars($voucher['description']); ?></p>
                        <div>
                            <h2 class="text-gray-600">Ketentuan Penukaran:</h2>
                            <ul class="list-disc pl-5">
                                <li class="text-gray-500 font-bold">Penukaran tidak dapat dibatalkan atau diubah setelah dikonfirmasi</li>
                                <li class="text-gray-500 font-bold">Voucher hanya berlaku di daerah JABODETABEK</li>
                            </ul>
                        </div>
                        <div class="mt-6 mb-6 mx-auto border-t-2 border-black-300"></div>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center">
                                <img src="assets/icon/poin logo.png" alt="Trash Poin" class="w-6 h-6 mr-2">
                                <span class="font-bold">Trash Poin</span>
                            </div>
                            <span class="font-bold text-lg"><?= htmlspecialchars($voucher['points']); ?></span>
                        </div>
                    </div>

                    <button class="w-full bg-blue-500 text-white mt-8 py-2.5 rounded-full hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">Tukar</button>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>
</body>
</html>
