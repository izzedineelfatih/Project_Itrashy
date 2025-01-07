<?php
// Menghubungkan ke database
require 'config.php';

// Mengambil data notifikasi
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Notifikasi</h2>
        <div class="relative">
            <button id="notifIcon" class="focus:outline-none">
                <img src="assets/icon/notifikasi.png" alt="Notifications" class="w-8 h-8">
                <span id="notifBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center <?= count($notifications) > 0 ? '' : 'hidden' ?>">
                    <?= count($notifications) ?>
                </span>
            </button>

            <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                <ul id="notifList" class="divide-y divide-gray-200">
                    <?php foreach ($notifications as $notification): ?>
                        <li class="p-2"><?= htmlspecialchars($notification['message']); ?> <small class="text-gray-500"><?= htmlspecialchars($notification['created_at']); ?></small></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <script>
            const notifIcon = document.getElementById('notifIcon');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifBadge = document.getElementById('notifBadge');
            const notifList = document.getElementById('notifList');

            const updateNotifBadge = () => {
                const notifCount = notifList.querySelectorAll("li").length;
                if (notifCount > 0) {
                    notifBadge.textContent = notifCount;
                    notifBadge.classList.remove("hidden");
                } else {
                    notifBadge.classList.add("hidden");
                }
            };

            notifIcon.addEventListener('click', (event) => {
                event.stopPropagation();
                notifDropdown.classList.toggle('hidden');
                if (!notifDropdown.classList.contains('hidden')) {
                    notifBadge.classList.add('hidden');
                }
            });

            document.addEventListener('click', () => {
                notifDropdown.classList.add('hidden');
            });

            updateNotifBadge(); // Update badge saat halaman dimuat
        </script>
    </div>
</body>
</html>