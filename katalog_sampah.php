<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require 'config.php'; 

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
</head>
<body class="flex bg-gray-100 min-h-screen">
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="flex-1 p-6">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Katalog Sampah</h1>
            <button onclick="openModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tambah Jenis Sampah</button>
        </header>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2 border">Gambar</th>
                        <th class="px-4 py-2 border">Nama Sampah</th>
                        <th class="px-4 py-2 border">Harga</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jenis_sampah as $sampah): ?>
                        <tr>
                            <td class="px-4 py-2 border">
                                <img src="assets/image/<?php echo $sampah['image']; ?>" alt="<?php echo $sampah['name']; ?>" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="px-4 py-2 border"><?php echo $sampah['name']; ?></td>
                            <td class="px-4 py-2 border">Rp. <?php echo $sampah['price']; ?>/Kg</td>
                            <td class="px-4 py-2 border">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" onclick="openModal('editModal<?php echo $sampah['id']; ?>')">Edit</button>
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2" onclick="confirmDeletion('<?php echo $sampah['id']; ?>')">Hapus</button>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div id="editModal<?php echo $sampah['id']; ?>" class="modal fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
                            <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                                <span class="absolute top-4 right-4 text-xl cursor-pointer" onclick="closeModal('editModal<?php echo $sampah['id']; ?>')">&times;</span>
                                <h2 class="text-xl font-bold mb-4">Edit Jenis Sampah</h2>
                                <form action="update_sampah.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    <input type="hidden" name="id" value="<?php echo $sampah['id']; ?>">
                                    <div class="mb-4">
                                        <label class="block text-gray-700 mb-1">Nama Sampah:</label>
                                        <input type="text" name="name" value="<?php echo $sampah['name']; ?>" required class="w-full border border-gray-300 rounded-md p-2">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 mb-1">Harga:</label>
                                        <input type="number" name="price" value="<?php echo $sampah['price']; ?>" required class="w-full border border-gray-300 rounded-md p-2">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 mb-1">Gambar Saat Ini:</label>
                                        <img src="assets/image/<?php echo $sampah['image']; ?>" alt="<?php echo $sampah['name']; ?>" class="w-24 h-24 rounded-lg mb-2">
                                        <label class="block text-gray-700 mb-1">Ganti Gambar:</label>
                                        <input type="file" name="image" class="w-full border border-gray-300 rounded-md p-2">
                                    </div>
                                    <div class="flex justify-end mt-4">
                                        <button type="submit" class="bg-blue-500 text-white mr-4 px-6 py-2 rounded-md hover:bg-blue-600">Update</button>
                                        <button type="button" class="bg-gray-400 text-white px-6 py-2 rounded-lg" onclick="closeModal('editModal<?php echo $sampah['id']; ?>')">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah -->
        <div id="addModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                <span class="absolute top-4 right-4 text-xl cursor-pointer" onclick="closeModal('addModal')">&times;</span>
                <h2 class="text-xl font-bold mb-4">Tambah Jenis Sampah</h2>
                <form action="add_sampah.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Nama Sampah:</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Harga:</label>
                        <input type="number" name="price" required class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Gambar:</label>
                        <input type="file" name="image" required class="w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-blue-500 text-white mr-4 px-6 py-2 rounded-md hover:bg-blue-600">Tambah</button>
                        <button type="button" class="bg-gray-400 text-white px-6 py-2 rounded-lg" onclick="closeModal('addModal')">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.getElementById(modalId).classList.add('flex');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
        }

        function confirmDeletion(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                window.location.href = `delete_sampah.php?id=${id}`;
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.add('hidden');
                event.target.classList.remove('flex');
            }
        }
    </script>
</body>
</html>
