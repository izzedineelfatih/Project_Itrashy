<?php
session_start();
require 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM jenis_sampah WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: katalog_sampah.php");
}
?>