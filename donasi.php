<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all donasi from database
$query = "SELECT * FROM katalog_donasi ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$donasis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi</title>
    <meta name="page-title" content="Donasi">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-[#f5f6fb] font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5">
                    <div class="shadow-lg mb-6">
                        <img src="assets/image/poster5.png" alt="Banner Image" class="w-full rounded-lg">
                    </div>

                    <div class="lg:hidden block bg-gradient-to-r from-[#FED4B4] to-[#54B68B] p-4 rounded-lg mb-10">
                        <div class="flex items-center space-x-4">
                            <img class="h-10 w-10" src="assets/icon/poin logo.png" alt="Poin">
                            <h4 class="text-2xl font-bold">Rp 50.000</h4>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php foreach ($donasis as $donasi): 
                            $progress = ($donasi['collected'] / $donasi['goal']) * 100;
                        ?>
                            <div class="bg-white rounded-lg shadow-md p-3">
                                <a href="detail_donasi.php?id=<?php echo $donasi['id']; ?>">
                                    <img src="assets/image/<?php echo htmlspecialchars($donasi['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($donasi['title']); ?>" 
                                         class="w-full h-40 object-cover rounded-lg">
                                    
                                    <h4 class="font-semibold mt-3 mb-2 line-clamp-2">
                                        <?php echo htmlspecialchars($donasi['title']); ?>
                                    </h4>
                                    
                                    <div class="relative mt-4">
                                        <div class="flex mb-2">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" 
                                                     style="width: <?php echo $progress; ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <strong>Rp <?php echo number_format($donasi['collected']); ?> terkumpul</strong> 
                                            dari Rp <?php echo number_format($donasi['goal']); ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
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