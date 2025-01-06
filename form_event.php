<?php
require 'config.php';

// Ambil data event untuk form edit jika ada ID event dalam URL
$event = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch();
}

// Proses simpan data setelah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $eventImageUrl = $event['image_url']; // Default image from the database

    // Jika ada file gambar yang diunggah
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/gambar/';
        $fileName = basename($_FILES['event_image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validasi file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetFilePath)) {
                $eventImageUrl = $targetFilePath; // Simpan URL file
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Update data ke database
    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$title, $description, $eventImageUrl, $_POST['id']]);

    header('Location: admin_panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold mb-5">Edit Event</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($event['id'] ?? '') ?>">

            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($event['title'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="event_image" class="block text-gray-700 text-sm font-bold mb-2">Upload Event Image:</label>
                <input type="file" id="event_image" name="event_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                
                <?php if (isset($event['image_url']) && $event['image_url']): ?>
                    <div class="mt-2">
                        <small class="text-gray-500">Current Image:</small>
                        <br>
                        <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="Current Event Image" class="w-32 h-32 object-cover mt-2">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
            <a href="katalog_edukasi.php" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
        </form>
    </div>
</body>
</html>
