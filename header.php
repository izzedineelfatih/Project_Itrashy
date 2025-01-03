<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<header class="sticky top-0 bg-white shadow-md z-40">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center space-x-4">
            <button id="menuToggle" class="lg:hidden">
                <svg class="w-6 h-6" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <h2 id="pageTitle" class="text-lg font-semibold" data-username="<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>">
                <!-- Title akan diupdate oleh JavaScript -->
            </h2>
        </div>
        
        <div class="flex items-center space-x-4 relative">
            <!-- Notifikasi -->
            <div class="relative">
                <button id="notifIcon" class="focus:outline-none">
                    <img src="assets/icon/notifikasi.png" alt="Notifications" class="w-8 h-8">
                    <!-- Badge -->
                    <span id="notifBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                        3
                    </span>
                </button>

                <!-- Dropdown Notifikasi -->
                <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                    <ul id="notifList" class="divide-y divide-gray-200">
                        <!-- Notifikasi: Jadwal Jemput Sampah -->
                        <li class="p-4 hover:bg-gray-100">
                            <div class="bg-yellow-400 text-white p-3 rounded-full inline-block">
                                📦
                            </div>
                            <div class="ml-4 inline-block align-top">
                                <h3 class="text-lg font-semibold text-gray-800">Jadwal Jemput Sampah</h3>
                                <p class="text-gray-500 text-sm">Senin, 7 Maret 2024</p>
                                <p class="text-gray-600 mt-2">Ada jadwal jemput sampah nih jam 07:00 - 09:00. Yuk, persiapkan sampahmu, itrashy picker sudah siap menuju tempatmu.</p>
                                <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Lacak Trashy Picker
                                </button>
                            </div>
                        </li>
                        <!-- Notifikasi: Bayar Tagihan Token Listrik -->
                        <li class="p-4 hover:bg-gray-100">
                            <div class="bg-yellow-500 text-white p-3 rounded-full inline-block">
                                ⚡
                            </div>
                            <div class="ml-4 inline-block align-top">
                                <h3 class="text-lg font-semibold text-gray-800">Bayar Tagihan Token Listrik</h3>
                                <p class="text-gray-500 text-sm">Selasa, 20 Februari 2024</p>
                                <p class="text-gray-600 mt-2">Hore, pembayaran berhasil. Terus kumpulkan saldo dengan mengumpulkan sampahmu dan nikmati benefitnya.</p>
                                <div class="mt-3 bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center justify-between">
                                    <span>1234-5678-9012-3456-7890</span>
                                    <button class="text-blue-500 hover:text-blue-700">📋</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="w-px h-10 bg-black"></div>
            
            <!-- Profile -->
            <a href="profile.php">
    <img src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'assets/image/profile.jpg'); ?>" alt="Profile" class="w-10 h-10 rounded-full">
</a>

        </div>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
    const notifIcon = document.getElementById("notifIcon");
    const notifDropdown = document.getElementById("notifDropdown");
    const notifBadge = document.getElementById("notifBadge");
    const notifList = document.getElementById("notifList");

    // Fungsi untuk mengupdate badge berdasarkan jumlah notifikasi
    const updateNotifBadge = () => {
        const notifCount = notifList.querySelectorAll("li").length; // Hitung elemen <li>
        if (notifCount > 0) {
            notifBadge.textContent = notifCount; // Set jumlah notifikasi ke badge
            notifBadge.classList.remove("hidden"); // Tampilkan badge jika ada notifikasi
        } else {
            notifBadge.classList.add("hidden"); // Sembunyikan badge jika tidak ada notifikasi
        }
    };

    // Toggle Dropdown
    notifIcon.addEventListener("click", (event) => {
        event.stopPropagation();
        notifDropdown.classList.toggle("hidden");
        // Sembunyikan badge hanya jika dropdown dibuka
        if (!notifDropdown.classList.contains("hidden")) {
            notifBadge.classList.add("hidden");
        }
    });

    // Sembunyikan dropdown jika klik di luar
    document.addEventListener("click", () => {
        notifDropdown.classList.add("hidden");
    });

    // Panggil fungsi untuk mengupdate badge saat halaman dimuat
    updateNotifBadge();
});

    </script>
</header>
