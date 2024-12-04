<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require 'config.php'; // Pastikan ini adalah file koneksi database yang Anda buat sebelumnya

// Ambil data jenis sampah dari database
$stmt = $pdo->query("SELECT * FROM jenis_sampah");
$jenis_sampah = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Sampah - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Form styling */
        .modal form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .modal input[type="text"],
        .modal input[type="number"],
        .modal input[type="file"] {
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
        }

        .modal button[type="submit"] {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
        }

        .modal button[type="submit"]:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body class="flex bg-gray-100">
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Katalog Sampah</h1>
            <button onclick="openModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Jenis Sampah</button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($jenis_sampah as $sampah): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <img src="assets/image/<?php echo $sampah['image']; ?>" alt="<?php echo $sampah['name']; ?>" class="h-20 w-20 object-cover mb-4">
                    <h2 class="text-lg font-semibold mb-2"><?php echo $sampah['name']; ?></h2>
                    <p class="text-gray-500">Rp. <?php echo $sampah['price']; ?>/Kg</p>
                    <div class="mt-4">
                        <button onclick="openModal('editModal<?php echo $sampah['id']; ?>')" class="text-blue-500 hover:text-blue-700">Edit</button>
                        <a href="delete_sampah.php?id=<?php echo $sampah['id']; ?>" class="text-red-500 hover:text-red-700 ml-2">Hapus</a>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div id="editModal<?php echo $sampah['id']; ?>" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('editModal<?php echo $sampah['id']; ?>')">&times;</span>
                        <h2 class="text-xl font-bold mb-4">Edit Jenis Sampah</h2>
                        <form action="update_sampah.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $sampah['id']; ?>">
                            <div>
                                <label class="block text-gray-700 mb-2">Nama Sampah:</label>
                                <input type="text" name="name" value="<?php echo $sampah['name']; ?>" required class="w-full">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Harga:</label>
                                <input type="number" name="price" value="<?php echo $sampah['price']; ?>" required class="w-full">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Gambar:</label>
                                <input type="file" name="image" class="w-full">
                            </div>
                            <button type="submit" class="w-full">Update</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal Tambah -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('addModal')">&times;</span>
                <h2 class="text-xl font-bold mb-4">Tambah Jenis Sampah</h2>
                <form action="add_sampah.php" method="POST" enctype="multipart/form-data">
                    <div>
                        <label class="block text-gray-700 mb-2">Nama Sampah:</label>
                        <input type="text" name="name" required class="w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Harga:</label>
                        <input type="number" name="price" required class="w-full">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Gambar:</label>
                        <input type="file" name="image" required class="w-full">
                    </div>
                    <button type="submit" class="w-full">Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>