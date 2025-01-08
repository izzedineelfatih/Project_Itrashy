<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data dari tabel orders berdasarkan user_id
$ordersQuery = "SELECT id, user_id, pickup_location, pickup_date, pickup_time, total_amount, status FROM orders WHERE user_id = :user_id";
$stmtOrders = $pdo->prepare($ordersQuery);
$stmtOrders->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtOrders->execute();
$ordersResult = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel voucher_redeem dan voucher berdasarkan user_id
$voucherQuery = "
    SELECT vr.id, vr.user_id, vr.voucher_id, v.points, vr.created_at, v.title
    FROM voucher_redeem vr
    INNER JOIN voucher v ON vr.voucher_id = v.id
    WHERE vr.user_id = :user_id
";
$stmtVoucher = $pdo->prepare($voucherQuery);
$stmtVoucher->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Menggunakan user_id dari sesi login
$stmtVoucher->execute();
$voucherResult = $stmtVoucher->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel order_sembako dan sembako
$sembakoQuery = "
    SELECT os.id, os.user_id, os.sembako, os.jumlah_poin, os.pickup_date, os.pickup_time, os.pickup_location, os.status, os.created_at, os.pickup_order_id, s.title
    FROM order_sembako os
    INNER JOIN sembako s ON os.sembako = s.id
    WHERE os.user_id = :user_id
";
$stmtSembako = $pdo->prepare($sembakoQuery);
$stmtSembako->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Menggunakan user_id dari sesi login
$stmtSembako->execute();
$sembakoResult = $stmtSembako->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy Edukasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
    <!-- Main Layout Container -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>
            
            <!-- Tabs -->
            <div class="m-8 border-b border-black">
                <button id="semua-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Semua</button>
                <button id="jemput-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Jemput Sampah</button>
                <button id="tukar-btn" class="px-4 py-2 text-gray-600 hover:text-blue-600">Tukar Poin</button>
            </div>
            
            <!-- Search and Filters -->
            <div class="ml-8 flex gap-4 items-center py-4">
                <input
                    type="text"
                    placeholder="Cari"
                    class="w-1/3 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                />
                <button class="flex items-center px-4 py-2 border rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filters
                </button>
            </div>

            <!-- Data List -->
            <div class="ml-4 p-4 space-y-4 max-h-96 overflow-y-auto" id="data-list">
                <?php
                if (!empty($ordersResult)) {
                    foreach ($ordersResult as $order) {
                        echo '<div class="data-item jemput-sampah">';
                        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
                        echo '<img src="icons/trash-icon.png" alt="Icon" class="w-10 h-10 mr-4">';
                        echo '<div class="flex-1">';
                        echo '<h3 class="font-bold text-gray-700">Penjemputan Sampah</h3>';
                        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($order['status']) . '</span></p>';
                        echo '</div>';
                        echo '<div class="text-sm text-gray-600">';
                        echo '<p>+ Rp ' . htmlspecialchars($order['total_amount']) . '</p>';
                        echo '<p class="text-gray-400">' . htmlspecialchars($order['pickup_date']) . '</p>';
                        echo '</div>';
                        echo '<div class="ml-4">';
                        // echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Detail</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }

                if (!empty($voucherResult)) {
                    foreach ($voucherResult as $voucher) {
                        echo '<div class="data-item tukar-poin">';
                        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
                        echo '<img src="icons/point-icon.png" alt="Icon" class="w-10 h-10 mr-4">';
                        echo '<div class="flex-1">';
                        echo '<h3 class="font-bold text-gray-700"> ' . htmlspecialchars($voucher['title']) . '</h3>';
                        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">Selesai</span></p>';
                        echo '</div>';
                        echo '<div class="text-sm text-gray-600">';
                        echo '<p>- ' . htmlspecialchars($voucher['points']) . ' Poin</p>';
                        echo '<p class="text-gray-400">' . htmlspecialchars($voucher['created_at']) . '</p>';
                        echo '</div>';
                        echo '<div class="ml-4">';
                        // echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Detail</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>

                <?php

if (!empty($sembakoResult)) {
    foreach ($sembakoResult as $item) {
        echo '<div class="data-item tukar-poin">';
        echo '<div class="flex justify-between bg-white p-4 rounded-md shadow-sm">';
        echo '<img src="icons/sembako-icon.png" alt="Sembako Icon" class="w-10 h-10 mr-4">'; // Icon default untuk sembako
        echo '<div class="flex-1">';
        echo '<h3 class="font-bold text-gray-700">Sembako ' . htmlspecialchars($item['title']) . '</h3>';
        echo '<p class="text-sm text-gray-500">Status: <span class="text-green-600">' . htmlspecialchars($item['status']) . '</span></p>';
        echo '</div>';
        echo '<div class="text-sm text-gray-600">';
        echo '<p class="text-sm text-gray-500">Jumlah Poin: ' . htmlspecialchars($item['jumlah_poin']) . '</p>';
        echo '<p>' . htmlspecialchars($item['created_at']) . '</p>';
        echo '</div>';
        echo '<div class="ml-4">';
        // echo '<button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Detail</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
?>



            </div>

        </div>
    </div>

    <script>
        // JavaScript untuk Tab Switching
        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            initializeTabSwitching();
        });

        // Fungsi untuk mengatur tab switching
        function initializeTabSwitching() {
            const semuaBtn = document.getElementById('semua-btn');
            const jemputBtn = document.getElementById('jemput-btn');
            const tukarBtn = document.getElementById('tukar-btn');
            const dataItems = document.querySelectorAll('.data-item');

            // Mengatur tampilan berdasarkan tombol yang dipilih
            function showTab(tab) {
                dataItems.forEach(item => {
                    if (tab === 'semua') {
                        item.style.display = 'block';
                    } else if (tab === 'jemput-sampah' && item.classList.contains('jemput-sampah')) {
                        item.style.display = 'block';
                    } else if (tab === 'tukar-poin' && item.classList.contains('tukar-poin')) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Event listener untuk tombol-tombol
            semuaBtn.addEventListener('click', () => {
                showTab('semua');
                resetButtonStyles();
                semuaBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            jemputBtn.addEventListener('click', () => {
                showTab('jemput-sampah');
                resetButtonStyles();
                jemputBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            tukarBtn.addEventListener('click', () => {
                showTab('tukar-poin');
                resetButtonStyles();
                tukarBtn.classList.add('border-blue-600', 'text-blue-600', 'border-b-2');
            });

            // Fungsi untuk mereset gaya tombol
            function resetButtonStyles() {
                semuaBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
                jemputBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
                tukarBtn.classList.remove('border-blue-600', 'text-blue-600', 'border-b-2');
            }

            // Default tampilkan Semua
            showTab('semua');
        }
    </script>
</body>
</html>
