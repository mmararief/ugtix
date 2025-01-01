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

        /* Enhanced Hero Section */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a1f8f 100%);
            border-radius: 0 0 50px 50px;
            margin-bottom: 4rem;
            overflow: hidden;
            animation: fadeIn 1s ease-out;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
            animation-delay: 0.3s;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
            animation-delay: 0.5s;
        }

        .hero-banner {
            position: relative;
            height: 400px;
            border-radius: 30px;
            margin: 2rem auto;
            max-width: 1200px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            opacity: 0;
            animation: scaleIn 1s ease-out forwards;
            animation-delay: 0.3s;
        }

        /* Enhanced Event Slider */
        .event-slider {
            padding: 20px 0 40px;
            margin: 0 -20px;
        }

        .swiper-slide {
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .swiper-slide-active {
            transform: scale(1.05);
        }

        .event-card {
            background: rgba(42, 42, 42, 0.95);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            height: 100%;
            backdrop-filter: blur(10px);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: var(--accent-color);
        }

        .event-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-card:hover .event-image img {
            transform: scale(1.1);
        }

        .event-details {
            padding: 1.5rem;
            position: relative;
        }

        .event-title {
            color: white;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .event-info {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-info i {
            color: var(--accent-color);
            font-size: 1rem;
        }

        .event-price {
            color: var(--accent-color);
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Enhanced Swiper Navigation */
        .swiper-button-next,
        .swiper-button-prev {
            background: rgba(255, 255, 255, 0.9);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .swiper-button-next {
            right: 10px;
        }

        .swiper-button-prev {
            left: 10px;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: var(--accent-color);
            transform: scale(1.1);
        }

        .swiper-button-next:hover:after,
        .swiper-button-prev:hover:after {
            color: var(--dark-bg);
        }

        /* Disable buttons on mobile */
        @media (max-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                display: none;
            }
        }

        /* Enhanced pagination */
        .swiper-pagination {
            position: relative;
            margin-top: 20px;
        }

        .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .swiper-pagination-bullet-active {
            width: 20px;
            border-radius: 4px;
            background: var(--accent-color);
        }

        /* Event Section */
        .event-section {
            padding: 3rem 2rem;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 0.8s;
        }

        .section-header {
            margin-bottom: 2rem;
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
            animation-delay: 1s;
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
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
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
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border-radius: 30px;
            margin: 4rem 2rem;
            padding: 4rem 2rem;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.2s;
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
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
        }

        .step:nth-child(1) {
            animation-delay: 1.4s;
        }

        .step:nth-child(3) {
            animation-delay: 1.6s;
        }

        .step:nth-child(5) {
            animation-delay: 1.8s;
        }

        .step:nth-child(7) {
            animation-delay: 2s;
        }

        .step img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .step:hover img {
            transform: scale(1.1);
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
            background-color: rgba(255, 255, 255, 0.9);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 18px;
        }

        .swiper-pagination-bullet-active {
            background: var(--accent-color);
        }

        .event-card {
            margin: 10px;
        }

        /* Animation Keyframes */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-banner {
                height: 300px;
            }

            .how-to-buy {
                margin: 2rem 1rem;
                padding: 2rem 1rem;
            }
        }
    </style>
    <!-- Add Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <!-- Enhanced Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Temukan Event Terbaik</h1>
            <p class="hero-subtitle">Jelajahi dan pesan tiket event favoritmu dengan mudah</p>
        </div>
        <div class="hero-banner">
            <img src="img/2.jpg" alt="Event Banner" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
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
            spaceBetween: 30,
            centeredSlides: true,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'coverflow',
            coverflowEffect: {
                rotate: 0,
                stretch: 0,
                depth: 100,
                modifier: 2,
                slideShadows: false,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    centeredSlides: false,
                },
                968: {
                    slidesPerView: 3,
                    centeredSlides: false,
                },
                1200: {
                    slidesPerView: 4,
                    centeredSlides: false,
                },
            },
        });
    </script>
</body>

</html>