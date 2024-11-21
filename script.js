document.addEventListener("DOMContentLoaded", async () => {
    // Tunggu hingga header dan sidebar selesai dimuat
    await includeHTML("header", "header.html");
    await includeHTML("sidebar", "sidebar.html");

    // Ambil nama file dari URL
    const currentPage = window.location.pathname.split("/").pop();

    // Pilih elemen-elemen DOM
    const menuItems = document.querySelectorAll(".menu-item");
    const headerTitle = document.querySelector(".header-all h1");

    // Mapping antara halaman dan judul header
    const pageTitles = {
        "Jemput.html": "Jemput Sampah",
        "tukarPoin.html": "Tukar Poin",
        "edukasi.html": "Edukasi",
    };

    // Tandai menu aktif dan perbarui judul header
    menuItems.forEach(item => {
        const link = item.getAttribute("data-link");
        if (link === currentPage) {
            item.classList.add("active"); // Tambahkan kelas active
        } else {
            item.classList.remove("active"); // Hapus kelas active lainnya
        }
    });

    if (headerTitle) {
        headerTitle.textContent = pageTitles[currentPage] || "I-Trashy";
    }

    console.log("Current Page:", currentPage);
    console.log("Header Element:", headerTitle);
    console.log("Menu Items:", menuItems);

    // Grafik baru dimuat setelah DOM selesai
    initializeCharts();
});

// Fungsi untuk memuat file HTML ke dalam elemen
async function includeHTML(id, url) {
    try {
        const element = document.getElementById(id);
        if (element) {
            const response = await fetch(url); // Fetch file HTML
            if (response.ok) {
                element.innerHTML = await response.text(); // Sisipkan isi file ke elemen
            } else {
                console.error(`Error loading ${url}: ${response.status}`);
            }
        }
    } catch (error) {
        console.error(`Error fetching ${url}:`, error);
    }
}

// Inisialisasi Chart.js untuk grafik
function initializeCharts() {
    // Waste Chart
    const wasteCanvas = document.getElementById("wasteChart");
    if (wasteCanvas) {
        const wasteCtx = wasteCanvas.getContext("2d");
        new Chart(wasteCtx, {
            type: "bar",
            data: {
                labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN"],
                datasets: [{
                    label: "Kg Sampah",
                    data: [30, 18, 38, 25, 12, 15],
                    backgroundColor: "#6366F1",
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2],
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    // Carbon Chart
    const carbonCanvas = document.getElementById("carbonChart");
    if (carbonCanvas) {
        const carbonCtx = carbonCanvas.getContext("2d");
        new Chart(carbonCtx, {
            type: "line",
            data: {
                labels: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN"],
                datasets: [{
                    label: "Kg CO²",
                    data: [15, 8, 18, 5, 8, 2],
                    borderColor: "#6366F1",
                    backgroundColor: "rgba(99, 102, 241, 0.1)",
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2],
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
}

// Fungsi untuk slider
let currentSlide = 0;
function showSlide(index) {
    const slidesContainer = document.querySelector(".slides");
    if (slidesContainer) {
        slidesContainer.style.transform = `translateX(-${index * 100}%)`;
    }
}

function nextSlide() {
    const slides = document.querySelectorAll(".slide");
    if (slides.length > 0) {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
}

// Pindah slide otomatis setiap 5 detik
setInterval(nextSlide, 5000);