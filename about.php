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