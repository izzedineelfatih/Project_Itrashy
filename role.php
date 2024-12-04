<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    
    // Validasi role
    if ($role !== 'Individu' && $role !== 'Bisnis') {
        die('Invalid role selection');
    }
    
    // Simpan role ke session untuk digunakan saat register
    $_SESSION['selected_role'] = $role;
    
    // Redirect ke halaman register
    header('Location: register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="bg-white p-4 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-6xl aspect-[16/9] bg-gray-200 rounded-3xl">
        <a href="login.php">
            <button class="p-2 pl-5 pt-6 lg:pl-10 lg:pt-8">
                <img src="assets/icon/back icon.png" alt="back" class="invert w-5 lg:w-6">
            </button>
        </a>
        
        <h1 class="text-xl lg:text-2xl md:text-3xl font-semibold text-center mt-16 lg:mt">
            Sebagai Apa Anda Mendaftar?
        </h1>
    
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-20 max-w-3xl mx-auto mt-10 lg:mt-12 p-6">
            <form method="POST" class="flex flex-col lg:flex-row gap-6 lg:gap-20 w-full">
                <!-- Individual Role Card -->
                <button type="submit" name="role" value="Individu" class="flex-1">
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg hover:bg-[#66CB9F] transition-shadow cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 flex items-center justify-center mb-6">
                                <img src="assets/image/role individu.png" alt="">
                            </div>
                            <h2 class="text-xl font-semibold mb-3">Individu</h2>
                            <p class="text-gray-600 text-sm">
                                Masyarakat/Individu yang ingin sampahnya dikelola bersama i-Trashy
                            </p>
                        </div>
                    </div>
                </button>
                
                <!-- Business Role Card -->
                <button type="submit" name="role" value="Bisnis" class="flex-1">
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg hover:bg-[#66CB9F] transition-shadow cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 flex items-center justify-center mb-6">
                                <img src="assets/image/role bisnis.png" alt="">
                            </div>
                            <h2 class="text-xl font-semibold mb-3">Bisnis</h2>
                            <p class="text-gray-600 text-sm">
                                Pemilik bisnis yang ingin berlangganan pengelolaan sampah bersama i-Trashy
                            </p>
                        </div>
                    </div>
                </button>
            </form>
        </div>
    </div>
</body>
</html>