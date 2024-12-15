<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageSize = $_FILES['image']['size'];

    // Menentukan ukuran maksimal file (misalnya 2MB = 2 * 1024 * 1024 bytes)
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    // Menentukan ekstensi yang diperbolehkan
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    // Mendapatkan ekstensi file gambar
    $imageExtension = pathinfo($image, PATHINFO_EXTENSION);
    
    // Memeriksa apakah ekstensi file valid
    if (!in_array(strtolower($imageExtension), $allowedExtensions)) {
        echo "Ekstensi file tidak diizinkan. Gunakan jpg, jpeg, atau png.";
        exit;
    }

    // Memeriksa apakah ukuran file lebih besar dari batas maksimal
    if ($imageSize > $maxSize) {
        echo "Ukuran file terlalu besar. Maksimal ukuran file adalah 2MB.";
        exit;
    }

    // Membuat nama file unik dengan menambahkan timestamp atau uniqid()
    $newImageName = time() . '-' . uniqid() . '.' . $imageExtension;

    // Menentukan target path untuk gambar
    $target = "assets/image/" . $newImageName;

    // Mengupload file gambar
    if (move_uploaded_file($imageTmp, $target)) {
        // Menyimpan data ke database
        $stmt = $pdo->prepare("INSERT INTO jenis_sampah (name, price, image) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $newImageName]);
        
        // Redirect ke halaman katalog
        header("Location: katalog_sampah.php");
    } else {
        echo "Gagal mengupload gambar.";
    }
}
?>
