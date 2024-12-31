<?php
session_start();
require 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM katalog_donasi WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: katalog_donasi.php");
}
?>