<?php
session_start();
require 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: staff_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $deskripsi = $_POST['deskripsi']; // Contains HTML from CKEditor
        $collected = $_POST['collected'];
        $goal = $_POST['goal'];

        // Validate and upload image
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Silakan pilih gambar untuk diunggah.");
        }

        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        // Validate file extension
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        if (!in_array($image_ext, $allowed_exts)) {
            throw new Exception("Format file tidak valid. Gunakan JPG, JPEG, atau PNG.");
        }

        // Validate file size (max 2MB)
        $max_size = 2 * 1024 * 1024;
        if ($image_size > $max_size) {
            throw new Exception("Ukuran file terlalu besar. Maksimal 2MB.");
        }

        // Generate unique filename
        $new_image_name = time() . '-' . uniqid() . '.' . $image_ext;
        $target_path = "assets/image/" . $new_image_name;

        // Upload image
        if (!move_uploaded_file($image_tmp, $target_path)) {
            throw new Exception("Gagal mengupload gambar.");
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO katalog_donasi 
            (title, deskripsi, collected, goal, image, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        
        $stmt->execute([$title, $deskripsi, $collected, $goal, $new_image_name]);

        $_SESSION['success_message'] = "Donasi baru berhasil ditambahkan!";
        header("Location: katalog_donasi.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: katalog_donasi.php");
        exit();
    }
}
?>