<?php
require 'config.php';

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageUrl = null;

    // Jika ada file gambar yang diunggah
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['image_file']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validasi file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFilePath)) {
                $imageUrl = $targetFilePath; // Simpan URL file
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO articles (title, content, image_url) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $imageUrl]);

    header('Location: katalog_edukasi.php');
    exit();
}

// Ambil data artikel untuk form edit
$article = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $article = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Article</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold mb-5"><?= isset($article) ? 'Edit' : 'Add' ?> Article</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($article['title'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
                <textarea id="content" name="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
            </div>
            <div class="mb-4">
                <label for="image_file" class="block text-gray-700 text-sm font-bold mb-2">Upload Image:</label>
                <input type="file" id="image_file" name="image_file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
            <a href="katalog_edukasi.php" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
        </form>
    </div>
</body>
</html>
