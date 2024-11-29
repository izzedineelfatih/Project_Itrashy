<?php
session_start();
require_once 'config.php';

// Cek apakah ada email yang akan direset passwordnya
if (!isset($_SESSION['reset_email'])) {
    header('Location: verify-email.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validasi password
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    if (empty($errors)) {
        try {
            // Update password di database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);

            // Hapus session reset email
            unset($_SESSION['reset_email']);

            // Set pesan sukses
            $_SESSION['success'] = "Password berhasil diubah! Silakan login dengan password baru.";
            
            // Redirect ke halaman login
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $errors[] = "Terjadi kesalahan saat mengubah password. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Ubah Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['THICCCBOI'],
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-white">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Left Side -->
        <div class="relative w-full md:w-1/2">
            <div class="relative w-full h-64 md:h-full p-4">
                <a href="verify-email.php" class="absolute top-8 left-10 h-4 lg:top-12 lg:left-14">
                    <img src="assets/icon/back icon.png" alt="Back" class="w-6 h-6">
                </a>
                <img src="assets/image/orang buang sampah.jpg" alt="Background" class="w-full h-full object-cover rounded-xl">
                
                <div class="absolute bottom-10 left-10 lg:bottom-20 lg:left-14">
                    <div class="flex items-center mb-2">
                        <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-8 lg:w-12">
                    </div>
                    <h1 class="md:text-2xl font-bold text-white/80 mb-2">I-Trashy.</h1>
                    <p class="text-sm text-white/80 pr-20 lg:pr-0">Solusi pengelolaan sampah untuk rumah tangga dan bisnis</p>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex-1 p-6 md:p-12 flex flex-col items-center justify-center">
            <div class="max-w-md mx-auto w-full">
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold pb-2">Ubah Password</h1>
                    <p class="text-gray-400">Masukkan password baru Anda</p>
                </div>

                <form method="POST" class="space-y-4">
                    <div class="relative">
                        <label for="password" class="text-gray-600">Password Baru</label>
                        <input type="password" id="password" name="password" 
                            placeholder="Masukkan Password Baru" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-6 pt-4 opacity-50">
                            <img src="assets/icon/eye icon.png" alt="eye" class="w-6">
                        </button>
                    </div>

                    <div class="relative">
                        <label for="confirmPassword" class="text-gray-600">Konfirmasi Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" 
                            placeholder="Ulangi Password Baru" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2">
                        <button type="button" onclick="togglePassword('confirmPassword')" class="absolute right-6 pt-4 opacity-50">
                            <img src="assets/icon/eye icon.png" alt="eye" class="w-6">
                        </button>
                    </div>

                    <div class="pt-8">
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>