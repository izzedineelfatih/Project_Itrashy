<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php'; // Pastikan file config.php ada dan berisi koneksi ke database

// Ambil data artikel berdasarkan ID dari URL
$artikel = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $artikel = $stmt->fetch();

    if (!$artikel) {
        echo "Artikel tidak ditemukan!";
        exit();
    }
} else {
    echo "ID artikel tidak valid!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($artikel['title']); ?></title>
    <meta name="page-title" content="Artikel Edukasi">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <style>
      
      .nav-link {
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #3b82f6;
        }

        .nav-link.active {
            background-color: #3968DA;
            color: white;
            margin-left: -20px;
            padding-left: 30px; 
            margin-right: 10px;
            padding-top: 10px;
            padding-bottom: 10px;
            font-weight: 500;
            border-radius: 0 15px 15px 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
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

            <!-- Content Section -->
            <div class="flex flex-1 overflow-auto p-4">
                <!-- Left Section -->
                <div class="flex-1 p-6 space-y-4 overflow-auto">
                    <img src="<?= htmlspecialchars($artikel['image_url']); ?>" alt="<?= htmlspecialchars($artikel['title']); ?>" class="w-full rounded-lg shadow-md">
                    <p class="text-sm text-gray-600 text-right"><?= htmlspecialchars($artikel['created_at']); ?></p>
                    <h1 class="text-2xl font-bold text-black"><?= htmlspecialchars($artikel['title']); ?></h1>
                    <p class="text-black leading-relaxed">
                        <?= nl2br(htmlspecialchars($artikel['content'])); ?>
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
