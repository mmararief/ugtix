<?php
session_start();

// if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_nama'])) {
//     header("Location: login.php");
//     exit;
// }

require_once '../process/koneksi.php';

// Handle AJAX request for ticket validation
if (isset($_POST['ticket_code'])) {
    error_log('Received ticket code: ' . $_POST['ticket_code']);

    // Log the query for debugging
    $ticket_code = $_POST['ticket_code'];
    error_log('Running query for ticket: ' . $ticket_code);

    try {
        // Updated query to match your table structure
        $query = "SELECT t.*, e.nama as event_nama 
                  FROM tiket t 
                  JOIN events e ON t.id_event = e.id 
                  WHERE t.kode_tiket = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $ticket_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $response = [
                'ticket_found' => true,
                'ticket_id' => $row['id'],
                'event_name' => $row['event_nama'],
                'status' => $row['status'] ?: 'aktif',

            ];

            // Fixed the SQL syntax error by adding the missing quote
            if ($row['status'] === 'aktif' || $row['status'] === '') {
                $update_query = "UPDATE tiket SET status = 'digunakan' WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $row['id']);
                $update_stmt->execute();
                error_log('Updated ticket status for ID: ' . $row['id']);
            }
        } else {
            $response = [
                'ticket_found' => false,
                'message' => 'Tiket tidak ditemukan'
            ];
        }

        // Log the query result
        error_log('Query result: ' . json_encode($response));
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
        $response = [
            'ticket_found' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Tiket - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
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

        .scanner-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .result-container {
            display: none;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .result-valid {
            border-left: 5px solid #28a745;
        }

        .result-invalid {
            border-left: 5px solid #dc3545;
        }

        .result-used {
            border-left: 5px solid #ffc107;
        }

        .ticket-info {
            margin-top: 1rem;
        }

        .ticket-info p {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .status-valid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-invalid {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-used {
            background-color: #fff3cd;
            color: #856404;
        }

        .scan-button {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: #1a1464;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 1rem;
        }

        .scan-button:hover {
            background-color: #2a1f8f;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
        <ul class="navbar-nav">
            <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="events.php" class="nav-link">Events</a></li>
            <li><a href="pesanan.php" class="nav-link">Pesanan</a></li>
            <li><a href="logout.php" class="nav-link logout-btn">Logout</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="scanner-container">
            <h2>Scan Tiket Event</h2>
            <p>Arahkan kamera ke QR Code tiket untuk memvalidasi</p>
            <div id="reader"></div>
            <button id="startButton" class="scan-button">
                <i class="fas fa-camera"></i> Mulai Scan
            </button>
        </div>

        <div id="result" class="result-container">
            <h3>Hasil Scan</h3>
            <div class="ticket-info"></div>
        </div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const resultContainer = document.getElementById('result');
        const ticketInfo = document.querySelector('.ticket-info');
        const startButton = document.getElementById('startButton');
        let isScanning = false;

        startButton.addEventListener('click', () => {
            if (isScanning) {
                html5QrCode.stop().then(() => {
                    startButton.innerHTML = '<i class="fas fa-camera"></i> Mulai Scan';
                    isScanning = false;
                });
            } else {
                html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    onScanSuccess,
                    onScanError
                ).then(() => {
                    startButton.innerHTML = '<i class="fas fa-stop"></i> Stop Scan';
                    isScanning = true;
                });
            }
        });

        function onScanSuccess(decodedText) {
            // Stop scanning after successful scan
            html5QrCode.stop().then(() => {
                startButton.innerHTML = '<i class="fas fa-camera"></i> Mulai Scan';
                isScanning = false;
                validateTicket(decodedText);
            });
        }

        function onScanError(error) {
            // Handle scan error
            console.warn(`Code scan error = ${error}`);
        }

        function validateTicket(ticketCode) {
            console.log('Attempting to validate ticket:', ticketCode); // Debug: Log ticket code

            fetch('scan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ticket_code=${encodeURIComponent(ticketCode)}`
                })
                .then(response => {
                    console.log('Raw response:', response); // Debug: Log raw response
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data); // Debug: Log parsed response data
                    resultContainer.style.display = 'block';

                    if (data.ticket_found) {
                        console.log('Ticket status:', data.status); // Debug: Log ticket status
                        let statusClass = '';
                        let statusText = '';

                        switch (data.status) {
                            case 'aktif':
                                statusClass = 'status-valid';
                                statusText = 'Valid';
                                resultContainer.className = 'result-container result-valid';
                                break;
                            case 'digunakan':
                                statusClass = 'status-used';
                                statusText = 'Sudah Digunakan';
                                resultContainer.className = 'result-container result-used';
                                break;
                            default:
                                console.log('Unexpected status:', data.status); // Debug: Log unexpected status
                                statusClass = 'status-invalid';
                                statusText = 'Tidak Valid';
                                resultContainer.className = 'result-container result-invalid';
                        }

                        ticketInfo.innerHTML = `
                            <p><strong>Event:</strong> ${data.event_name}</p>
               
                            <p><strong>Status:</strong> <span class="status-badge ${statusClass}">${statusText}</span></p>
                        `;
                    } else {
                        console.log('Ticket not found in database'); // Debug: Log ticket not found
                        resultContainer.className = 'result-container result-invalid';
                        ticketInfo.innerHTML = `
                            <p class="status-badge status-invalid">Tiket tidak ditemukan</p>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Detailed error:', error); // Debug: Log detailed error
                    console.error('Error stack:', error.stack); // Debug: Log error stack trace
                    resultContainer.style.display = 'block';
                    resultContainer.className = 'result-container result-invalid';
                    ticketInfo.innerHTML = `
                        <p class="status-badge status-invalid">Terjadi kesalahan saat memvalidasi tiket</p>
                        <p style="color: red; font-size: 12px;">${error.message}</p>
                    `;
                });
        }
    </script>
</body>

</html>