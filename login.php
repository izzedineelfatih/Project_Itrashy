<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-Trashy - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Left Side -->
        <div class="flex-1 bg-cover bg-center relative text-white p-8" style="background-image: url('assets/image/orang buang sampah.jpg')">
            <a href="#" class="absolute top-8 left-8">
                <img src="assets/icon/back icon.png" alt="Back" class="w-6 h-6">
            </a>
            <div class="absolute bottom-20 max-w-xs">
                <img src="assets/image/Logo Itrashy.png" alt="I-Trashy Logo" class="w-16 h-20 mb-4">
                <h1 class="text-2xl font-bold">I-Trashy.</h1>
                <p class="text-sm font-light leading-relaxed">Solusi pengelolaan sampah untuk rumah tangga dan bisnis</p>
            </div>
        </div>
        <!-- Right Side -->
        <div class="flex-1 bg-gray-50 flex flex-col justify-center p-8">
            <div class="flex justify-evenly mb-8">
                <a href="registrasi.php" class="bg-white text-gray-500 px-8 py-2 rounded-lg">Daftar</a>
                <a href="login.php" class="bg-blue-600 text-white px-8 py-2 rounded-lg">Login</a>
            </div>            
            <div class="max-w-md mx-auto">
                <h2 class="text-2xl font-bold mb-4">Welcome Back!<br>
                    <span class="text-base font-normal">Yuk, kelola sampahmu bersama kami</span>
                </h2>
                <form>
                    <div class="mb-4">
                        <input type="email" placeholder="Email" required class="w-full px-4 py-2 rounded-lg border border-gray-300">
                    </div>
                    <div class="mb-4 relative">
                        <input type="password" placeholder="Password" required class="w-full px-4 py-2 rounded-lg border border-gray-300">
                        <img src="assets/icon/eye icon.png" alt="Toggle Password" class="absolute top-1/2 right-4 w-5 h-5 transform -translate-y-1/2 cursor-pointer">
                    </div>
                    <div class="text-right mb-4">
                        <a href="LupaPassword.html" class="text-sm text-blue-500">Lupa password?</a>
                    </div>
                    <div class="flex items-center space-x-2 mb-4">
                        <input type="checkbox" required>
                        <span class="text-sm">Saya setuju untuk</span>
                        <a href="#" class="text-sm text-blue-500">Ketentuan Layanan</a>
                        <span class="text-sm">&</span>
                        <a href="#" class="text-sm text-blue-500">Kebijakan Privasi</a>
                    </div>
                    <button type="submit" formaction="index.html" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>