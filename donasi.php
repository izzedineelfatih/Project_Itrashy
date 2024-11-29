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
    <title>Donasi</title>
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
                <!-- kode tulis di sini -->
                <div class="p-5">
                    <div class="relative overflow-hidden rounded-lg shadow-lg mb-6">
                        <img src="assets/image/poster5.png" alt="Banner Image" class="w-full">
                    </div>

                    <div class="bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg mb-10">
                        <div class="flex items-center space-x-4">
                            <img class="h-10 w-10" src="assets/icon/poin logo.png" alt="Poin">
                            <h4 class="text-2xl font-bold">Rp 50.000</h4>
                        </div>
                    </div>

                    <!-- donasi card -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-2 lg:gap-5">
                        <!-- donasi card 1 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/bank sampah.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Pembangunan Fasilitas Bank Sampah di Bandung</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 20%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 10.000.000 terkumpul</strong> dari Rp 100.000.000</label>
                                </div>
                            </a>
                        </div>
                        <!-- donasi card 2 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/menanam pohon.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Penanaman 1000 Bibit Mangrove di Pantai Indonesia</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 40%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 20.000.000 terkumpul</strong> dari Rp 50.000.000</label>
                                </div>
                            </a>
                        </div>
                        <!-- donasi card 3 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/petugas kebersihan.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Bantuan Kemanusiaan Untuk Petugas Kebersihan di Indonesia</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 50%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 50.000.000 terkumpul</strong> dari Rp 100.000.000</label>
                                </div>
                            </a>
                        </div>
                        <!-- donasi card 4 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/terumbu karang.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Restorasi Terumbu Karang di Pantai Sumatera</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 90%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 10.000.000 terkumpul</strong> dari Rp 15.000.000</label>
                                </div>
                            </a>
                        </div>
                        <!-- donasi card 5 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/daur ulang kerajinan.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Pendanaan UMKM kerajinan Daur Ulang Sampah</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 30%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 10.000.000 terkumpul</strong> dari Rp 40.000.000</label>
                                </div>
                            </a>
                        </div>
                        <!-- donasi card 6 -->
                        <div class="bg-white rounded-lg shadow-md p-3 donasi-card">
                            <a href="#">
                                <img src="assets/image/membersihkan sungai.png" alt="Voucher Image" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                <h4 class="font-semibold mt-2">Program Bersih - Bersih Sungai di Indonesia</h4>
                                <div class="relative mt-4 mb-4">
                                    <div class="flex mt-2">
                                        <!-- Progress bar -->
                                        <div class="relative flex mb-2 w-full">
                                        <div class="flex-1 bg-gray-300 rounded-full h-2.5">
                                            <!-- This is the progress fill -->
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 70%"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <label for="progress" class="donasi-terkumpul block text-sm font-medium text-gray-700"><strong>Rp 15.000.000 terkumpul</strong> dari Rp 25.000.000</label>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
    </script>
</body>
</html>