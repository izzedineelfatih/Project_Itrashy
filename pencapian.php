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
    <title>I-Trashy Pencapaian</title>
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
    <div class="flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
    <?php include 'header.php'; ?>

    <div class="min-h-screen bg-gray-50 overflow-y-auto">
        <div class="container mx-auto p-6 space-y-8">
            <!-- Header -->
            <h2 class="text-2xl font-bold text-gray-800 text-center sm:text-left">
                Yuk, Lihat Statistik Pencapaianmu!
            </h2>
    
            <!-- Main Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Statistik Section -->
                <div class="col-span-1 lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sampah Terkumpul -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Sampah Terkumpul</h3>
                        <canvas id="sampahTerkumpulChart" class="my-4"></canvas>
                        <p class="text-2xl font-bold text-gray-800">140 Kg</p>
                        <p class="text-gray-500 text-sm">
                            Kumpulkan sampahmu, kurangi tumpukan sampah di TPA!
                        </p>
                    </div>
    
                    <!-- Jejak Karbon -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Jejak Karbon Berkurang</h3>
                        <canvas id="jejakKarbonChart" class="my-4"></canvas>
                        <p class="text-2xl font-bold text-gray-800">63 Kg CO<sub>2</sub></p>
                        <p class="text-gray-500 text-sm">
                            Ayo olah sampahmu untuk kurangi jejak karbonmu!
                        </p>
                    </div>
                </div>
    
                <!-- Leaderboard Section -->
                <div class="bg-blue-500 text-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-center mb-6">Leaderboard</h3>
                    <div class="flex justify-around items-center mb-6">
                        <!-- Top 3 -->
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto bg-white rounded-full overflow-hidden">
                                <img src="https://via.placeholder.com/100" alt="Alena Donin" class="w-full h-full object-cover">
                            </div>
                            <p class="font-bold mt-2">Alena Donin</p>
                            <p class="text-sm">45 Kg</p>
                        </div>
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto bg-white rounded-full overflow-hidden border-4 border-yellow-300">
                                <img src="https://via.placeholder.com/100" alt="Davis Curtis" class="w-full h-full object-cover">
                            </div>
                            <p class="font-bold mt-2">Davis Curtis</p>
                            <p class="text-sm">50 Kg</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto bg-white rounded-full overflow-hidden">
                                <img src="https://via.placeholder.com/100" alt="Craig Gouse" class="w-full h-full object-cover">
                            </div>
                            <p class="font-bold mt-2">Craig Gouse</p>
                            <p class="text-sm">36 Kg</p>
                        </div>
                    </div>
    
                    <!-- Additional Leaderboard -->
                    <ul class="bg-white text-gray-800 rounded-lg p-4 space-y-4">
                        <li class="flex justify-between items-center">
                            <p>4. Madelyn Dias</p>
                            <p>35 Kg</p>
                        </li>
                        <li class="flex justify-between items-center">
                            <p>5. Joko Anwar</p>
                            <p>30 Kg</p>
                        </li>
                        <li class="flex justify-between items-center">
                            <p>6. Frederick Stafford</p>
                            <p>28 Kg</p>
                        </li>
                        <li class="flex justify-between items-center">
                            <p>7. Andi Setiabudi</p>
                            <p>25 Kg</p>
                        </li>
                    </ul>
                </div>
            </div>
    <!-- #region -->
            <!-- Paling Sering Dikumpulkan -->
            <div class="h-screen overflow-y-auto bg-gray-50">
                <div class="container mx-auto p-6 space-y-8 pb-20">
                    <!-- Paling Sering Dikumpulkan -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Paling Sering Dikumpulkan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Sampah Plastik -->
                            <div class="flex items-center space-x-4">
                                <img src="assets/image/botol_plastik.png" alt="Sampah Plastik" class="w-12 h-12">
                                <div>
                                    <p class="font-bold text-gray-800">Sampah Plastik</p>
                                    <p class="text-sm text-gray-500">30 Kg</p>
                                    <p class="text-xs text-gray-400">Botol PET, Gelas plastik, Plastik lainnya</p>
                                </div>
                            </div>
                            <!-- Sampah Organik -->
                            <div class="flex items-center space-x-4">
                                <img src="assets/image/sampah_organik.png" alt="Sampah Organik" class="w-12 h-12">
                                <div>
                                    <p class="font-bold text-gray-800">Sampah Organik</p>
                                    <p class="text-sm text-gray-500">25 Kg</p>
                                    <p class="text-xs text-gray-400">Sampah sisa dapur dan daun kering</p>
                                </div>
                            </div>
                            <!-- Sampah Kardus -->
                            <div class="flex items-center space-x-4">
                                <img src="assets/image/kardus.png" alt="Sampah Kardus" class="w-12 h-12">
                                <div>
                                    <p class="font-bold text-gray-800">Sampah Kardus</p>
                                    <p class="text-sm text-gray-500">15 Kg</p>
                                    <p class="text-xs text-gray-400">Kardus tebal, kardus tipis, Kardus karton</p>
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
        initializeCharts();
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

        // Chart.js for Sampah Terkumpul
        function initializeCharts() {
        const ctx1 = document.getElementById('sampahTerkumpulChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sampah Terkumpul',
                    data: [10, 20, 30, 40, 25, 15],
                    backgroundColor: '#6366F1',
                }]
            },
            options: { responsive: true }
        });

        // Chart.js for Jejak Karbon
        const ctx2 = document.getElementById('jejakKarbonChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Jejak Karbon',
                    data: [5, 15, 10, 20, 8, 4],
                    borderColor: '#1E3A8A',
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                }]
            },
            options: { responsive: true }
        });
        }  
    </script>
</body>
</html>
