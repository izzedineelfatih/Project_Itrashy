<?php
session_start();
require 'config.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Query untuk mengambil data donasi dari database
$query = "SELECT * FROM katalog_donasi ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$donasis = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
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
    
    <!-- Konten Utama -->
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Katalog Donasi</h1>
            <button onclick="openModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Donasi</button>
            <div class="flex items-center">
                <span class="mr-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <img src="assets/image/profile.jpg" alt="Profile" class="w-10 h-10 rounded-full">
            </div>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Judul</th>
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Deskripsi</th>
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Gambar</th>
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Collected</th> <!-- Kolom Collected -->
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Goal</th> <!-- Kolom Goal -->
                    <th class="border border-gray-300 px-4 py-2 text-left font-medium w-1/4">Edit</th>
                </tr>
            </thead>

                </thead>
                <tbody>
                    <?php foreach ($donasis as $donasi): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($donasi['title']); ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?php 
                                // Membatasi deskripsi hingga 30 karakter dan menambahkan ellipsis "..."
                                echo htmlspecialchars(substr($donasi['deskripsi'], 0, 30)) . (strlen($donasi['deskripsi']) > 30 ? '...' : '');
                                ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <img src="assets/image/<?php echo htmlspecialchars($donasi['image']); ?>" alt="Image" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($donasi['collected']); ?></td> <!-- Menampilkan collected -->
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($donasi['goal']); ?></td> <!-- Menampilkan goal -->
                            <td class="border border-gray-300 px-4 py-2">
                                <button onclick="openModal('editModal<?php echo $donasi['id']; ?>')"  class="bg-blue-500 text-white px-2 py-1 mb-2 rounded text-sm">Edit</button>
                                <a href="delete_donasi.php?id=<?php echo $donasi['id']; ?>" class="bg-green-500 text-white px-2 py-1 rounded text-sm">Delete</a>
                            </td>
                        </tr>
                        <div id="editModal<?php echo $donasi['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal('editModal<?php echo $donasi['id']; ?>')">&times;</span>
                                <h2 class="text-xl font-bold mb-4">Edit Donasi</h2>
                                <form action="update_donasi.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $donasi['id']; ?>"> <!-- ID donasi -->
                                    
                                    <label class="block text-gray-700 mb-2" for="title">Judul:</label>
                                    <input type="text" id="title" name="title" value="<?php echo $donasi['title']; ?>" required class="w-full">
                                    
                                    <label class="block text-gray-700 mb-2" for="deskripsi">Deskripsi:</label>
                                    <textarea id="deskripsi" name="deskripsi" required><?php echo $donasi['deskripsi']; ?></textarea>
                                    
                                    <label class="block text-gray-700 mb-2" for="collected">Collected:</label>
                                    <input type="number" id="collected" name="collected" value="<?php echo $donasi['collected']; ?>" required class="w-full">
                                    
                                    <label class="block text-gray-700 mb-2" for="goal">Goal:</label>
                                    <input type="number" id="goal" name="goal" value="<?php echo $donasi['goal']; ?>" required class="w-full">
                                    
                                    <label class="block text-gray-700 mb-2" for="image">Gambar (Opsional):</label>
                                    <input type="file" id="image" name="image" class="w-full">
                                    
                                    <button type="submit" class="w-full">Update Donasi</button>
                                </form>
                            </div>
                        </div>                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal untuk Tambah Donasi -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 class="text-xl font-bold mb-4">Tambah Donasi</h2>
            <form action="add_donasi.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label class="block text-gray-700 mb-2">Judul:</label>
                    <input type="text" name="title" required class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Deskripsi:</label>
                    <input type="text" name="deskripsi" required class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Collected:</label>
                    <input type="number" name="collected" required class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Goal:</label>
                    <input type="number" name="goal" required class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Gambar:</label>
                    <input type="file" name="image" required class="w-full">
                </div>
                <button type="submit" class="w-full">Tambah</button>
            </form>
        </div>
    </div>

   

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }

        // Inisialisasi TinyMCE untuk textarea dengan id 'deskripsi'
        ClassicEditor
            .create(document.querySelector('#deskripsi'))
            .catch(error => {
                console.error(error);
            });
    </script>
</body>
</html>
