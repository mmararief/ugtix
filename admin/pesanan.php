<?php
// Memulai sesi
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';

// Query untuk mengambil data pesanan dari tabel
$queryPesanan = "
    SELECT id, email, nama, npm, tanggal, id_event, metode, total, status_pembayaran 
    FROM pesanan 
    ORDER BY id DESC
";
$resultPesanan = $conn->query($queryPesanan);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
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

        /* Navbar Styles */
        .navbar {
            background-color: #1a1464;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-nav {
            display: flex;
            gap: 1rem;
            list-style: none;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Main Content Styles */
        .main-content {
            margin-top: 80px;
            padding: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            background-color: #1a1464;
            color: white;
        }

        .status-pending {
            color: orange;
        }

        .status-completed {
            color: green;
        }

        .status-canceled {
            color: red;
        }

        .action-button {
            text-decoration: none;
            padding: 0.5rem 1rem;
            color: white;
            background-color: #1a1464;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #0000ff;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="navbar-brand">
            Admin Panel
        </a>
        <ul class="navbar-nav">
            <li><a href="admin_dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="orders.php" class="nav-link">Pesanan</a></li>
            <li><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <h2>Daftar Pesanan</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Nama</th>
                    <th>NPM</th>
                    <th>Tanggal</th>
                    <th>ID Event</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultPesanan->num_rows > 0) {
                    while ($row = $resultPesanan->fetch_assoc()) {
                        $statusClass = "";
                        switch ($row['status_pembayaran']) {
                            case 'pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'completed':
                                $statusClass = 'status-completed';
                                break;
                            case 'canceled':
                                $statusClass = 'status-canceled';
                                break;
                        }
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>" . $row['npm'] . "</td>";
                        echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                        echo "<td>" . $row['id_event'] . "</td>";
                        echo "<td>" . ucfirst($row['metode']) . "</td>";
                        echo "<td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>";
                        echo "<td class='" . $statusClass . "'>" . ucfirst($row['status_pembayaran']) . "</td>";
                        echo "<td><a href='view_order.php?id=" . $row['id'] . "' class='action-button'>Lihat</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada pesanan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
