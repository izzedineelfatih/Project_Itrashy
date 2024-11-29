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
    <title>I-Trashy Edukasi</title>
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

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-5 grid lg:grid-cols-3 gap-5">
                    <!-- Left Section (2 columns on desktop) -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Search Bar -->
                        <div class="relative">
                            <input type="text" placeholder="Cari..." 
                                class="w-full p-3 border border-gray-300 bg-white text-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-gray-400 transition">
                        </div>

                        <!-- Banner -->
                        <div class="relative overflow-hidden rounded-lg shadow-lg">
                            <img src="assets/image/poster2.png" alt="Banner Image" class="w-full">
                        </div>

                        <!-- Artikel Section -->
                        <div>
                            <h3 class="text-xl font-bold mb-4">Artikel</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="artikel.html">
                                    <img src="assets/image/gambar2.png" alt="Belajar Mengelola Sampah" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Yuk, Belajar Mengelola Sampah Sejak Dini Secara Mandiri</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="artikel.html">
                                    <img src="assets/image/gambar8.png" alt="Kerajinan Daur Ulang" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Cara Membuat Kerajinan Daur Ulang dari Barang Bekas</h4>
                                    <p class="text-sm text-gray-500 mt-1">Kompas.com • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="artikel.html">
                                    <img src="assets/image/gambar7.png" alt="Pupuk Kompos" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Pupuk Kompos untuk Tanaman Kesayangan Anda</h4>
                                    <p class="text-sm text-gray-500 mt-1">Detik.com • 02 Januari 2024</p>
                                </a>
                                </div>
                            </div>
                        </div>

                        <!-- Video Section -->
                        <div>
                            <h3 class="text-xl font-bold mb-4">Video</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="video.html">
                                    <img src="assets/image/gambar6.png" alt="Sampah Plastik" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Berapa Lama Sampah Plastik Terurai? Yuk, Cari Tahu</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="video.html">
                                    <img src="assets/image/gambar5.png" alt="Bersih Pantai" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Gerakan Bersih - Bersih Pantai di Indonesia</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="video.html">
                                    <img src="assets/image/gambar4.png" alt="Tips Pilah Sampah" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Tips Memilah Sampah Tanpa Ribet di Rumah</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-xl font-bold mb-4">Event Kami</h3>
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="event.html">
                                    <img src="assets/image/gambar11.png" alt="Belanja Tanpa Plastik" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Gerakan Belanja Tanpa Kantong Plastik</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="event.html">
                                    <img src="assets/image/gambar10.png" alt="Kerajinan UMKM" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Pameran Kerajinan Daur Ulang dari Sampah Bersama UMKM</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="event.html">
                                    <img src="assets/image/gambar9.png" alt="Donasi Sampah" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Gerakan Donasi Bersama I-Trashy</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </a>
                                </div>
                            </div>
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