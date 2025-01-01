<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
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

if (!$event) {
    header("Location: events.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Tiket - <?= htmlspecialchars($event['nama']) ?></title>
    <link href="navbar.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        :root {
            --primary-color: #1a1464;
            --secondary-color: #4e4bb8;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
        }

        .main-content {
            margin-top: 90px;
            padding: 2rem;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .scan-container {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .event-info {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .event-info h2 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }

        .event-info p {
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        #reader {
            width: 100%;
            margin: 0 auto;
        }

        .result-container {
            margin-top: 2rem;
            padding: 1.5rem;
            border-radius: 12px;
            display: none;
        }

        .result-success {
            background-color: #e6f7ed;
            color: var(--success);
            border: 1px solid var(--success);
        }

        .result-error {
            background-color: #ffe9e9;
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .back-btn {
            background-color: var(--gray-200);
            color: var(--text-dark);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .back-btn:hover {
            background-color: var(--gray-100);
        }

        .result-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .warning-message {
            color: #856404;
            margin-top: 1rem;
            font-weight: 500;
        }

        .success-message {
            color: var(--success);
            margin-top: 1rem;
            font-weight: 500;
        }

        .error-message {
            color: var(--danger);
            margin-top: 1rem;
            font-weight: 500;
        }

        .scan-again-btn {
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            background: var(--primary-color);
            color: white;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .scan-again-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <a href="event_participants.php?id=<?= $event_id ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="event-info">
            <h2><?= htmlspecialchars($event['nama']) ?></h2>
            <p><i class="far fa-calendar"></i> <?= date('d F Y', strtotime($event['tanggal'])) ?></p>
            <p><i class="far fa-clock"></i> <?= date('H:i', strtotime($event['waktu'])) ?> WIB</p>
            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['lokasi']) ?></p>
        </div>

        <div class="scan-container">
            <div id="reader"></div>
            <div id="result" class="result-container"></div>
        </div>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanner
            html5QrcodeScanner.clear();

            // Send AJAX request to verify ticket
            fetch('verify_ticket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        kode_tiket: decodedText,
                        event_id: <?= $event_id ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('result');
                    resultDiv.style.display = 'block';

                    if (data.success) {
                        // Valid ticket
                        resultDiv.className = 'result-container result-success';
                        resultDiv.innerHTML = `
                        <h3><i class="fas fa-check-circle"></i> Tiket Valid</h3>
                        <p>Nama: ${data.nama}</p>
                        <p>Email: ${data.email}</p>
                        <p>NPM: ${data.npm}</p>
                        <p class="success-message"><i class="fas fa-check"></i> ${data.message}</p>
                    `;
                    } else if (data.status === 'digunakan') {
                        // Ticket already digunakan
                        resultDiv.className = 'result-container result-warning';
                        resultDiv.innerHTML = `
                        <h3><i class="fas fa-exclamation-circle"></i> Tiket Sudah Digunakan</h3>
                        <p>Nama: ${data.ticket_info.nama}</p>
                        <p>Email: ${data.ticket_info.email}</p>
                        <p>NPM: ${data.ticket_info.npm}</p>
          
                    `;
                    } else {
                        // Invalid ticket
                        resultDiv.className = 'result-container result-error';
                        resultDiv.innerHTML = `
                        <h3><i class="fas fa-times-circle"></i> Tiket Tidak Valid</h3>
                        <p class="error-message">${data.message}</p>
                    `;
                    }

                    // Add reload button
                    resultDiv.innerHTML += `
                    <button onclick="location.reload()" class="scan-again-btn">
                        <i class="fas fa-redo"></i> Scan Ulang
                    </button>
                `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    const resultDiv = document.getElementById('result');
                    resultDiv.style.display = 'block';
                    resultDiv.className = 'result-container result-error';
                    resultDiv.innerHTML = `
                    <h3><i class="fas fa-exclamation-triangle"></i> Error</h3>
                    <p>Terjadi kesalahan saat memverifikasi tiket</p>
                    <button onclick="location.reload()" class="scan-again-btn">
                        <i class="fas fa-redo"></i> Scan Ulang
                    </button>
                `;
                });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                // aspectRatio: 1.0
            }
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>

</html>