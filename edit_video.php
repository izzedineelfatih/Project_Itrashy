<?php
require 'config.php'; // Menghubungkan dengan file konfigurasi database

// Ambil data video untuk form edit jika ada ID video dalam URL
$video = null;
if (isset($_GET['id'])) {
    $video_id = $_GET['id'];
    
    // Ambil data video dari database berdasarkan ID
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(); // Ambil data video yang akan diedit
}

// Proses pengeditan video
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $title = $_POST['title'];
    $videoUrl = $video['video_url'];  // Simpan URL lama jika tidak ada file baru yang diunggah

    // Jika ada file video yang diunggah
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/videos/';  // Folder tempat menyimpan video
        $fileName = basename($_FILES['video_file']['name']);  // Nama file video
        $targetFilePath = $uploadDir . $fileName;  // Path lengkap file
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));  // Ekstensi file

        // Validasi file video
        $allowedTypes = ['mp4', 'avi', 'mov', 'mkv'];
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file video ke folder tujuan
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $targetFilePath)) {
                $videoUrl = $targetFilePath; // Update URL file video
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Only MP4, AVI, MOV, and MKV files are allowed.";
        }
    }

    // Update data video di database
    $stmt = $pdo->prepare("UPDATE videos SET title = ?, video_url = ? WHERE id = ?");
    $stmt->execute([$title, $videoUrl, $video_id]);

    // Redirect setelah berhasil mengupdate
    header('Location: katalog_edukasi.php');
    exit();
}
?>

<!-- HTML Form untuk mengedit video -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold mb-5">Edit Video</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($video['title'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            
            <div class="mb-4">
                <label for="video_file" class="block text-gray-700 text-sm font-bold mb-2">Upload New Video:</label>
                <input type="file" id="video_file" name="video_file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                <small class="text-gray-500">Current video: <?= htmlspecialchars($video['video_url']) ?></small>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
            <a href="katalog_edukasi.php" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
        </form>
    </div>

</body>
</html>
