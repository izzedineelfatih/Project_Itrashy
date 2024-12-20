<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php'; // Koneksi ke database

// Query data sembako
$stmtSembako = $pdo->query("SELECT * FROM sembako");
$sembakos = $stmtSembako->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Sembako</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function openAddModal() {
            document.getElementById('add-modal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('add-modal').classList.add('hidden');
        }

        function openEditModal(id, title, description, points, image) {
            document.getElementById('edit-modal').classList.remove('hidden');
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-points').value = points;
            document.getElementById('current-image').src = "assets/image/" + image;
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }
    </script>
</head>
<body class="flex bg-gray-100">
<?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 mx-auto p-5">
        <header class="flex justify-between items-center mb-5">
            <h1 class="text-3xl font-bold">Katalog Sembako</h1>
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Tambah Sembako</button>
        </header>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2 border">Gambar</th>
                        <th class="px-4 py-2 border">Judul</th>
                        <th class="px-4 py-2 border">Deskripsi</th>
                        <th class="px-4 py-2 border">Poin</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sembakos as $sembako): ?>
                        <tr>
                            <td class="px-4 py-2 border">
                                <img src="assets/image/<?= $sembako['image'] ?>" alt="<?= $sembako['title'] ?>" class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="px-4 py-2 border truncate max-w-xs">
                                <?= htmlspecialchars($sembako['title']) ?>
                            </td>
                            <td class="px-4 py-2 border truncate max-w-xs">
                                <?= htmlspecialchars($sembako['description']) ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?= htmlspecialchars($sembako['points']) ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" onclick="openEditModal('<?= $sembako['id'] ?>', '<?= htmlspecialchars($sembako['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sembako['description'], ENT_QUOTES) ?>', '<?= $sembako['points'] ?>', '<?= $sembako['image'] ?>')">Edit</button>
                                <a href="delete_sembako.php?id=<?= $sembako['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Sembako -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full relative">
            <span class="absolute top-4 right-4 text-xl cursor-pointer" onclick="closeAddModal()">&times;</span>
            <h2 class="text-2xl font-bold mb-4">Tambah Sembako</h2>
            <form method="POST" action="add_sembako.php" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="title">Judul</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="w-full border border-gray-300 rounded-md p-2" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="points">Poin</label>
                    <input type="number" id="points" name="points" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="image">Gambar</label>
                    <input type="file" id="image" name="image" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-500 text-white mr-4 px-6 py-2 rounded-md hover:bg-blue-600">Tambah</button>
                    <button type="button" class="bg-gray-400 text-white px-6 py-2 rounded-lg" onclick="closeAddModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Sembako -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full relative">
            <span class="absolute top-4 right-4 text-xl cursor-pointer" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-bold mb-4">Edit Sembako</h2>
            <form method="POST" action="update_sembako.php" enctype="multipart/form-data">
                <input type="hidden" id="edit-id" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-title">Judul</label>
                    <input type="text" id="edit-title" name="title" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-description">Deskripsi</label>
                    <textarea id="edit-description" name="description" class="w-full border border-gray-300 rounded-md p-2" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-points">Poin</label>
                    <input type="number" id="edit-points" name="points" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Gambar Saat Ini</label>
                    <img id="current-image" src="" alt="Current Image" class="w-24 h-24 rounded-lg mb-2">
                    <label class="block text-gray-700 mb-2" for="edit-image">Ganti Gambar</label>
                    <input type="file" id="edit-image" name="image" class="w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-500 text-white mr-4 px-6 py-2 rounded-md hover:bg-blue-600">Update</button>
                    <button type="button" class="bg-gray-400 text-white px-6 py-2 rounded-lg" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
