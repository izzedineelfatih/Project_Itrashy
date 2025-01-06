<?php
    session_start();
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    require 'config.php';

// Ambil semua artikel dari database
$stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
$artikels = $stmt->fetchAll();
// Query for videos
$stmt_videos = $pdo->query("SELECT * FROM Videos ORDER BY created_at DESC");
$videos = $stmt_videos->fetchAll();
// Query for events
$stmt_events = $pdo->query("SELECT * FROM Events ORDER BY created_at DESC");
$events = $stmt_events->fetchAll();
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
    <!-- Main Layout Container -->
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col  lg:ml-0">
        <!-- Header -->
        <?php include 'header.php'; ?>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-5 grid lg:grid-cols-3 gap-5">
                <!-- Left Section (2 columns on desktop) -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Banner -->
                    <div class="relative overflow-hidden rounded-lg shadow-lg">
                        <img src="assets/image/poster2.png" alt="Banner Image" class="w-full">
                    </div>
                    <!-- Artikel Section -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">Artikel</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($artikels as $artikel): ?>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="artikel.php?id=<?= $artikel['id']; ?>">
                                        <img src="<?= htmlspecialchars($artikel['image_url']); ?>" alt="<?= htmlspecialchars($artikel['title']); ?>" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2"><?= htmlspecialchars($artikel['title']); ?></h4>
                                        <div class="flex justify-between">
                                            <p class="text-sm text-gray-500 mt-1 ">I-Trashy</p>
                                            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars(date('d F Y', strtotime($artikel['created_at']))) ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Video Section -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">Video</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($videos as $video) { ?>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="video.php?id=<?php echo htmlspecialchars($video['id']); ?>">
                                        <img src="<?php echo htmlspecialchars($video['video_url']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="w-full h-32 md:h-40 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2"><?php echo htmlspecialchars($video['title']); ?></h4>
                                        <div class="flex justify-between">
                                            <p class="text-sm text-gray-500 mt-1 ">I-Trashy</p>
                                            <p class="text-sm text-gray-500 mt-1 "><?= htmlspecialchars(date('d F Y', strtotime($video['created_at']))) ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Right Section (Event) -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-xl font-bold mb-4">Event Kami</h3>
                        <div class="space-y-4">
                            <?php foreach ($events as $event) { ?>
                                <div class="bg-white rounded-lg shadow-md p-3">
                                    <a href="event.php?id=<?php echo htmlspecialchars($event['id']); ?>">
                                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full h-32 object-cover rounded-lg">
                                        <h4 class="font-semibold mt-2"><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <div class="flex justify-between">
                                            <p class="text-sm text-gray-500 mt-1 ">I-Trashy</p>
                                            <p class="text-sm text-gray-500 mt-1 "><?= htmlspecialchars(date('d F Y', strtotime($event['created_at']))) ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="flex-1 overflow-y-auto">
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