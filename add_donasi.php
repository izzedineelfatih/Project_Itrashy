<?php
session_start();
require 'config.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $title = $_POST['title'];
    $deskripsi = $_POST['deskripsi'];
    $collected = $_POST['collected'];
    $goal = $_POST['goal'];
    $image = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];

    // Validasi file gambar
    $maxSize = 2 * 1024 * 1024; // 2MB
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $imageExtension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if (!in_array($imageExtension, $allowedExtensions)) {
        echo "Ekstensi file tidak valid. Gunakan jpg, jpeg, atau png.";
        exit();
    }

    if ($imageSize > $maxSize) {
        echo "Ukuran file terlalu besar. Maksimal 2MB.";
        exit();
    }

    // Generate nama file unik
    $newImageName = time() . '-' . uniqid() . '.' . $imageExtension;
    $target = "assets/image/" . $newImageName;

    // Proses upload gambar
    if (move_uploaded_file($imageTmp, $target)) {
        // Siapkan query untuk menyimpan data ke database
        $query = "INSERT INTO katalog_donasi (image, title, deskripsi, collected, goal, created_at) 
                  VALUES (:image, :title, :deskripsi, :collected, :goal, NOW())";

        $stmt = $pdo->prepare($query);

        try {
            $stmt->execute([
                ':image' => $newImageName,
                ':title' => $title,
                ':deskripsi' => $deskripsi,
                ':collected' => $collected,
                ':goal' => $goal,
            ]);

            // Redirect ke halaman katalog
            header("Location: katalog_donasi.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Gagal mengupload gambar.";
    }
} else {
    echo "Akses tidak valid.";
}
?>
