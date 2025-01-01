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

        /* Main Content Styles */
        .main-content {
            margin-top: 80px;
            padding: 2rem;
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
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-icon {
            background-color: #1a1464;
            color: #9eff00;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .card-title {
            color: #1a1464;
            margin: 0;
        }

        .card-value {
            font-size: 2rem;
            color: #1a1464;
            font-weight: bold;
        }

        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .action-button {
            background: #1a1464;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-button:hover {
            background: #2a1f8f;
        }

        .logout-btn {
            background: #ff3b3b;
        }

        .logout-btn:hover {
            background: #ff5252;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

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