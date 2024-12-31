<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';

// Handle event deletion
if (isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];

    // Get image filename before deletion
    $query = "SELECT gambar FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = "../uploads/events/" . $row['gambar'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete from database
    $query = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Event berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus event.";
    }
    header("Location: events.php");
    exit;
}

// Fetch all events
$query = "SELECT * FROM events ORDER BY tanggal DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - Admin Panel</title>
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

        .main-content {
            margin-top: 80px;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #1a1464;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2a1f8f;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-details {
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.25rem;
            color: #1a1464;
            margin-bottom: 0.5rem;
        }

        .event-info {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .event-price {
            font-weight: bold;
            color: #1a1464;
            margin-bottom: 1rem;
        }

        .event-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #000;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 3rem;
            color: #1a1464;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #1a1464;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="admin_dashboard.php" class="navbar-brand">Admin Panel</a>
        <ul class="navbar-nav">
            <li><a href="admin_dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="orders.php" class="nav-link">Pesanan</a></li>
            <li><a href="logout.php" class="nav-link logout-btn">Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h2>Daftar Event</h2>
            <a href="add_event.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Event
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="events-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="event-card">
                        <img src="../uploads/events/<?= htmlspecialchars($row['gambar']) ?>"
                            alt="<?= htmlspecialchars($row['nama']) ?>"
                            class="event-image">
                        <div class="event-details">
                            <h3 class="event-title"><?= htmlspecialchars($row['nama']) ?></h3>
                            <p class="event-info">
                                <i class="far fa-calendar"></i>
                                <?= date('d F Y', strtotime($row['tanggal'])) ?>
                            </p>
                            <p class="event-info">
                                <i class="far fa-clock"></i>
                                <?= date('H:i', strtotime($row['waktu'])) ?> WIB
                            </p>
                            <p class="event-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($row['lokasi']) ?>
                            </p>
                            <p class="event-price">
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </p>
                            <div class="event-actions">
                                <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="" method="POST" style="display: inline;"
                                    onsubmit="return confirm('Yakin ingin menghapus event ini?');">
                                    <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_event" class="btn btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="far fa-calendar-times"></i>
                <h3>Belum ada event</h3>
                <p>Mulai tambahkan event pertama Anda</p>
                <a href="add_event.php" class="btn btn-primary">Tambah Event</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>