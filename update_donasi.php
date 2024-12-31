<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];           // ID donasi yang ingin diupdate
    $title = $_POST['title'];     // Judul donasi
    $deskripsi = $_POST['deskripsi']; // Deskripsi donasi
    $collected = $_POST['collected']; // Total collected amount
    $goal = $_POST['goal'];       // Target goal
    $image = $_FILES['image']['name']; // Gambar yang diupload

    // Jika ada gambar yang diupload
    if ($image) {
        // Menentukan path untuk menyimpan gambar
        $target = "assets/image/" . basename($image);
        // Pindahkan file gambar yang diupload ke folder tujuan
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        // Query untuk update data termasuk gambar
        $stmt = $pdo->prepare("UPDATE katalog_donasi SET image = ?, title = ?, deskripsi = ?, collected = ?, goal = ? WHERE id = ?");
        $stmt->execute([$image, $title, $deskripsi, $collected, $goal, $id]);
    } else {
        // Jika tidak ada gambar yang diupload, update tanpa mengganti gambar
        $stmt = $pdo->prepare("UPDATE katalog_donasi SET title = ?, deskripsi = ?, collected = ?, goal = ? WHERE id = ?");
        $stmt->execute([$title, $deskripsi, $collected, $goal, $id]);
    }

    // Setelah berhasil, redirect ke halaman katalog donasi
    header("Location: katalog_donasi.php");
}
?>
