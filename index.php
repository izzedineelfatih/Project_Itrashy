<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard I-Trashy</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="bg-white w-60 flex flex-col justify-between p-5 shadow-lg">
    <div>
      <div class="flex items-center space-x-3 mb-8">
        <img src="assets/image/Logo Itrashy.png" alt="logo Itrashy" class="w-10 h-12">
        <h2 class="text-xl font-light">I-Trashy</h2>
      </div>
      <nav>
        <a href="index.html" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/dashboard.png" alt="Dashboard Icon" class="inline w-5 h-5 mr-3"> Dashboard
        </a>
        <a href="Jemput.html" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/jemput.png" alt="Jemput Sampah Icon" class="inline w-5 h-5 mr-3"> Jemput Sampah
        </a>
        <a href="tukarPoin.html" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/poin.png" alt="TrashPay Icon" class="inline w-5 h-5 mr-3"> Tukar Poin
        </a>
        <a href="edukasi.html" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/edukasi.png" alt="Edukasi Icon" class="inline w-5 h-5 mr-3"> Edukasi
        </a>
        <a href="#" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/pencapaian.png" alt="Pencapaian Icon" class="inline w-5 h-5 mr-3"> Pencapaian
        </a>
        <a href="#" class="block py-2 px-4 mb-2 rounded-lg text-gray-700 hover:bg-blue-100 hover:font-semibold">
          <img src="assets/icon/riwayat.png" alt="Riwayat Icon" class="inline w-5 h-5 mr-3"> Riwayat
        </a>
      </nav>
    </div>
    <div>
      <a href="#" class="flex items-center py-2 px-4 mb-2 text-gray-700 hover:text-blue-600">
        <img src="assets/icon/settings.png" alt="Pengaturan Icon" class="w-5 h-5 mr-3"> Pengaturan
      </a>
      <a href="#" class="flex items-center py-2 px-4 text-gray-700 hover:text-red-600">
        <img src="assets/icon/log-out.png" alt="Keluar Icon" class="w-5 h-5 mr-3"> Keluar
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <!-- Slider -->
    <div class="relative overflow-hidden rounded-lg">
      <div class="flex transition-transform duration-700">
        <div class="w-full">
          <img src="assets/image/poster4.png" alt="Slide 1" class="w-full">
          <button class="absolute bottom-5 left-1/2 transform -translate-x-1/2 bg-white py-2 px-4 rounded-full shadow-md">Jemput Sampah</button>
        </div>
        <div class="w-full">
          <img src="assets/image/poster1.png" alt="Slide 2" class="w-full">
        </div>
        <div class="w-full">
          <img src="assets/image/poster3.png" alt="Slide 3" class="w-full">
        </div>
      </div>
    </div>

    <!-- Balance Section -->
    <section class="bg-gradient-to-l from-green-400 to-pink-200 rounded-lg p-6 mt-6 shadow-md">
      <div class="flex justify-around items-center">
        <div class="flex items-center space-x-2">
          <img src="assets/icon/poin logo.png" alt="Poin Logo" class="w-12">
          <h2 class="text-2xl font-bold text-gray-700">Rp 50.000</h2>
        </div>
        <div class="flex space-x-4">
          <div class="text-center">
            <button class="bg-gray-200 p-3 rounded-lg">
              <img src="assets/icon/transfer.png" alt="Transfer Icon" class="w-6">
            </button>
            <p class="text-sm mt-2">Transfer</p>
          </div>
          <div class="text-center">
            <button class="bg-gray-200 p-3 rounded-lg">
              <img src="assets/icon/tagihan.png" alt="Tagihan Icon" class="w-6">
            </button>
            <p class="text-sm mt-2">Tagihan</p>
          </div>
          <div class="text-center">
            <button class="bg-gray-200 p-3 rounded-lg">
              <img src="assets/icon/donasi.png" alt="Donasi Icon" class="w-6">
            </button>
            <p class="text-sm mt-2">Donasi</p>
          </div>
        </div>
      </div>
    </section>

    <!-- News Section -->
    <section class="mt-6">
      <h3 class="text-lg font-semibold mb-4">Terbaru</h3>
      <div class="flex space-x-4 overflow-x-auto">
        <div class="min-w-[200px] bg-white rounded-lg p-4 shadow-md">
          <img src="assets/image/gambar2.png" alt="News 1" class="w-full rounded-lg mb-4">
          <h4 class="font-bold text-sm">Belajar Mengelola Sampah Sejak Dini</h4>
          <p class="text-sm text-gray-500">Membangun generasi peduli lingkungan...</p>
        </div>
        <div class="min-w-[200px] bg-white rounded-lg p-4 shadow-md">
          <img src="assets/image/gambar1.png" alt="News 2" class="w-full rounded-lg mb-4">
          <h4 class="font-bold text-sm">Rusaknya Pantai Akibat Sampah</h4>
          <p class="text-sm text-gray-500">Dampak lingkungan yang mengkhawatirkan...</p>
        </div>
      </div>
    </section>
  </main>

  <!-- Right Sidebar -->
  <aside class="bg-white w-72 p-6 shadow-lg">
    <!-- Profile -->
    <div class="flex items-center justify-between mb-6">
      <img src="assets/icon/notifikasi.png" alt="Notification" class="w-8 h-8">
      <div class="flex items-center space-x-4">
        <img src="assets/image/profile.jpg" alt="Profile" class="w-12 h-12 rounded-full">
        <p class="text-sm font-semibold">M Abizar Atalaric</p>
      </div>
    </div>
    <!-- Schedule Card -->
    <div class="bg-gray-100 p-4 rounded-lg mb-4">
      <h4 class="font-bold text-sm">Jadwal Penjemputan</h4>
      <p class="text-center text-gray-500 text-sm mt-2">Belum ada penjemputan...</p>
    </div>
    <!-- Waste Collected -->
    <div class="bg-gray-100 p-4 rounded-lg">
      <h4 class="font-bold text-sm">Sampah Terkumpul</h4>
      <canvas id="wasteChart" class="mt-4"></canvas>
    </div>
  </aside>

  <script src="script.js"></script>
</body>
</html>