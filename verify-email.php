<?php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($errors)) {
        try {
            // Cek apakah email ada di database
            $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Jika email ditemukan, simpan ke session dan arahkan ke halaman ubah password
                $_SESSION['reset_email'] = $user['email'];
                header('Location: change-password.php');
                exit();
            } else {
                $errors[] = "Email tidak ditemukan dalam database";
            }
        } catch (Exception $e) {
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Verifikasi Email</title>
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
                <a href="login.php" class="absolute top-8 left-10 h-4 lg:top-12 lg:left-14">
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
                    <h1 class="text-2xl font-bold pb-2">Verifikasi Email</h1>
                    <p class="text-gray-400">Masukkan email yang terdaftar untuk mengubah password</p>
                </div>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="text-gray-600">Email</label>
                        <input type="email" id="email" name="email" 
                            placeholder="Masukkan Email" required
                            class="w-full px-4 py-2 rounded-xl bg-[#f5f7fa] mt-2"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="pt-8">
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Verifikasi
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="login.php" class="text-blue-600">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>