<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampilan Jadwal</title>
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
<style>
  .nav-link {
      transition: color 0.3s ease;
  }

  .nav-link:hover {
      color: #3b82f6;
  }

  .nav-link.active {
      background-color: #3968DA;
      color: white;
      margin-left: -20px;
      padding-left: 30px; 
      margin-right: 10px;
      padding-top: 10px;
      padding-bottom: 10px;
      font-weight: 500;
      border-radius: 0 15px 15px 0;
  }

  html, body {
      height: 100%;
      margin: 0;
      padding: 0;
  }
</style>
</head>
<body class="bg-[#f5f6fb] font-sans">
<!-- Main Layout Container -->
<div class="flex h-screen overflow-hidden">
  <!-- Sidebar -->
  <div id="sidebar"></div>

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
      <!-- Header -->
      <div id="header" data-page-title="Notifikasi"></div>
        <!-- Jadwal Jemput Sampah -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0 overflow-y-scroll">
        <div class="p-4 flex items-start border-b mt-8">
            <div class="mr-4">
                <div class="bg-yellow-400 text-white p-3 rounded-full">
                    <!-- Icon -->
                    📦
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Jadwal Jemput Sampah</h3>
                <p class="text-gray-500 text-sm">Senin, 7 Maret 2024</p>
                <p class="text-gray-600 mt-2">Ada jadwal jemput sampah nih jam 07:00 - 09:00. Yuk, persiapkan sampahmu, itrashy picker sudah siap menuju tempatmu.</p>
                <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Lacak Trashy Picker
                </button>
            </div>
        </div>

        <!-- Bayar Tagihan Token Listrik -->
        <div class="p-4 flex items-start">
            <div class="mr-4">
                <div class="bg-yellow-500 text-white p-3 rounded-full">
                    <!-- Icon -->
                    ⚡
                </div>
                
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Bayar Tagihan Token Listrik</h3>
                <p class="text-gray-500 text-sm">Selasa, 20 Februari 2024</p>
                <p class="text-gray-600 mt-2">Hore, pembayaran berhasil. Terus kumpulkan saldo dengan mengumpulkan sampahmu dan nikmati benefitnya.</p>
                <div class="mt-3 bg-gray-200 text-gray-700 px-4 py-2 rounded flex items-center justify-between">
                    <span>1234-5678-9012-3456-7890</span>
                    <button class="text-blue-500 hover:text-blue-700">📋</button>
                </div>
            </div>
        </div>
    </div>
  </div>

 <script>
        (async function initializePage() {
            async function initializeSidebar() {
                try {
                    const sidebarResponse = await fetch("sidebar.html");
                    const headerResponse = await fetch("header.html");
                    
                    if (sidebarResponse.ok && headerResponse.ok) {
                        document.getElementById("sidebar").innerHTML = await sidebarResponse.text();
                        document.getElementById("header").innerHTML = await headerResponse.text();
                        
                        // Initialize menu functionality
                        initializeMenu();
                    } else {
                        console.error("Error loading components");
                    }
                } catch (error) {
                    console.error("Error initializing components:", error);
                }
            }

            function initializeMenu() {
                const menuToggle = document.getElementById('menuToggle');
                const menuClose = document.getElementById('menuClose');
                const menu = document.getElementById('menu');
                const navLinks = document.querySelectorAll('.nav-link');
                const pageTitle = document.getElementById('pageTitle');
                const currentPageTitle = document.querySelector('[data-page-title]').dataset.pageTitle;

                // Mobile menu toggles
                if (menuToggle && menuClose && menu) {
                    menuToggle.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
                    menuClose.addEventListener('click', () => menu.classList.add('-translate-x-full'));
                }

                // Set active menu item and initial page title
                navLinks.forEach(link => {
                    const linkText = link.querySelector('span').textContent;

                    if (linkText === currentPageTitle) {
                        link.classList.add('active');
                    }

                    link.addEventListener('click', () => {
                        // Set active state (without preventing navigation)
                        navLinks.forEach(l => l.classList.remove('active'));
                        link.classList.add('active');
                    });
                });

                // Update page title
                if (pageTitle) {
                    pageTitle.textContent = currentPageTitle === 'Dashboard' ? 'Halo, Atalaric👋' : currentPageTitle;
                }
            }


            await initializeSidebar();
        })();
    </script>
</body>
</html>
