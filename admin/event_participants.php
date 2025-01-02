<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['id'];

// Get event details
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

// Add statistics query
$stats_query = "SELECT 
    COUNT(*) as total_participants,
    SUM(CASE WHEN t.status = 'digunakan' THEN 1 ELSE 0 END) as scanned_count
FROM pesanan p 
LEFT JOIN tiket t ON p.id = t.id_pesanan
WHERE p.id_event = ?";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $event_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Modify participants query to include filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT p.*, t.kode_tiket, t.status as status_tiket
          FROM pesanan p 
          LEFT JOIN tiket t ON p.id = t.id_pesanan
          WHERE p.id_event = ?";

if ($filter === 'scanned') {
    $query .= " AND t.status = 'digunakan'";
} elseif ($filter === 'unscanned') {
    $query .= " AND (t.status = 'aktif' OR t.status IS NULL)";
}

$query .= " ORDER BY p.tanggal DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$participants = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="navbar.css" rel="stylesheet">
    <title>Daftar Peserta - <?= htmlspecialchars($event['nama']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        :root {
            --primary-color: #1a1464;
            --secondary-color: #4e4bb8;
            --accent-color: #ffd700;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
        }



        /* Main Content */
        .main-content {
            margin-top: 90px;
            padding: 2rem;
            max-width: 1300px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Event Header */
        .event-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .event-header h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .event-header p {
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            opacity: 0.9;
        }

        /* Buttons */
        .button-group {
            margin: 2rem 0;
            display: flex;
            gap: 1rem;
        }

        .back-btn,
        .export-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .back-btn {
            background-color: var(--white);
            color: var(--text-dark);
            border: 1px solid var(--gray-200);
        }

        .export-btn {
            background-color: var(--success);
            color: var(--white);
        }

        .back-btn:hover,
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Table Styles */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        th {
            background-color: var(--gray-100);
            color: var(--text-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem 1.5rem;
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            color: var(--text-light);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background-color: var(--gray-100);
            transform: scale(1.001);
        }

        /* Status Badges */
        .status-pending,
        .status-completed,
        .status-canceled {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        .status-pending {
            background-color: #fff8e6;
            color: #fab005;
        }

        .status-completed {
            background-color: #e6f7ed;
            color: #00b894;
        }

        .status-canceled {
            background-color: #ffe9e9;
            color: #d63031;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-light);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .event-header {
                padding: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }

            th,
            td {
                padding: 0.75rem 1rem;
            }
        }

        .scan-btn {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .scan-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background-color: var(--secondary-color);
        }

        /* Statistics Cards */
        .stats-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            flex: 1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-card h4 {
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Filter Buttons */
        .filter-group {
            margin-bottom: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-dark);
        }

        .filter-btn:hover {
            background-color: var(--gray-100);
        }

        .filter-btn.active {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <div class="event-header">
            <h3><?= htmlspecialchars($event['nama']) ?></h3>
            <p><i class="far fa-calendar"></i> <?= date('d F Y', strtotime($event['tanggal'])) ?></p>
            <p><i class="far fa-clock"></i> <?= date('H:i', strtotime($event['waktu'])) ?> WIB</p>
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lokasi']) ?></p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h4>Total Pendaftar</h4>
                <div class="number"><?= $stats['total_participants'] ?></div>
            </div>
            <div class="stat-card">
                <h4>Sudah Hadir</h4>
                <div class="number"><?= $stats['scanned_count'] ?></div>
            </div>
            <div class="stat-card">
                <h4>Belum Hadir</h4>
                <div class="number"><?= $stats['total_participants'] - $stats['scanned_count'] ?></div>
            </div>
        </div>

        <div class="filter-group">
            <a href="?id=<?= $event_id ?>" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
                Semua
            </a>
            <a href="?id=<?= $event_id ?>&filter=scanned" class="filter-btn <?= $filter === 'scanned' ? 'active' : '' ?>">
                Sudah Hadir
            </a>
            <a href="?id=<?= $event_id ?>&filter=unscanned" class="filter-btn <?= $filter === 'unscanned' ? 'active' : '' ?>">
                Belum Hadir
            </a>
        </div>

        <div class="button-group">
            <a href="events.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="scan_event.php?id=<?= $event_id ?>" class="scan-btn">
                <i class="fas fa-qrcode"></i> Scan Tiket
            </a>
            <!-- <a href="export_participants.php?id=<?= $event_id ?>" class="export-btn">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </a> -->
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NPM</th>
                        <th>Tanggal Pesanan</th>
                        <th>Total Bayar</th>
                        <th>Metode Pembayaran</th>
                        <th>Kode Tiket</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = $participants->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['npm']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['metode']) ?></td>
                            <td><?= htmlspecialchars($row['kode_tiket'] ?? '-') ?></td>
                            <td>
                                <span class="status-<?= $row['status_tiket'] === 'digunakan' ? 'completed' : 'pending' ?>">
                                    <?= $row['status_tiket'] === 'digunakan' ? 'Hadir' : ($row['status_tiket'] === 'aktif' ? 'Belum Hadir' : 'Pending') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($participants->num_rows === 0): ?>
                        <div class="empty-state">
                            <i class="fas fa-users" style="font-size: 3rem; color: var(--gray-200); margin-bottom: 1rem;"></i>
                            <p>Belum ada peserta terdaftar untuk event ini</p>
                        </div>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>