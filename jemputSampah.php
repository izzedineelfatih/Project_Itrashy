<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<?php
require 'config.php';

// Ambil data jenis sampah dari database
$stmt = $pdo->query("SELECT * FROM jenis_sampah");
$jenis_sampah = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jemput Sampah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
<body class="bg-[#f5f6fb] font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include 'sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col min-h-screen">
            <?php include 'header.php'; ?>

            <div class="flex-1 overflow-y-auto relative">
                <div class="p-5 lg:pt-5 lg:pb-5 pt-8 pb-8 flex justify-between items-center sticky top-0 bg-[#f5f6fb] z-10">
                    <h2 class="font-semibold text-lg">Yuk, Pilih jenis sampahmu dulu</h2>
                    <button id="cartToggle" class="lg:hidden relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                </div>
                
                <div class="p-5 pt-0">
                    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
                        <div class="w-full lg:w-[600px]">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3 md:gap-4">
                            <?php 
                                foreach ($jenis_sampah as $index => $sampah) {
                                    echo '<div class="flex items-center bg-white rounded-lg shadow-md p-3 gap-3">';
                                    echo '<img src="assets/image/' . $sampah['image'] . '" alt="' . $sampah['name'] . '" class="h-16 w-16 md:h-20 md:w-20 object-cover rounded-lg">';
                                    echo '<div class="flex flex-col flex-1">';
                                    echo '<h4 class="font-semibold md:text-base">' . $sampah['name'] . '</h4>';
                                    echo '<p class="text-xs md:text-sm text-gray-500 mt-1">Rp. ' . $sampah['price'] . '/Kg</p>';
                                    echo '<button onclick="addToOrderList(' . $index . ', \'' . $sampah['name'] . '\', ' . $sampah['price'] . ', this)" id="btn-' . $index . '" class="bg-[#40916c] px-4 md:px-6 py-1.5 rounded-full mt-2 text-white text-xs md:text-sm hover:bg-[#2d724f] transition-colors">Pilih</button>';
                                    echo '</div></div>';
                                }
                                ?>
                            </div>
                        </div>

                        <div id="cartOverlay" class="fixed lg:relative top-0 right-0 h-full w-full max-w-md bg-white shadow-xl transform translate-x-full lg:translate-x-0 transition-transform duration-300 rounded-lg z-50 lg:z-0 lg:w-[370px] lg:block">
                            <div class="h-full flex flex-col">
                                <div class="p-4 border-b flex justify-between items-center lg:hidden">
                                    <h3 class="font-semibold text-lg">Order List</h3>
                                    <button id="closeCart" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>

                                <div class="flex-1 overflow-y-auto p-4">
                                    <div class="bg-gray-100 rounded-lg p-4 mb-4">
                                        <h3 class="font-semibold text-lg mb-5">List Sampah</h3>
                                        <div id="orderListItems" class="space-y-3"></div>
                                        <div id="emptyOrderMessage" class="text-sm text-gray-600 mb-4">Belum ada jenis sampah yang kamu masukin nih</div>
                                        <div class="border-t border-gray-300 mt-4 pt-4 space-y-2">
                                            <div class="flex justify-between text-sm text-gray-700">
                                                <span>Admin</span>
                                                <span>20%</span>
                                            </div>
                                            <div class="flex justify-between text-sm font-semibold">
                                                <span>Total</span>
                                                <span id="totalPrice">Rp. 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block font-semibold mb-2">Lokasi Penjemputan</label>
                                            <input type="text" id="location" placeholder="Tentukan lokasimu" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none"/>
                                        </div>
                                        
                                        <div>
                                            <label class="block font-semibold mb-2">Tanggal Penjemputan</label>
                                            <input type="date" id="pickup_date" class="w-full border mb-5 border-gray-300 rounded-lg p-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"/>

                                            <label class="block font-semibold mb-2">Waktu Penjemputan</label>
                                            <select id="pickup_time" class="w-full border border-gray-300 rounded-lg p-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="" disabled selected>Pilih waktu</option>
                                                <option value="08:00-10:00">08:00-10:00</option>
                                                <option value="10:00-12:00">10:00-12:00</option>
                                                <option value="12:00-14:00">12:00-14:00</option>
                                                <option value="14:00-16:00">14:00-16:00</option>
                                                <option value="16:00-18:00">16:00-18:00</option>
                                                <option value="18:00-23:59">18:00-23:59</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <button id="saveOrderButton" class="w-full bg-blue-500 text-white py-2.5 rounded-full hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">Cari Picker</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'footer.php'; ?>
            </div>
        </div>
    </div>


    <script>
        let orderItems = [];
        const adminFeePercentage = 0.20;

        document.addEventListener('DOMContentLoaded', function() {
            initializeMenu();
            setMinDate();
            disablePastTimes();
        });

        function initializeMenu() {
            const menuToggle = document.getElementById('menuToggle');
            const menuClose = document.getElementById('menuClose');
            const menu = document.getElementById('menu');
            const navLinks = document.querySelectorAll('.nav-link');
            const pageTitle = document.getElementById('pageTitle');
            
            // Dapatkan nama halaman dari URL saat ini
            const currentPage = window.location.pathname.split('/').pop().replace('.php', '');

            // Mobile menu toggles
            if (menuToggle && menuClose && menu) {
                menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
                menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
            }

            // Set active menu item dan update page title
            navLinks.forEach(link => {
                const href = link.getAttribute('href').replace('.php', '');
                const menuText = link.querySelector('span').textContent;
                
                if (href.includes(currentPage)) {
                    link.classList.add('active');
                    // Update page title sesuai menu yang aktif
                    if (pageTitle) {
                        if (currentPage === 'dashboard') {
                            pageTitle.textContent = `Halo, ${pageTitle.dataset.username}👋`;
                        } else {
                            pageTitle.textContent = menuText;
                        }
                    }
                }
            });
        }

        // Event listener untuk tombol save order
        document.getElementById('saveOrderButton').addEventListener('click', async function() {
            const location = document.getElementById('location').value;
            const pickupDate = document.getElementById('pickup_date').value;
            const pickupTime = document.getElementById('pickup_time').value;

            // Validasi input
            if (!location || !pickupDate || !pickupTime) {
                alert('Mohon lengkapi semua data yang diperlukan');
                return;
            }

            if (orderItems.length === 0) {
                alert('Mohon pilih minimal satu jenis sampah');
                return;
            }

            const subtotal = orderItems.reduce((total, item) => total + item.totalPrice, 0);
            const adminFee = subtotal * adminFeePercentage;
            const total = subtotal - adminFee;

            const orderData = {
                items: orderItems,
                location: location,
                pickup_date: pickupDate,
                pickup_time: pickupTime,
                total: total,
                admin_fee: adminFee
            };

            try {
                const response = await fetch('save_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'success.php';
                } else {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });

        function addToOrderList(index, name, price, button) {
            const existingItemIndex = orderItems.findIndex(item => item.name === name);
            
            if (existingItemIndex > -1) {
                // Jika item sudah ada, hapus item
                orderItems.splice(existingItemIndex, 1);
                // Reset tampilan button
                button.classList.remove('bg-white', 'border', 'border-blue-500', 'text-black', 'hover:bg-gray-50');
                button.classList.add('bg-[#40916c]', 'text-white', 'hover:bg-[#2d724f]');
                button.textContent = 'Pilih';
            } else {
                // Jika item baru, tambahkan dengan quantity default 2 kg
                orderItems.push({
                    name: name,
                    price: price,
                    quantity: 2,
                    totalPrice: price * 2
                });
                // Ubah tampilan button
                button.classList.remove('bg-[#40916c]', 'text-white', 'hover:bg-[#2d724f]');
                button.classList.add('bg-white', 'border', 'border-blue-500', 'text-black', 'hover:bg-gray-50');
                button.textContent = 'Dipilih';
            }

            updateOrderList();
            updateCartCount();
        }

        function updateQuantity(index, change) {
            const newQuantity = orderItems[index].quantity + (change * 0.5); // Ubah increment/decrement menjadi 0.5
            
            if (newQuantity < 2) { // Cek jika kurang dari batas minimal 2 kg
                if (change < 0) { // Jika user mencoba mengurangi di bawah 2 kg
                    alert('Minimal berat sampah adalah 2 kg');
                }
                return;
            }
            
            orderItems[index].quantity = newQuantity;
            orderItems[index].totalPrice = newQuantity * orderItems[index].price;
            
            updateOrderList();
            updateCartCount();
        }

        function updateOrderList() {
            const orderListElement = document.getElementById('orderListItems');
            const emptyMessageElement = document.getElementById('emptyOrderMessage');
            const totalPriceElement = document.getElementById('totalPrice');
            
            orderListElement.innerHTML = '';
            
            if (orderItems.length === 0) {
                emptyMessageElement.style.display = 'block';
            } else {
                emptyMessageElement.style.display = 'none';
                
                orderItems.forEach((item, index) => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'flex items-center justify-between bg-white p-3 rounded-lg shadow-sm';
                    itemElement.innerHTML = 
                        `<div class="flex-1">
                            <h4 class="font-medium text-sm">${item.name}</h4>
                            <p class="text-xs text-gray-500">Rp. ${item.price}/Kg</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(${index}, -1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-6 h-6 flex items-center justify-center text-gray-600">-</button>
                            <span class="text-sm w-12 text-center">${item.quantity.toFixed(1)} kg</span>
                            <button onclick="updateQuantity(${index}, 1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-6 h-6 flex items-center justify-center text-gray-600">+</button>
                            <button onclick="removeItem(${index})" class="ml-2 text-red-500 hover:text-red-700"><img src="assets/icon/trash.png" alt="" class="w-5 opacity-80"></button>
                        </div>`;
                    orderListElement.appendChild(itemElement);
                });
            }
            
            const subtotal = orderItems.reduce((total, item) => total + item.totalPrice, 0);
            const adminFee = subtotal * adminFeePercentage;
            const total = subtotal - adminFee;
            
            totalPriceElement.textContent = `Rp. ${Math.round(total).toLocaleString()}`;
        }

        function removeItem(index) {
            const item = orderItems[index];
            const button = document.querySelector(`button[onclick*="${item.name}"]`);
            if (button) {
                button.classList.remove('bg-white', 'border', 'border-blue-500', 'text-black', 'hover:bg-gray-50');
                button.classList.add('bg-[#40916c]', 'text-white', 'hover:bg-[#2d724f]');
                button.textContent = 'Pilih';
            }
            
            orderItems.splice(index, 1);
            updateOrderList();
            updateCartCount();
        }

        function updateCartCount() {
            const cartCount = document.getElementById('cartCount');
            const numberOfWasteTypes = orderItems.length;
            cartCount.textContent = numberOfWasteTypes;
            cartCount.style.display = numberOfWasteTypes > 0 ? 'flex' : 'none';
        }

        // Fungsi untuk manajemen waktu penjemputan
        function timeToMinutes(timeStr) {
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function setMinDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            const minDate = `${year}-${month}-${day}`;
            document.getElementById("pickup_date").setAttribute("min", minDate);
        }

        function disablePastTimes() {
            const pickupDate = document.getElementById("pickup_date").value;
            const pickupTime = document.getElementById("pickup_time");
            const options = pickupTime.querySelectorAll("option:not([value=''])"); // Abaikan placeholder
            
            if (!pickupDate) {
                return;
            }

            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();
            const selectedDate = new Date(pickupDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);

            // Jika tanggal yang dipilih adalah hari ini
            if (selectedDate.getTime() === today.getTime()) {
                options.forEach(option => {
                    if (!option.value) return; // Skip placeholder option
                    const [startTime, endTime] = option.value.split('-');
                    const startMinutes = timeToMinutes(startTime);
                    const endMinutes = timeToMinutes(endTime);

                    // Disable opsi jika waktu akhir sudah lewat dari waktu sekarang
                    if (endMinutes < currentMinutes) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            } else if (selectedDate < today) {
                // Disable semua opsi untuk tanggal yang sudah lewat
                options.forEach(option => {
                    option.disabled = true;
                });
            } else {
                // Enable semua opsi untuk tanggal yang akan datang
                options.forEach(option => {
                    option.disabled = false;
                });
            }
        }

        // Cart toggle untuk mobile
        document.getElementById('cartToggle')?.addEventListener('click', function() {
            const cartOverlay = document.getElementById('cartOverlay');
            cartOverlay.classList.remove('translate-x-full');
        });

        document.getElementById('closeCart')?.addEventListener('click', function() {
            const cartOverlay = document.getElementById('cartOverlay');
            cartOverlay.classList.add('translate-x-full');
        });

        // Initialize saat halaman dimuat
        window.onload = function() {
            setMinDate();
            disablePastTimes();
        };

        // Event listeners untuk date picker
        document.getElementById("pickup_date").addEventListener("change", disablePastTimes);

        // Update setiap menit
        setInterval(disablePastTimes, 60000);
    </script>
</body>
</html>