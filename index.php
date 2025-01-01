<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UGTIX - Kelola Tiket Acara</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        :root {
            --primary-color: #1a1464;
            --accent-color: #9eff00;
            --dark-bg: #1a1a1a;
        }

        body {
            background-color: var(--dark-bg);
        }

        /* Header Styles */
        .header {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: var(--accent-color);
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
        }

        .login-btn {
            background-color: var(--accent-color);
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            color: black;
            text-decoration: none;
        }

        /* Hero Banner */
        .hero-banner {
            width: 100%;
            height: 300px;
            background-image: url('/api/placeholder/1200/300');
            background-size: cover;
            background-position: center;
            margin-bottom: 2rem;
        }

        /* Event Section */
        .event-section {
            padding: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: var(--accent-color);
            font-size: 1.5rem;
        }

        .see-all {
            color: var(--accent-color);
            text-decoration: none;
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background-color: #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #333;
        }

        .event-image {
            width: 100%;
            height: 150px;
            background-size: cover;
            background-position: center;
        }

        .event-details {
            padding: 1rem;
        }

        .event-title {
            color: white;
            margin-bottom: 0.5rem;
        }

        .event-info {
            color: #888;
            font-size: 0.9rem;
        }

        .event-price {
            color: var(--accent-color);
            margin-top: 0.5rem;
        }

        /* How to Buy Section */
        .how-to-buy {
            padding: 2rem;
            background-color: #2a2a2a;
            margin: 2rem 0;
        }

        .steps {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 2rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            text-align: center;
        }

        .step img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .arrow {
            color: var(--accent-color);
            font-size: 2rem;
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            padding: 2rem;
            color: white;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-info h2 {
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .footer-info p {
            max-width: 300px;
            color: #ccc;
        }

        .contact-info {
            color: #ccc;
        }

        .event-slider {
            padding: 20px;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: var(--accent-color);
        }

        .swiper-pagination-bullet-active {
            background: var(--accent-color);
        }

        .event-card {
            margin: 10px;
        }
    </style>
    <!-- Add Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <!-- Hero Banner -->
    <div class="hero-banner">
        <img src="img/2.jpg" alt="Event Banner" style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <!-- Event Section -->
    <section class="event-section">
        <div class="section-header">
            <h2 class="section-title">Event tersedia</h2>
            <a href="events.php" class="see-all">Semua event →</a>
        </div>
        <!-- Add Swiper container -->
        <div class="swiper event-slider">
            <div class="swiper-wrapper">
                <?php
                // Connect to the database
                require_once 'process/koneksi.php';
                // Fetch 6 latest events from the database
                $sql = "SELECT id, nama, tanggal, waktu, lokasi, harga, deskripsi, gambar FROM events ORDER BY id DESC LIMIT 6";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="swiper-slide">';
                        echo '<a href="detailevent.php?id=' . $row["id"] . '" style="text-decoration: none;color: inherit;display: block;">';
                        echo '<div class="event-card">';
                        echo '    <div class="event-image">';
                        echo '        <img src="uploads/events/' .  $row["gambar"] . '" alt="' . $row["nama"] . '" style="width: 100%; height: 100%; object-fit: cover;">';
                        echo '    </div>';
                        echo '    <div class="event-details">';
                        echo '        <h3 class="event-title">' . $row["nama"] . '</h3>';
                        echo '        <p class="event-info">' . $row["tanggal"] . ' ' . $row["waktu"] . '</p>';
                        echo '        <p class="event-info">' . $row["lokasi"] . '</p>';
                        echo '        <p class="event-price">' . $row["harga"] . '</p>';
                        echo '    </div>';
                        echo '</div>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo "No events available.";
                }
                $conn->close();
                ?>
            </div>
            <!-- Add navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <!-- Add pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- How to Buy Section -->
    <section class="how-to-buy">
        <h2 class="section-title" style="text-align: center;">Cara Beli Tiket</h2>
        <p style="text-align: center; color: #888; margin-top: 0.5rem;">Sekarang ikut event jadi lebih mudah</p>
        <div class="steps">
            <div class="step">
                <img src="img/akun.png" alt="Step 1">
                <p>Daftarkan Akun</p>
            </div>
            <div class="arrow">→</div>
            <div class="step">
                <img src="img/choose-topic.png" alt="Step 2">
                <p>Pilih Event</p>
            </div>
            <div class="arrow">→</div>
            <div class="step">
                <img src="img/money.png" alt="Step 3">
                <p>Pembayaran</p>
            </div>
            <div class="arrow">→</div>
            <div class="step">
                <img src="img/checked.png" alt="Step 4">
                <p>Pembelian Selesai</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-info">
                <h2>UGTIX</h2>
                <p>Kelola Tiket Acara Kamu Jadi Super Praktis dengan UGTIX!</p>
            </div>
            <div class="contact-info">
                <h3 style="color: var(--accent-color); margin-bottom: 1rem;">Kontak Kami</h3>
                <p>Jl. KH. Noer Ali, RT.005/RW.006A, Jakasampurna, Kec. Bekasi Bar., Kota Bks, Jawa Barat 17145</p>
            </div>
        </div>
    </footer>

    <!-- Add Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.event-slider', {
            slidesPerView: 1,
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                968: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            },
        });
    </script>
</body>

</html>