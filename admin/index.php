<?php
// Memulai sesi
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';
// Ambil data untuk ditampilkan di dasbor (contoh jumlah event dan pesanan)
$queryEvent = "SELECT COUNT(*) AS total_event FROM events";
$queryPesanan = "SELECT COUNT(*) AS total_pesanan FROM pesanan";

$resultEvent = $conn->query($queryEvent);
$resultPesanan = $conn->query($queryPesanan);

$totalEvent = $resultEvent->fetch_assoc()['total_event'] ?? 0;
$totalPesanan = $resultPesanan->fetch_assoc()['total_pesanan'] ?? 0;

?>
// Include file koneksi database

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="navbar.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1464 0%, #2a1f8f 100%);
            border-radius: 0 0 30px 30px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.8s ease-out;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 1rem;
            text-align: center;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeIn 0.8s ease-out forwards;
            animation-delay: 0.3s;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            opacity: 0;
            animation: fadeIn 0.8s ease-out forwards;
            animation-delay: 0.5s;
        }

        /* Main Content Styles */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
        }

        .card:nth-child(1) {
            animation-delay: 0.4s;
        }

        .card:nth-child(2) {
            animation-delay: 0.6s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-icon {
            background-color: #1a1464;
            color: #9eff00;
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .card:hover .card-icon {
            transform: scale(1.1);
        }

        .card-title {
            color: #1a1464;
            margin: 0;
            font-size: 1.2rem;
        }

        .card-value {
            font-size: 2.5rem;
            color: #1a1464;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .quick-actions {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
            animation-delay: 0.8s;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-button {
            background: #1a1464;
            color: white;
            padding: 1rem;
            border-radius: 15px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            font-weight: 500;
        }

        .action-button:hover {
            background: #2a1f8f;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(26, 20, 100, 0.2);
        }

        /* Animations */
        @keyframes slideDown {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Welcome, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</h1>
            <p class="hero-subtitle">Manage your events and orders from one place</p>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="card-title">Total Event</h3>
                </div>
                <div class="card-value"><?php echo $totalEvent ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="card-title">Total Pesanan</h3>
                </div>
                <div class="card-value"><?php echo $totalPesanan ?></div>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="add_event.php" class="action-button">
                    <i class="fas fa-plus"></i>
                    Tambah Event Baru
                </a>
                <a href="pesanan.php?status=pending" class="action-button">
                    <i class="fas fa-clock"></i>
                    Pesanan Pending
                </a>
            </div>
        </div>
    </div>
</body>

</html>