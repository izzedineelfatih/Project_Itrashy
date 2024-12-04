<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target = "assets/image/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $stmt = $pdo->prepare("UPDATE jenis_sampah SET name = ?, price = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $price, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE jenis_sampah SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $price, $id]);
    }

    header("Location: katalog_sampah.php");
}
?>