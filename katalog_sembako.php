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
        // Fungsi untuk membuka modal tambah sembako
        function openAddModal() {
            document.getElementById('add-modal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal tambah sembako
        function closeAddModal() {
            document.getElementById('add-modal').classList.add('hidden');
        }

        // Fungsi untuk membuka modal edit sembako
        function openEditModal(id, title, description, points, image) {
            document.getElementById('edit-modal').classList.remove('hidden');
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-points').value = points;
            document.getElementById('current-image').src = "assets/image/" + image;
        }

        // Fungsi untuk menutup modal edit sembako
        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }
    </script>
</head>
<body class="flex bg-gray-100">
<?php include 'admin_sidebar.php'; ?>

    <div class="container mx-auto p-5">
        <h1 class="text-2xl font-bold mb-5">Katalog Sembako</h1>
        <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg mb-5">Tambah Sembako</button>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2">Image</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Points</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sembakos as $sembako): ?>
                        <tr>
                            <td class="border px-4 py-2"><img src="assets/image/<?= $sembako['image'] ?>" alt="<?= $sembako['title'] ?>" class="w-12 h-12"></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($sembako['title']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($sembako['description']) ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($sembako['points']) ?></td>
                            <td class="border px-4 py-2">
                                <button class="text-blue-500" onclick="openEditModal('<?= $sembako['id'] ?>', '<?= htmlspecialchars($sembako['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($sembako['description'], ENT_QUOTES) ?>', '<?= $sembako['points'] ?>', '<?= $sembako['image'] ?>')">Edit</button> |
                                <a href="delete_sembako.php?id=<?= $sembako['id'] ?>" class="text-red-500" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Sembako -->
    <div id="add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Tambah Sembako</h2>
            <form method="POST" action="add_sembako.php" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="title">Title</label>
                    <input type="text" id="title" name="title" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="description">Description</label>
                    <textarea id="description" name="description" class="w-full border-gray-300 rounded-lg shadow-sm" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="points">Points</label>
                    <input type="number" id="points" name="points" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="image">Upload Image</label>
                    <input type="file" id="image" name="image" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Sembako -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Edit Sembako</h2>
            <form method="POST" action="update_sembako.php" enctype="multipart/form-data">
                <input type="hidden" id="edit-id" name="id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-title">Title</label>
                    <input type="text" id="edit-title" name="title" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-description">Description</label>
                    <textarea id="edit-description" name="description" class="w-full border-gray-300 rounded-lg shadow-sm" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="edit-points">Points</label>
                    <input type="number" id="edit-points" name="points" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Current Image</label>
                    <img id="current-image" src="" alt="Current Image" class="w-24 h-24 rounded-lg mb-2">
                    <label class="block text-gray-700 mb-2" for="edit-image">Upload New Image</label>
                    <input type="file" id="edit-image" name="image" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg mr-2" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
