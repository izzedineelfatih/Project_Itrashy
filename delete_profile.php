<?php
// delete.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "DELETE FROM profiles WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);

// Hapus session dan redirect ke halaman login setelah profil dihapus
session_destroy();
header('Location: login.php');
exit();
?>
