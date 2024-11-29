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
    <title>Transfer</title>
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