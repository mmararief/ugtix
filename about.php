<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang UGTIX</title>
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
            color: white;
        }

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

        .about-section {
            padding: 3rem 2rem;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .about-section h1 {
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .about-section p {
            margin-bottom: 1rem;
            line-height: 1.6;
            color: #ccc;
        }

        .team-section {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .team-section h2 {
            color: var(--accent-color);
            margin-bottom: 2rem;
        }

        .team-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5 kolom untuk 5 anggota tim */
            gap: 2rem;
            justify-items: center; /* Pusatkan elemen di setiap kolom */
            align-items: start; /* Pusatkan elemen secara vertikal */
            margin-top: 2rem;
        }

        .team-member {
            text-align: center;
            color: white;
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 2px solid var(--accent-color);
            align-content: center;
        }

        .team-member h3 {
            margin-bottom: 0.5rem;
            color: var(--accent-color);
            align-content: center;
        }

        .footer {
            background-color: var(--primary-color);
            padding: 2rem;
            color: white;
            margin-top: 3rem;
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

        .contact-info {
            color: #ccc;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

      <!-- About Section -->
      <section class="about-section">
        <h1>Tentang UGTIX</h1>
        <p>UGTIX adalah platform manajemen tiket acara yang memudahkan pengguna untuk menemukan dan membeli tiket acara dengan cepat dan praktis. Kami menyediakan berbagai pilihan acara yang dapat diakses kapan saja dan di mana saja.</p>
        <p>Dengan antarmuka yang ramah pengguna dan dukungan pembayaran yang aman, UGTIX hadir untuk memenuhi kebutuhan hiburan dan acara Anda.</p>
        <p>Hubungi kami untuk informasi lebih lanjut atau pertanyaan terkait layanan kami.</p>
    </section>

    <!-- Our Team Section -->
    <section class="team-section">
        <h2>Our Team</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Team Member 1">
                <h3>Azzahra Dania Indriyani</h3>
                <p>CEO</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Team Member 2">
                <h3>Dellia Putri Santoso</h3>
                <p>CTO</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Team Member 3">
                <h3>Mifta Rizaldirahmat</h3>
                <p>Marketing Lead</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Team Member 4">
                <h3>Muhammad Naufal Hilmy</h3>
                <p>Product Manager</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/150" alt="Team Member 5">
                <h3>Muhammad Ammar Arief</h3>
                <p>Developer</p>
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
