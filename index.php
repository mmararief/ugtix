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
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="#" class="logo">UGTIX</a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="events.php">Event</a>
            <a href="#">About</a>
        </nav>
        <a href="admin/login.php" class="login-btn">Login</a>
    </header>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <img src="img/2.jpg" alt="Event Banner" style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <!-- Event Section -->
    <section class="event-section">
        <div class="section-header">
            <h2 class="section-title">Event tersedia</h2>
            <a href="#" class="see-all">Semua event →</a>
        </div>
        <div class="event-grid">
            <?php
            // Connect to the database
            $servername = "localhost:3305";
            $username = "root";
            $password = "";
            $dbname = "ugtix";
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch events from the database
            $sql = "SELECT id, nama, tanggal, waktu, lokasi, harga, deskripsi, gambar FROM events";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="detailevent.php?id=' . $row["id"] . '" style="  text-decoration: none;color: inherit;display: block;">';
                    echo '<div class="event-card">';
                    echo '    <div class="event-image">';
                    echo '        <img src="uploads/events/' .  $row["gambar"] . '" alt="' . $row["nama"] . '" style="width: 100%; height: 100%; object-fit: cover;">';
                    echo '    </div>';
                    echo '    <div class="event-details">';
                    echo '        <h3 class="event-title">' . $row["nama"] . '</h3>';
                    echo '        <p class="event-info">' . $row["tanggal"] . ' ' . $row["waktu"] . '</p>';
                    echo '        <p class="event-info">' . $row["lokasi"] . '</p>';
                    echo '        <p class="event-price">' . $row["harga"] . '</p>';
                    echo '        <p class="event-description">' . $row["deskripsi"] . '</p>';
                    echo '    </div>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo "No events available.";
            }
            $conn->close();
            ?>
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
</body>

</html>