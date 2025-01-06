<?php
session_start();
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: staff_login.php");
    exit();
}
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
<?php include 'staff_sidebar.php'; ?>
    <div class="flex-1 mx-auto p-5">
    <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Katalog Edukasi</h1>
            <div class="flex items-center">
                <span class="mr-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['staff_username']); ?></span>
                <img src="assets/image/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full">
            </div>
        </header>
     
<div class="bg-white rounded-lg shadow p-6">
  

        <!-- Tabs -->
        <div class="tabs flex justify-start mb-5">
            <a href="#articles" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Articles</a>
            <a href="#videos" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Videos</a>
            <a href="#events" class="px-4 py-2 bg-blue-500 text-white rounded">Events</a>
        </div>

        <!-- Articles -->
        <div id="articles" class="tab-content">
            <h2 class="text-2xl font-bold mb-3">Manage Articles</h2>
            <a href="form_article.php" class="bg-green-500 text-white px-4 py-2 rounded">Add Article</a>
            <table class="table-auto w-full mt-5">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Title</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM articles");
                    $isOdd = true; // Untuk mengatur warna baris
                    while ($row = $stmt->fetch()) {
                        $rowClass = $isOdd ? "bg-white" : "bg-gray-100";
                        $isOdd = !$isOdd;
                        echo "<tr class='$rowClass'>
                                <td class='border px-4 py-2'>{$row['id']}</td>
                                <td class='border px-4 py-2'>{$row['title']}</td>
                                <td class='border px-4 py-2'>
                                    <a href='edit_article.php?id={$row['id']}' class='text-blue-500'>Edit</a> |
                                    <a href='delete_article.php?type=article&id={$row['id']}' class='text-red-500'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Videos -->
        <div id="videos" class="tab-content hidden">
            <h2 class="text-2xl font-bold mb-3">Manage Videos</h2>
            <a href="form_video.php" class="bg-green-500 text-white px-4 py-2 rounded">Add Video</a>
            <table class="table-auto w-full mt-5">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Title</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM videos");
                    $isOdd = true; // Untuk mengatur warna baris
                    while ($row = $stmt->fetch()) {
                        $rowClass = $isOdd ? "bg-white" : "bg-gray-100";
                        $isOdd = !$isOdd;
                        echo "<tr class='$rowClass'>
                                <td class='border px-4 py-2'>{$row['id']}</td>
                                <td class='border px-4 py-2'>{$row['title']}</td>
                                <td class='border px-4 py-2'>
                                    <a href='edit_video.php?id={$row['id']}' class='text-blue-500'>Edit</a> |
                                    <a href='delete_article.php?type=video&id={$row['id']}' class='text-red-500'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Events -->
        <div id="events" class="tab-content hidden">
            <h2 class="text-2xl font-bold mb-3">Manage Events</h2>
            <a href="form_event.php" class="bg-green-500 text-white px-4 py-2 rounded">Add Event</a>
            <table class="table-auto w-full mt-5">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Title</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM events");
                    $isOdd = true; // Untuk mengatur warna baris
                    while ($row = $stmt->fetch()) {
                        $rowClass = $isOdd ? "bg-white" : "bg-gray-100";
                        $isOdd = !$isOdd;
                        echo "<tr class='$rowClass'>
                                <td class='border px-4 py-2'>{$row['id']}</td>
                                <td class='border px-4 py-2'>{$row['title']}</td>
                                <td class='border px-4 py-2'>
                                    <a href='form_event.php?id={$row['id']}' class='text-blue-500'>Edit</a> |
                                    <a href='delete_article.php?type=event&id={$row['id']}' class='text-red-500'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <script>
        document.querySelectorAll('.tabs a').forEach(tab => {
            tab.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
                const target = tab.getAttribute('href');
                document.querySelector(target).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
