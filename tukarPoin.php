<?php
session_start();
include 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil ID user yang sedang login
$user_id = $_SESSION['user_id'];

// Query untuk mengambil total poin dari database
$stmt = $pdo->prepare("SELECT poin_terkumpul FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$poin_terkumpul = $stmt->fetchColumn(); // Mengambil jumlah poin

// Jika poin tidak ditemukan, set ke 0
if ($poin_terkumpul === false) {
    $poin_terkumpul = 0;
}

// Ambil data voucher dan sembako dari database
$stmtVoucher = $pdo->query("SELECT * FROM voucher");
$vouchers = $stmtVoucher->fetchAll();

$stmtSembako = $pdo->query("SELECT * FROM sembako");
$sembakos = $stmtSembako->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy Dashboard</title>
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
    <!-- Main Layout Container -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Header -->
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto">

                <!-- Balance Card -->
                <div class="p-5">
                    <div class="lg:flex justify-between lg:justify-around lg:space-x-52 lg:space-y-0 space-y-8 bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg">
                        <div class="flex items-center justify-center space-x-4">
                            <img class="h-10 w-10" src="assets/icon/poin logo.png" alt="Poin">
                            <h4 class="text-2xl font-bold"><?php echo number_format($poin_terkumpul); ?></h4>
                        </div>
                        <div class="flex justify-between lg:justify-end pl-5 pr-5 lg:pr-0 lg:pl-0 lg:space-x-20">
                            <!-- Transfer -->
                            <a href="transfer.php">
                                <div class="flex flex-col items-center">
                                    <button class="bg-white rounded-xl w-12 h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                        <img src="assets/icon/transfer.png" alt="Transfer" class="w-6 h-6">
                                    </button>
                                    <span class="text-sm mt-2 text-center">Transfer</span>
                                </div>
                            </a>
                            <!-- Tagihan -->
                            <a href="tagihan.php">
                                <div class="flex flex-col items-center">
                                    <button class="bg-white rounded-xl w-12 h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                        <img src="assets/icon/tagihan.png" alt="Tagihan" class="w-7 h-7">
                                    </button>
                                    <span class="text-sm mt-2 text-center">Pembelian</span>
                                </div>
                            </a>
                            <!-- Donasi -->
                            <a href="donasi.php">
                                <div class="flex flex-col items-center">
                                    <button class="bg-white rounded-xl w-12 h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                        <img src="assets/icon/donasi.png" alt="Donasi" class="w-6 h-6">
                                    </button>
                                    <span class="text-sm mt-2 text-center">Donasi</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Filter -->
                <div class="flex p-5 space-x-4">
                    <button id="sembako-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full">Sembako</button>
                    <button id="voucher-btn" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-full">Voucher</button>
                </div>

                <!-- Voucher dan Sembako -->
                <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-2 lg:gap-5">
                <?php 
                    // Tampilkan data voucher
                    foreach ($vouchers as $voucher) {
                        echo '<a href="detail_voucher.php?id=' . $voucher['id'] . '">';
                        echo '<div class="bg-white rounded-lg shadow-md p-3 voucher-card">';
                        echo '<img src="assets/image/' . htmlspecialchars($voucher['image']) . '" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">';
                        echo '<h4 class="font-semibold mt-2">' . htmlspecialchars($voucher['title']) . '</h4>';
                        echo '<p class="text-sm text-gray-500 mt-1">' . htmlspecialchars($voucher['description']) . '</p>';
                        echo '<div class="flex justify-between items-center mt-2">';
                        echo '<div class="flex items-center gap-2">';
                        echo '<img src="assets/icon/poin logo.png" alt="poin" class="h-8 w-8">';
                        echo '<div class="hidden sm:block"><p>Trash Poin</p></div>';
                        echo '</div>';
                        echo '<span class="text-blue-600 font-bold">' . htmlspecialchars($voucher['points']) . ' Poin</span>';
                        echo '</div></div></a>';
                    }

                    // Tampilkan data sembako
                    foreach ($sembakos as $sembako) {
                        echo '<a href="detail_sembako.php?id=' . $sembako['id'] . '">';
                        echo '<div class="bg-white rounded-lg shadow-md p-3 sembako-card">';
                        echo '<img src="assets/image/' . htmlspecialchars($sembako['image']) . '" alt="Sembako Image" class="w-full h-32 md:h-40 object-cover rounded-lg">';
                        echo '<h4 class="font-semibold mt-2">' . htmlspecialchars($sembako['title']) . '</h4>';
                        echo '<p class="text-sm text-gray-500 mt-1">' . htmlspecialchars($sembako['description']) . '</p>';
                        echo '<div class="flex justify-between items-center mt-2">';
                        echo '<div class="flex items-center gap-2">';
                        echo '<img src="assets/icon/poin logo.png" alt="poin" class="h-8 w-8">';
                        echo '<div class="hidden sm:block"><p>Trash Poin</p></div>';
                        echo '</div>';
                        echo '<span class="text-blue-600 font-bold">' . htmlspecialchars($sembako['points']) . ' Poin</span>';
                        echo '</div></div></a>';
                    }
                ?>
                </div>
                <?php include 'footer.php'; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeFilter();
            initializeMenu();
        });

        function initializeMenu() {
            const menuToggle = document.getElementById('menuToggle');
            const menuClose = document.getElementById('menuClose');
            const menu = document.getElementById('menu');
            const navLinks = document.querySelectorAll('.nav-link');
            const pageTitle = document.getElementById('pageTitle');
            
            // Dapatkan nama halaman dari URL saat ini
            const currentPage = window.location.pathname.split('/').pop().replace('.php', '');

            // Mobile menu toggles
            if (menuToggle && menuClose && menu) {
                menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
                menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
            }

            // Set active menu item dan update page title
            navLinks.forEach(link => {
                const href = link.getAttribute('href').replace('.php', '');
                const menuText = link.querySelector('span').textContent;
                
                if (href.includes(currentPage)) {
                    link.classList.add('active');
                    // Update page title sesuai menu yang aktif
                    if (pageTitle) {
                        if (currentPage === 'dashboard') {
                            pageTitle.textContent = `Halo, ${pageTitle.dataset.username}👋`;
                        } else {
                            pageTitle.textContent = menuText;
                        }
                    }
                }
            });
        }

        function initializeFilter() {
            const voucherBtn = document.getElementById("voucher-btn");
            const sembakoBtn = document.getElementById("sembako-btn");

            const voucherCards = document.querySelectorAll(".voucher-card");
            const sembakoCards = document.querySelectorAll(".sembako-card");

            function showVoucher() {
                // Tampilkan hanya kartu voucher
                voucherCards.forEach(card => card.closest('a').style.display = "block");
                sembakoCards.forEach(card => card.closest('a').style.display = "none");

                // Perbarui status tombol aktif
                updateActiveButton(voucherBtn);
            }

            function showSembako() {
                // Tampilkan hanya kartu sembako
                voucherCards.forEach(card => card.closest('a').style.display = "none");
                sembakoCards.forEach(card => card.closest('a').style.display = "block");

                // Perbarui status tombol aktif
                updateActiveButton(sembakoBtn);
            }

            function updateActiveButton(activeBtn) {
                // Reset semua tombol ke kondisi default
                const buttons = [voucherBtn, sembakoBtn];
                buttons.forEach(btn => {
                    btn.classList.remove("bg-blue-600", "text-white"); // Hapus gaya aktif
                    btn.classList.add("bg-gray-200", "text-gray-700"); // Tambahkan gaya default
                });

                // Tambahkan kelas aktif ke tombol yang dipilih
                activeBtn.classList.remove("bg-gray-200", "text-gray-700");
                activeBtn.classList.add("bg-blue-600", "text-white");
            }

            // Tambahkan event listener ke tombol
            voucherBtn.addEventListener("click", () => showVoucher());
            sembakoBtn.addEventListener("click", () => showSembako());

            // Tampilkan sembako secara default
            showSembako();
        }
    </script>
</body>
</html>
