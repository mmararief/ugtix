<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
    header("Location: login.php");
    exit;
}

require_once '../process/koneksi.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Handle file upload
    $target_dir = "../uploads/events/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
    }

    $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (!is_writable($target_dir)) {
        die("Directory is not writable: " . realpath($target_dir));
    }

    // Validate file
    $allowed_types = array('jpg', 'jpeg', 'png');
    if (in_array($file_extension, $allowed_types) && move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        // Insert into database
        $query = "INSERT INTO events (nama, tanggal, waktu, lokasi, harga, deskripsi, gambar) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssdss", $nama, $tanggal, $waktu, $lokasi, $harga, $deskripsi, $new_filename);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Event berhasil ditambahkan!";
            header("Location: events.php");
            exit;
        } else {
            $error = "Gagal menambahkan event.";
        }
    } else {
        $error = "Gagal mengupload gambar. Pastikan file adalah JPG, JPEG, atau PNG.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event Baru</title>
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

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
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
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>


    <div class="main-content">
        <div class="card">
            <h2 class="form-title">Tambah Event Baru</h2>

            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Event</label>
                    <input type="text" id="nama" name="nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="waktu">Waktu</label>
                    <input type="time" id="waktu" name="waktu" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" class="form-control" min="0" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar Event</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/jpeg,image/png" required>
                    <img id="preview" class="preview-image">
                </div>

                <div class="actions">
                    <a href="events.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview
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