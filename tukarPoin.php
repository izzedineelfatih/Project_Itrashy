<?php
session_start();
// Cek apakah user sudah login
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
                            <h4 class="text-2xl font-bold">50.000</h4>
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
                                    <span class="text-sm mt-2 text-center">Tagihan</span>
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
                    $voucher = [
                        [
                            'image' => 'indomaret.png',
                            'title' => 'Voucher potongan belanja Indomaret Rp 10.000',
                            'description' => 'Tukarkan poinmu dengan voucher belanja di Indomaret daerah JABODETABEK',
                            'points' => '12.000'
                        ],
                        [
                            'image' => 'alfamart.png',
                            'title' => 'Voucher belanja Alfamart Rp 10.000',
                            'description' => 'Tukarkan poinmu dengan voucher belanja di Alfamart daerah JABODETABEK',
                            'points' => '12.000'
                        ],
                        [
                            'image' => 'ayam bakar.png',
                            'title' => 'Voucher ayam bakar kalasan Rp 5.000',
                            'description' => 'Makan murah dengan voucher potongan di ayam bakar kalasan',
                            'points' => '5.000'
                        ],
                        [
                            'image' => 'naspad.png',
                            'title' => 'Voucher RM padang mahkota Rp 7.500',
                            'description' => 'Makan murah dengan voucher potongan di RM padang mahkota',
                            'points' => '35.000'
                        ]
                    ];

                    $sembako = [
                        [
                            'image' => 'minyak goreng.png',
                            'title' => 'Minyak Goreng 2 liter',
                            'description' => 'Minyak goreng habis? jangan khawatir tukarkan poinmu dengan minyak goreng 2L',
                            'points' => '35.000'
                        ],
                        [
                            'image' => 'gula.png',
                            'title' => 'Gula Pasir 1 Kilogram',
                            'description' => 'Tukarkan poinmu dengan sembako gula pasir',
                            'points' => '35.000'
                        ],
                        [
                            'image' => 'telur.png',
                            'title' => 'Telur 500gram',
                            'description' => 'Penuhi kebutuhan protein hewani anda dengan telur berkualitas',
                            'points' => '35.000'
                        ],
                        [
                            'image' => 'beras.png',
                            'title' => 'Beras 1 Kilogram',
                            'description' => 'Beras habis? jangan khawatir tukarkan poinmu dengan beras sekarang juga',
                            'points' => '35.000'
                        ]
                    ];

                    foreach ($voucher as $voucher_item) {
                        echo '<a href="#">';
                        echo '<div class="bg-white rounded-lg shadow-md p-3 voucher-card">';
                        echo '<img src="assets/image/' . $voucher_item['image'] . '" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">';
                        echo '<h4 class="font-semibold mt-2">' . $voucher_item['title'] . '</h4>';
                        echo '<p class="text-sm text-gray-500 mt-1">' . $voucher_item['description'] . '</p>';
                        echo '<div class="flex justify-between items-center mt-2">';
                        echo '<div class="flex items-center gap-2">';
                        echo '<img src="assets/icon/poin logo.png" alt="poin" class="h-8 w-8">';
                        echo '<div class="hidden sm:block"><p>Trash Poin</p></div>';
                        echo '</div>';
                        echo '<span class="text-blue-600 font-bold">' . $voucher_item['points'] . '</span>';
                        echo '</div></div></a>';
                    }

                    foreach ($sembako as $sembako_item) {
                        echo '<a href="#">';
                        echo '<div class="bg-white rounded-lg shadow-md p-3 sembako-card">';
                        echo '<img src="assets/image/' . $sembako_item['image'] . '" alt="Sembako Image" class="w-full h-32 md:h-40 object-cover rounded-lg">';
                        echo '<h4 class="font-semibold mt-2">' . $sembako_item['title'] . '</h4>';
                        echo '<p class="text-sm text-gray-500 mt-1">' . $sembako_item['description'] . '</p>';
                        echo '<div class="flex justify-between items-center mt-2">';
                        echo '<div class="flex items-center gap-2">';
                        echo '<img src="assets/icon/poin logo.png" alt="poin" class="h-8 w-8">';
                        echo '<div class="hidden sm:block"><p>Trash Poin</p></div>';
                        echo '</div>';
                        echo '<span class="text-blue-600 font-bold">' . $sembako_item['points'] . '</span>';
                        echo '</div></div></a>';
                    }
                    ?>
                </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            initializeFilter();
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

        // Fungsi untuk filter tampilan (Voucher, Sembako)
        function initializeFilter() {
            const voucherBtn = document.getElementById("voucher-btn");
            const sembakoBtn = document.getElementById("sembako-btn");

            const grid = document.querySelector(".grid");
            const voucherCards = document.querySelectorAll(".voucher-card");
            const sembakoCards = document.querySelectorAll(".sembako-card");

            // Fungsi untuk mengatur ulang grid layout
            function adjustGridLayout() {
                // Hapus semua placeholder yang ada
                const existingPlaceholders = grid.querySelectorAll('.placeholder');
                existingPlaceholders.forEach(placeholder => placeholder.remove());

                // Dapatkan jumlah card yang sedang ditampilkan
                const visibleCards = Array.from(grid.querySelectorAll('a')).filter(
                    card => window.getComputedStyle(card).display !== 'none'
                );

                // Tambahkan placeholder jika jumlah card kurang dari 4
                const placeholderCount = Math.max(0, 4 - visibleCards.length);
                for (let i = 0; i < placeholderCount; i++) {
                    const placeholderDiv = document.createElement('div');
                    placeholderDiv.className = 'placeholder bg-transparent';
                    grid.appendChild(placeholderDiv);
                }
            }

            // Fungsi untuk menampilkan hanya voucher cards
            function showVoucher() {
                voucherCards.forEach(card => {
                    card.closest('a').style.display = "block";
                });
                sembakoCards.forEach(card => {
                    card.closest('a').style.display = "none";
                });
                adjustGridLayout();
            }

            // Fungsi untuk menampilkan hanya sembako cards
            function showSembako() {
                voucherCards.forEach(card => {
                    card.closest('a').style.display = "none";
                });
                sembakoCards.forEach(card => {
                    card.closest('a').style.display = "block";
                });
                adjustGridLayout();
            }

            // Fungsi untuk memperbarui status aktif pada tombol
            function updateActiveButton(activeBtn) {
                // Reset semua tombol ke kondisi default
                const buttons = [voucherBtn, sembakoBtn];
                buttons.forEach(btn => {
                    btn.classList.remove("bg-blue-600", "text-white");
                    btn.classList.add("bg-gray-200", "text-gray-700");
                });

                // Menambahkan kelas aktif ke tombol yang dipilih
                activeBtn.classList.remove("bg-gray-200", "text-gray-700");
                activeBtn.classList.add("bg-blue-600", "text-white");
            }

            // Event listeners untuk tombol
            voucherBtn.addEventListener("click", () => {
                showVoucher();
                updateActiveButton(voucherBtn);
            });

            sembakoBtn.addEventListener("click", () => {
                showSembako();
                updateActiveButton(sembakoBtn);
            });

            // Tampilkan sembako secara default dan atur layout
            showSembako();
            updateActiveButton(sembakoBtn);
        }
    </script>
</body>
</html>