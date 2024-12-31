<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';

// Get event ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['id'];

// Fetch event data
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    header("Location: events.php");
    exit;
}

$event = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Check if new image is uploaded
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../uploads/events/";
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Validate file
        $allowed_types = array('jpg', 'jpeg', 'png');
        if (in_array($file_extension, $allowed_types) && move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // Delete old image
            if (file_exists($target_dir . $event['gambar'])) {
                unlink($target_dir . $event['gambar']);
            }

            // Update database with new image
            $query = "UPDATE events SET nama=?, tanggal=?, waktu=?, lokasi=?, harga=?, deskripsi=?, gambar=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssdssi", $nama, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $new_filename, $event_id);
        } else {
            $error = "Gagal mengupload gambar. Pastikan file adalah JPG, JPEG, atau PNG.";
        }
    } else {
        // Update without changing image
        $query = "UPDATE events SET nama=?, tanggal=?, waktu=?, lokasi=?, harga=?, deskripsi=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssdsi", $nama, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $event_id);
    }

    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['success'] = "Event berhasil diperbarui!";
        header("Location: events.php");
        exit;
    } else {
        $error = "Gagal memperbarui event.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Admin Panel</title>
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
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            color: #1a1464;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1a1464;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #1a1464;
            outline: none;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .error-message {
            color: #ff3b3b;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .current-image {
            max-width: 200px;
            margin: 1rem 0;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #1a1464;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2a1f8f;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-right: 1rem;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
        }

        .preview-image {
            max-width: 200px;
            margin-top: 1rem;
            display: none;
            border-radius: 5px;
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
        <div class="card">
            <h2 class="form-title">Edit Event</h2>

            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Event</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        value="<?= htmlspecialchars($event['nama']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control"
                        value="<?= $event['tanggal'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="waktu">Waktu</label>
                    <input type="time" id="waktu" name="waktu" class="form-control"
                        value="<?= $event['waktu'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control"
                        value="<?= htmlspecialchars($event['lokasi']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" class="form-control"
                        value="<?= $event['harga'] ?>" min="0" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control"
                        required><?= htmlspecialchars($event['deskripsi']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar Event</label>
                    <input type="file" id="gambar" name="gambar" class="form-control"
                        accept="image/jpeg,image/png">
                    <small>Biarkan kosong jika tidak ingin mengubah gambar</small>

                    <?php if ($event['gambar']): ?>
                        <p>Gambar saat ini:</p>
                        <img src="../uploads/events/<?= htmlspecialchars($event['gambar']) ?>"
                            alt="Current event image" class="current-image">
                    <?php endif; ?>

                    <img id="preview" class="preview-image">
                </div>

                <div class="actions">
                    <a href="events.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview for new uploads
        document.getElementById('gambar').onchange = function(evt) {
            const preview = document.getElementById('preview');
            const file = evt.target.files[0];

            if (file) {
                preview.style.display = 'block';
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
        };
    </script>
</body>

</html>