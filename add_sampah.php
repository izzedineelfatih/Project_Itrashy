<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $target = "assets/image/" . basename($image);

    // Upload file gambar
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO jenis_sampah (name, price, image) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $image]);
        header("Location: katalog_sampah.php");
    } else {
        echo "Gagal mengupload gambar.";
    }
}
?>