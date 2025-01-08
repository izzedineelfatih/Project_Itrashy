<?php
session_start();
require 'config.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'admin') {
    header("Location: staff_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $deskripsi = $_POST['deskripsi']; // Now contains HTML from CKEditor
    $collected = $_POST['collected'];
    $goal = $_POST['goal'];

    try {
        if (!empty($_FILES['image']['name'])) {
            // Get old image to delete
            $stmt = $pdo->prepare("SELECT image FROM katalog_donasi WHERE id = ?");
            $stmt->execute([$id]);
            $old_image = $stmt->fetchColumn();

            // Upload new image
            $image = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($image_ext, $allowed_exts)) {
                throw new Exception("Format file tidak valid. Gunakan JPG, JPEG, atau PNG.");
            }

            if ($_FILES['image']['size'] > $max_size) {
                throw new Exception("Ukuran file terlalu besar. Maksimal 2MB.");
            }

            $new_image_name = time() . '-' . uniqid() . '.' . $image_ext;
            $target_path = "assets/image/" . $new_image_name;

            if (move_uploaded_file($image_tmp, $target_path)) {
                // Delete old image if exists
                if ($old_image && file_exists("assets/image/" . $old_image)) {
                    unlink("assets/image/" . $old_image);
                }

                // Update database with new image
                $stmt = $pdo->prepare("UPDATE katalog_donasi SET 
                    title = ?, 
                    deskripsi = ?, 
                    collected = ?, 
                    goal = ?,
                    image = ?,
                    updated_at = NOW()
                    WHERE id = ?");
                $stmt->execute([$title, $deskripsi, $collected, $goal, $new_image_name, $id]);
            } else {
                throw new Exception("Gagal mengupload gambar.");
            }
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("UPDATE katalog_donasi SET 
                title = ?, 
                deskripsi = ?, 
                collected = ?, 
                goal = ?,
                updated_at = NOW()
                WHERE id = ?");
            $stmt->execute([$title, $deskripsi, $collected, $goal, $id]);
        }

        $_SESSION['success_message'] = "Data donasi berhasil diperbarui!";
        header("Location: katalog_donasi.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: katalog_donasi.php");
        exit();
    }
}
?>