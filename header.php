<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Menghubungkan ke database
require 'config.php';

// Ambil notifikasi
$stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
$notificationCount = count($notifications);
?>
<header class="sticky top-0 bg-white shadow-md z-40">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center space-x-4">
            <button id="menuToggle" class="lg:hidden">
                <svg class="w-6 h-6" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <h2 id="pageTitle" class="text-lg font-semibold" data-username="<?php echo htmlspecialchars($_SESSION['username'] ?? 'Pengguna'); ?>">
                <!-- Title akan diperbarui oleh JavaScript -->
            </h2>
        </div>
        
        <div class="flex items-center space-x-4 relative">
            <!-- Notifikasi -->
            <div class="relative">
                <button id="notifIcon" class="focus:outline-none">
                    <img src="assets/icon/notifikasi.png" alt="Notifications" class="w-8 h-8">
                    <!-- Badge -->
                    <span id="notifBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                    <?= $notificationCount ?>

                    </span>
                </button>

                <!-- Dropdown Notifikasi -->
                <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                <ul id="notifList" class="divide-y divide-gray-200">
    <?php foreach ($notifications as $notification): ?>
        <li class="p-4 hover:bg-gray-100" data-notif-id="<?= htmlspecialchars($notification['id']); ?>">
            <div class="bg-yellow-400 text-white p-3 rounded-full inline-block">
                📦
            </div>
            <div class="ml-4 inline-block align-top">
                <h3 class="text-lg font-semibold text-gray-800">Notifikasi Baru</h3>
                <p class="text-gray-500 text-sm"><?= htmlspecialchars($notification['created_at']) ?></p>
                <p class="text-gray-600 mt-2"><?= htmlspecialchars($notification['message']) ?></p>
                <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Lacak Trashy Picker
                </button>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="w-px h-10 bg-black"></div>
            
            <!-- Profile -->
            <a href="profile.php">
    <img src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'assets/image/profile.jpg'); ?>" alt="Profile" class="w-10 h-10 rounded-full">
</a>

        </div>
    </div>
    <script>
    function initializeMenu() {
        const menuToggle = document.getElementById('menuToggle');
        const menuClose = document.getElementById('menuClose');
        const menu = document.getElementById('menu');
        const navLinks = document.querySelectorAll('.nav-link');
        const pageTitle = document.getElementById('pageTitle');

        // Ambil judul halaman dari meta tag
        const metaPageTitle = document.querySelector('meta[name="page-title"]')?.getAttribute('content');

        // Jika pageTitle tersedia, update berdasarkan metaPageTitle
        if (pageTitle && metaPageTitle) {
            pageTitle.textContent = metaPageTitle;
        }

        // Toggle menu
        if (menuToggle && menuClose && menu) {
            menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
            menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
        }

        // Highlight tautan aktif di menu navigasi
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        navLinks.forEach(link => {
            const href = link.getAttribute('href').replace('.php', '');
            if (href.includes(currentPage)) {
                link.classList.add('active');
            }
        });

        // Notifikasi dropdown toggle
        const notifIcon = document.getElementById('notifIcon');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifBadge = document.getElementById('notifBadge');
        const notifList = document.getElementById('notifList');

        // Update badge jumlah notifikasi
        const updateNotifBadge = () => {
            const notifCount = notifList.querySelectorAll("li").length;
            if (notifCount > 0) {
                notifBadge.textContent = notifCount;
                notifBadge.classList.remove("hidden");
            } else {
                notifBadge.classList.add("hidden");
            }
        };

        notifIcon.addEventListener('click', function() {
            notifDropdown.classList.toggle('hidden');
        });

        updateNotifBadge(); // Update badge saat halaman dimuat

        if (notifList) {
            notifList.addEventListener('click', function (event) {
                // Cek apakah elemen yang diklik adalah item notifikasi
                const notifItem = event.target.closest('li');
                if (notifItem) {
                    const notifId = notifItem.getAttribute('data-notif-id'); // Pastikan ID notifikasi ada di data attribute

                    // Kirim permintaan untuk menghapus notifikasi
                    fetch(`delete_notification.php?id=${notifId}`)
                        .then(response => {
                            if (response.ok) {
                                // Hapus notifikasi dari tampilan
                                notifItem.remove();
                                // Update badge jumlah notifikasi
                                updateNotifBadge();
                            } else {
                                console.error('Gagal menghapus notifikasi');
                                alert('Gagal menghapus notifikasi. Silakan coba lagi.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        });
                }
            });
        }
    }

    // Jalankan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', initializeMenu);
</script>
</header>