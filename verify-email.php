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
                // Buat token unik
                $token = bin2hex(random_bytes(50));
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
                $stmt->execute([$email, $token]);

                // Simpan token di session
                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_email'] = $email;

                // Set pesan sukses
                $_SESSION['success'] = "Email Terdaftar. Berikut Link untuk mengubah password Anda.";
                header('Location: verify-email.php'); // Redirect untuk menampilkan notifikasi
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

        function showPopup() {
            const popup = document.getElementById('popup');
            popup.classList.remove('hidden');
        }

        function hidePopup() {
            const popup = document.getElementById('popup');
            popup.classList.add('hidden');
        }
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

                <div class="mb-8 text-center ">
                    <h2 class="text-2xl font-bold">Verifikasi Email</h2>
                    <p class="text-gray-600">Masukkan email Anda untuk menerima link reset password.</p>
                </div>

                <form action="verify-email.php" method="POST">
                    <input type="email" name="email" placeholder="Email Anda" required class="border border-gray-300 rounded p-2 w-full mb-4">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors">Kirim</button>
                </form>

                <?php if (isset($_SESSION['success'])): ?>
                    <div id="popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full text-center relative">
                            <!-- Close Button -->
                            <button onclick="hidePopup()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Title -->
                            <h3 class="text-2xl font-bold text-gray-700 mb-4">Notifikasi</h3>
                            
                            <!-- Message -->
                            <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                            
                            <!-- Link -->
                            <a href="change-password.php?token=<?php echo $_SESSION['reset_token']; ?>&email=<?php echo urlencode($_SESSION['reset_email']); ?>" 
                            class="text-blue-600 font-medium underline hover:text-blue-800">
                                Klik di sini untuk mengubah password
                            </a>
                            
                        </div>
                    </div>

                    <script>
                        showPopup();
                        <?php unset($_SESSION['success']); ?>
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>