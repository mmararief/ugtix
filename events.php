<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UGTIX - Semua Event</title>
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

        /* Events Page Specific Styles */
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            color: var(--accent-color);
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            background-color: #2a2a2a;
            color: white;
            border: 1px solid #333;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-btn.active {
            background-color: var(--accent-color);
            color: black;
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .event-card {
            background-color: #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #333;
            transition: transform 0.2s;
        }

        .event-card:hover {
            transform: translateY(-5px);
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .page-btn {
            background-color: #2a2a2a;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .page-btn.active {
            background-color: var(--accent-color);
            color: black;
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            padding: 2rem;
            color: white;
            margin-top: 2rem;
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

        @media (max-width: 768px) {
            .filters {
                flex-wrap: wrap;
            }

            .footer-content {
                flex-direction: column;
                gap: 2rem;
            }
        }

        .event-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
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
        <a href="#" class="login-btn">Login</a>
    </header>

    <!-- Main Content -->
    <main class="events-container">
        <h1 class="page-title">Semua Event</h1>

        <!-- Filters -->
        <div class="filters">
            <button class="filter-btn active">Semua</button>
            <button class="filter-btn">Musik</button>
            <button class="filter-btn">Kompetisi</button>
            <button class="filter-btn">Workshop</button>
            <button class="filter-btn">Festival</button>
        </div>

        <!-- Event Grid -->
        <div class="event-grid">
            <!-- Event Cards -->
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
            $sql = "SELECT id,nama, tanggal, waktu, lokasi, harga, deskripsi, gambar FROM events";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="detailevent.php?id=' . $row["id"] . '" class="event-card-link">';
                    echo '<div class="event-card">';
                    echo '    <div class="event-image">';
                    echo '        <img src="uploads/events/' . $row["gambar"] . '" alt="' . $row["nama"] . '" style="width: 100%; height: 100%; object-fit: cover;">';
                    echo '    </div>';
                    echo '    <div class="event-details">';
                    echo '        <h3 class="event-title">' . $row["nama"] . '</h3>';
                    echo '        <p class="event-info">' . $row["tanggal"] . ' ' . $row["waktu"] . '</p>';
                    echo '        <p class="event-info">' . $row["lokasi"] . '</p>';
                    echo '        <p class="event-price">Rp ' . number_format($row["harga"], 0, ',', '.') . '</p>';
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

        <!-- Pagination -->
        <div class="pagination">
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">4</button>
            <button class="page-btn">→</button>
        </div>
    </main>

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