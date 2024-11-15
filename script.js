let currentSlide = 0;
const slides = document.querySelectorAll(".slide");
const totalSlides = slides.length;

function showSlide(index) {
    const slidesContainer = document.querySelector(".slides");
    // Menggeser slide dengan CSS transform
    slidesContainer.style.transform = `translateX(-${index * 100}%)`;
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

// Pindah slide otomatis setiap 5 detik
setInterval(nextSlide, 5000);



    // Waste Chart
    const wasteCtx = document.getElementById('wasteChart').getContext('2d');
    new Chart(wasteCtx, {
        type: 'bar',
        data: {
            labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'],
            datasets: [{
                label: 'Kg Sampah',
                data: [30, 18, 38, 25, 12, 15],
                backgroundColor: '#6366F1',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Carbon Chart
    const carbonCtx = document.getElementById('carbonChart').getContext('2d');
    new Chart(carbonCtx, {
        type: 'line',
        data: {
            labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'],
            datasets: [{
                label: 'Kg CO²',
                data: [15, 8, 18, 5, 8, 2],
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
