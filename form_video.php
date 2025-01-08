<?php
require 'config.php'; // Menghubungkan ke file konfigurasi database

// Proses penyimpanan data video (add or edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $title = $_POST['title'];
    $videoUrl = null;  // Inisialisasi variabel URL video

    // Jika ada file video yang diunggah
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';  // Folder tempat menyimpan video
        $fileName = basename($_FILES['video_file']['name']);  // Mendapatkan nama file
        $targetFilePath = $uploadDir . $fileName;  // Path lengkap file
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));  // Mendapatkan ekstensi file

        // Validasi jenis file
        $allowedTypes = ['mp4', 'avi', 'mov', 'mkv'];
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $targetFilePath)) {
                $videoUrl = $targetFilePath; // Simpan URL file video
            } else {
                echo "Error uploading file.";  // Jika gagal mengunggah
            }
        } else {
            echo "Invalid file type. Only MP4, AVI, MOV, and MKV files are allowed.";  // Jika jenis file tidak sesuai
        }
    }

    // Simpan data video ke database
    $stmt = $pdo->prepare("INSERT INTO videos (title, video_url,content) VALUES (?, ?,?)");
    $stmt->execute([$title, $videoUrl,$content]);  // Menyimpan judul dan URL video

    // Redirect ke halaman admin setelah berhasil
    header('Location: katalog_edukasi.php');
    exit();
}

// Ambil data video untuk form edit jika ada ID video dalam URL
$video = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $video = $stmt->fetch();  // Ambil data video yang akan diedit
}
?>

<!-- Form untuk menambah/edit video -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Video</title>
    <script src="https://cdn.tailwindcss.com"></script>  <!-- Menyertakan TailwindCSS -->
</head>
<body class="bg-gray-100 font-sans">

    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold mb-5"><?= isset($video) ? 'Edit' : 'Add' ?> Video</h1>
        <!-- Form untuk menambah atau mengedit video -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($video['title'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
                <textarea id="content" name="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required><?= htmlspecialchars($video['content'] ?? '') ?></textarea>
            </div>
            <div class="mb-4">
                <label for="video_file" class="block text-gray-700 text-sm font-bold mb-2">Upload Video:</label>
                <input type="file" id="video_file" name="video_file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
            <a href="katalog_edukasi" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
        </form>
    </div>

</body>
</html>
