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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <main class="flex-1 overflow-y-auto">
                <div class="p-5">
                    <!-- Main Content Grid -->
                    <div class="flex flex-col lg:grid lg:grid-cols-3 lg:gap-5">
                        <!-- Left Section (2 columns on desktop) -->
                        <div class="lg:col-span-2 space-y-5 order-1">
                            <!-- Banner Slider -->
                            <div class="relative overflow-hidden rounded-lg shadow-lg">
                                <div id="slider" class="flex w-full">
                                    <img src="assets/image/poster1.png" alt="Banner 1" class="w-full">
                                    <img src="assets/image/poster2.png" alt="Banner 2" class="w-full">
                                    <img src="assets/image/poster3.png" alt="Banner 3" class="w-full">
                                </div>
                            </div>

                            <!-- Balance Card -->
                            <div class="bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg">
                                <div class="flex lg:flex-row md:justify-around lg:justify-around justify-between">
                                    <!-- Balance Display -->
                                    <div class="flex items-center space-x-2 justify-center">
                                        <img src="assets/icon/poin logo.png" alt="Poin" class="md:w-10 lg:w-10 w-8">
                                        <h4 class="text-xl lg:text-2xl font-bold">50.000</h4>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex lg:justify-end space-x-5 lg:space-x-16 md:space-x-16">
                                        <!-- Transfer -->
                                        <a href="transfer.php">
                                            <div class="flex flex-col items-center">
                                                <button class="bg-white rounded-xl w-10 h-10 lg:w-12 lg:h-12 md:w-12 md:h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                                    <img src="assets/icon/transfer.png" alt="Transfer" class="md:w-6 md:h-6 lg:w-6 lg:h-6 w-5 h-5">
                                                </button>
                                                <span class="lg:text-sm md:text-sm text-xs mt-2 text-center">Transfer</span>
                                            </div>
                                        </a>
                                        <!-- Tagihan -->
                                        <a href="tagihan.php">
                                            <div class="flex flex-col items-center">
                                                <button class="bg-white rounded-xl w-10 h-10 lg:w-12 lg:h-12 md:w-12 md:h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                                    <img src="assets/icon/tagihan.png" alt="Tagihan" class="md:w-7 md:h-7 lg:w-7 lg:h-7 w-6 h-6">
                                                </button>
                                                <span class="lg:text-sm md:text-sm text-xs mt-2 text-center">Tagihan</span>
                                            </div>
                                        </a>
                                        <!-- Donasi -->
                                        <a href="donasi.php">
                                            <div class="flex flex-col items-center">
                                                <button class="bg-white rounded-xl w-10 h-10 lg:w-12 lg:h-12 md:w-12 md:h-12 flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                                                    <img src="assets/icon/donasi.png" alt="Donasi" class="md:w-6 md:h-6 lg:w-6 lg:h-6 w-5 h-5">
                                                </button>
                                                <span class="lg:text-sm md:text-sm text-xs mt-2 text-center">Donasi</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- News Section - Desktop Only -->
                            <div class="hidden lg:block">
                                <h3 class="text-xl font-bold mb-4">Terbaru</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- News Card 1 -->
                                    <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                        <img src="assets/image/gambar2.png" alt="Belajar Mengelola Sampah" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2">Yuk, Belajar Mengelola Sampah Sejak Dini Secara Mandiri</h4>
                                        <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                    </div>
                                    <!-- News Card 2 -->
                                    <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                        <img src="assets/image/gambar8.png" alt="Kerajinan Daur Ulang" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2">Cara Membuat Kerajinan Daur Ulang dari Barang Bekas</h4>
                                        <p class="text-sm text-gray-500 mt-1">Kompas.com • 02 Januari 2024</p>
                                    </div>
                                    <!-- News Card 3 -->
                                    <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                        <img src="assets/image/gambar7.png" alt="Pupuk Kompos" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2">Pupuk Kompos untuk Tanaman Kesayangan Anda</h4>
                                        <p class="text-sm text-gray-500 mt-1">Detik.com • 02 Januari 2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Section (Charts & Schedule) -->
                        <div class="space-y-4 mt-10 lg:mt-0 lg:bg-white lg:p-5 lg:rounded-lg order-2 lg:order-3">
                            <!-- Pickup Schedule -->
                            <div class="bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4 h-[200px]">
                                <h4 class="font-bold text-lg mb-3">Jadwal Penjemputan</h4>
                                <div class="flex flex-col items-center">
                                    <img src="assets/image/Schedule.png" alt="schedule" class="w-20">
                                    <p class="text-sm pt-2 text-gray-500 text-center">Jadwal Kosong</p>
                                </div>
                            </div>
                    
                            <!-- Collected Garbage Chart -->
                            <div class="bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4 h-[220px]">
                                <h4 class="font-bold text-lg mb-3">Sampah Terkumpul</h4>
                                <div class="h-[150px]">
                                    <canvas id="garbageChart"></canvas>
                                </div>
                            </div>

                            <!-- Carbon Footprint Chart -->
                            <div class="bg-white lg:bg-[#f5f6fb] rounded-lg shadow p-4 h-[220px]">
                                <h4 class="font-bold text-lg mb-3">Jejak Karbon Terkurangi</h4>
                                <div class="pl-50 h-[150px]">
                                    <canvas id="carbonChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- News Section - Mobile Only -->
                        <div class="lg:hidden mt-10 lg:mt-0 order-3">
                            <h3 class="text-xl font-bold mb-4">Terbaru</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Mobile News Card 1 -->
                                <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                    <img src="assets/image/gambar2.png" alt="Belajar Mengelola Sampah" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Yuk, Belajar Mengelola Sampah Sejak Dini Secara Mandiri</h4>
                                    <p class="text-sm text-gray-500 mt-1">I-Trashy • 02 Januari 2024</p>
                                </div>
                                <!-- Mobile News Card 2 -->
                                <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                    <img src="assets/image/gambar8.png" alt="Kerajinan Daur Ulang" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Cara Membuat Kerajinan Daur Ulang dari Barang Bekas</h4>
                                    <p class="text-sm text-gray-500 mt-1">Kompas.com • 02 Januari 2024</p>
                                </div>
                                <!-- Mobile News Card 3 -->
                                <div class="bg-white rounded-lg shadow-md p-3 news-card">
                                    <img src="assets/image/gambar7.png" alt="Pupuk Kompos" class="w-full h-32 object-cover rounded-lg">
                                    <h4 class="font-semibold mt-2">Pupuk Kompos untuk Tanaman Kesayangan Anda</h4>
                                    <p class="text-sm text-gray-500 mt-1">Detik.com • 02 Januari 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
        initializeMenu();
        initializeCharts();
        initializeBannerSlider();
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

        function initializeBannerSlider() {
            let currentIndex = 0;
            const slider = document.getElementById('slider');
            if (!slider) return;
            
            const bannerImages = slider.children;

            function slideBanner() {
                currentIndex = (currentIndex + 1) % bannerImages.length;
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            }
            setInterval(slideBanner, 5000);
        }

        function initializeCharts() {
            const chartConfig = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 0
                    }
                }
            };

            // Garbage Chart
            const garbageChartCtx = document.getElementById('garbageChart')?.getContext('2d');
            if (garbageChartCtx) {
                new Chart(garbageChartCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Sampah Terkumpul (kg)',
                            data: [10, 20, 30, 40, 17, 15],
                            backgroundColor: '#6C63FF',
                            borderRadius: 5,
                            barThickness: 20,
                        }]
                    },
                    options: chartConfig
                });
            }

            // Carbon Chart
            const carbonChartCtx = document.getElementById('carbonChart')?.getContext('2d');
            if (carbonChartCtx) {
                const gradient = carbonChartCtx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(108, 99, 255, 0.5)');
                gradient.addColorStop(1, 'rgba(108, 99, 255, 0)');

                new Chart(carbonChartCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Jejak Karbon (kg CO₂)',
                            data: [5, 8, 10, 15, 8, 7],
                            borderColor: '#6C63FF',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: chartConfig
                });
            }
        }
    </script>
</body>
</html>