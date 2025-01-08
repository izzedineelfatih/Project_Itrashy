<?php
session_start();
require 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: staff_login.php");
    exit();
}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js"></script>
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
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .tox-tinymce {
            min-height: 300px !important;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1100;
        }

        /* Style untuk preview teks yang sudah di-format */
        .formatted-content {
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }

        .formatted-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: linear-gradient(transparent, white);
        }
    </style>
</head>
<body class="flex bg-gray-100">
    <?php include 'staff_sidebar.php'; ?>
    
    <div class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-2xl font-bold">Katalog Donasi</h1>
            <button onclick="openModal('addModal')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105">
                <i data-feather="plus-circle" class="inline-block mr-2"></i>
                Tambah Donasi
            </button>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Judul</th>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Deskripsi</th>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Gambar</th>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Collected</th>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Goal</th>
                        <th class="border border-gray-300 px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donasis as $donasi): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($donasi['title']); ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="formatted-content">
                                    <?php echo $donasi['deskripsi']; ?>
                                </div>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <img src="assets/image/<?php echo htmlspecialchars($donasi['image']); ?>" 
                                     alt="Donasi Image" 
                                     class="w-20 h-20 object-cover rounded-lg shadow">
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Rp <?php echo number_format($donasi['collected'], 0, ',', '.'); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Rp <?php echo number_format($donasi['goal'], 0, ',', '.'); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="flex gap-2">
                                    <button onclick="openModal('editModal<?php echo $donasi['id']; ?>')" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300">
                                        Edit
                                    </button>
                                    <button onclick="confirmDeletion('<?php echo $donasi['id']; ?>')" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div id="editModal<?php echo $donasi['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal('editModal<?php echo $donasi['id']; ?>')">&times;</span>
                                <h2 class="text-xl font-bold mb-4">Edit Donasi</h2>
                                <form action="update_donasi.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    <input type="hidden" name="id" value="<?php echo $donasi['id']; ?>">
                                    
                                    <div>
                                        <label class="block text-gray-700 mb-2">Judul:</label>
                                        <input type="text" name="title" value="<?php echo htmlspecialchars($donasi['title']); ?>" 
                                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 mb-2">Deskripsi:</label>
                                        <textarea name="deskripsi" class="tinymce-editor"><?php echo $donasi['deskripsi']; ?></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 mb-2">Collected (Rp):</label>
                                            <input type="number" name="collected" value="<?php echo $donasi['collected']; ?>" 
                                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                   required>
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 mb-2">Goal (Rp):</label>
                                            <input type="number" name="goal" value="<?php echo $donasi['goal']; ?>" 
                                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                   required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 mb-2">Gambar Baru (Opsional):</label>
                                        <input type="file" name="image" accept="image/*" 
                                               class="w-full px-4 py-2 rounded-lg border border-gray-300">
                                    </div>

                                    <button type="submit" 
                                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition duration-300">
                                        Update Donasi
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 class="text-xl font-bold mb-4">Tambah Donasi Baru</h2>
            <form action="add_donasi.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2">Judul:</label>
                    <input type="text" name="title" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Deskripsi:</label>
                    <textarea name="deskripsi" class="tinymce-editor"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Collected (Rp):</label>
                        <input type="number" name="collected" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Goal (Rp):</label>
                        <input type="number" name="goal" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Gambar:</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border border-gray-300" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition duration-300">
                    Tambah Donasi
                </button>
            </form>
        </div>
    </div>

    <script>
        // Initialize TinyMCE for all textareas with class 'tinymce-editor'
        tinymce.init({
            selector: '.tinymce-editor',
            height: 300,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false
        });

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            } else {
                console.error(`Modal dengan ID ${modalId} tidak ditemukan.`);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
            } else {
                console.error(`Modal dengan ID ${modalId} tidak ditemukan.`);
            }
        }

        function confirmDeletion(id) {
            if (confirm('Apakah Anda yakin ingin menghapus donasi ini?')) {
                window.location.href = `delete_donasi.php?id=${id}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        };
    </script>
</body>
</html>