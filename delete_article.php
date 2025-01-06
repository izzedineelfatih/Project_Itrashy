<?php
session_start();

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: staff_login.php");
    exit();
}

// Masukkan konfigurasi database
require 'config.php';

// Validasi parameter GET
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Invalid request");
}

$type = $_GET['type'];
$id = (int) $_GET['id']; // Pastikan ID selalu integer

// Tentukan tabel berdasarkan jenis (type)
$table = '';
switch ($type) {
    case 'article':
        $table = 'articles';
        break;
    case 'video':
        $table = 'videos';
        break;
    case 'event':
        $table = 'events';
        break;
    default:
        die("Invalid type");
}

// Hapus data dari tabel
try {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect setelah berhasil
    header("Location: katalog_edukasi.php");
    exit();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
