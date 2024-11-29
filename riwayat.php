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
                    <button class="px-4 py-2 text-blue-600 border-b-2 border-blue-600">Semua</button>
                    <button class="px-4 py-2 text-gray-600 hover:text-blue-600">Jemput Sampah</button>
                    <button class="px-4 py-2 text-gray-600 hover:text-blue-600">TrashPay</button>
                    <button class="px-4 py-2 text-gray-600 hover:text-blue-600">Tukar Poin</button>
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
<div class="ml-4 p-4 space-y-4 max-h-96 overflow-y-auto">
    <!-- Item 1 -->
    <div class="flex items-center bg-white p-4 rounded-md shadow-sm">
        <img src="icons/trash-icon.png" alt="Icon" class="w-10 h-10 mr-4">
        <div class="flex-1">
            <h3 class="font-bold text-gray-700">Penjemputan Sampah</h3>
            <p class="text-sm text-gray-500">Status: <span class="text-green-600">Selesai</span></p>
        </div>
        <div class="text-sm text-gray-600">
            <p>+ Rp 10.000</p>
            <p class="text-gray-400">6 Maret 2024</p>
        </div>
        <div class="ml-4">
            <button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Detail
            </button>
        </div>
    </div>

    <!-- Item 2 -->
    <div class="flex items-center bg-white p-4 rounded-md shadow-sm">
        <img src="icons/point-icon.png" alt="Icon" class="w-10 h-10 mr-4">
        <div class="flex-1">
            <h3 class="font-bold text-gray-700">Tukar Poin</h3>
            <p class="text-sm text-gray-500">Status: <span class="text-green-600">Selesai</span></p>
        </div>
        <div class="text-sm text-gray-600">
            <p>- 2000 Poin</p>
            <p class="text-gray-400">1 Maret 2024</p>
        </div>
        <div class="ml-4">
            <button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Detail
            </button>
        </div>
    </div>

    <!-- Item 3 -->
    <div class="flex items-center bg-white p-4 rounded-md shadow-sm">
        <img src="icons/bill-icon.png" alt="Icon" class="w-10 h-10 mr-4">
        <div class="flex-1">
            <h3 class="font-bold text-gray-700">Tagihan</h3>
            <p class="text-sm text-gray-500">Status: <span class="text-green-600">Selesai</span></p>
        </div>
        <div class="text-sm text-gray-600">
            <p>+ Rp 30.000</p>
            <p class="text-gray-400">20 Februari 2024</p>
        </div>
        <div class="ml-4">
            <button class="text-sm px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Detail
            </button>
        </div>
    </div>
</div>

             
            
                <!-- Pagination -->
                <div class="gap-4 flex justify-between items-center mt-4 bg-white p-4 shadow-md">
                    <div class="text-gray-600">Tampilkan: 10 baris</div>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 border rounded-md bg-gray-100 hover:bg-gray-200">&lt;</button>
                        <button class="px-4 py-2 border rounded-md bg-blue-600 text-white">1</button>
                        <button class="px-4 py-2 border rounded-md bg-gray-100 hover:bg-gray-200">&gt;</button>
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