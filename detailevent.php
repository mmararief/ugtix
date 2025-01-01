<?php
// Connect to the database
$servername = "localhost:3305";
$username = "root";
$password = "";
$dbname = "ugtix";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the event ID from the URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch event details from the database
$sql = "SELECT nama, tanggal, waktu, lokasi, harga, deskripsi, gambar FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UGTIX - Detail Event</title>
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
            }

            /* Header Styles */
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

            /* Event Detail Styles */
            .event-container {
                max-width: 1200px;
                margin: 2rem auto;
                padding: 0 2rem;
            }

            .event-header {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .event-image {
                width: 100%;
                height: 400px;
                border-radius: 8px;
                overflow: hidden;
            }

            .event-info {
                color: white;
            }

            .event-title {
                color: var(--accent-color);
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .event-meta {
                margin-bottom: 1.5rem;
            }

            .meta-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 0.5rem;
                color: #ccc;
            }

            .event-price-tag {
                font-size: 1.5rem;
                color: var(--accent-color);
                margin-bottom: 1rem;
            }

            .ticket-selection {
                background-color: #2a2a2a;
                padding: 1.5rem;
                border-radius: 8px;
                margin-bottom: 1rem;
            }

            .ticket-type {
                margin-bottom: 1rem;
            }

            .ticket-type label {
                color: white;
                display: block;
                margin-bottom: 0.5rem;
            }

            .ticket-quantity {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .quantity-btn {
                background-color: var(--primary-color);
                color: white;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 4px;
                cursor: pointer;
            }

            .quantity-input {
                background-color: #1a1a1a;
                border: 1px solid #444;
                color: white;
                padding: 0.5rem;
                width: 60px;
                text-align: center;
                border-radius: 4px;
            }

            .buy-btn {
                background-color: var(--accent-color);
                color: black;
                border: none;
                padding: 1rem 2rem;
                border-radius: 4px;
                font-weight: bold;
                cursor: pointer;
                width: 100%;
            }

            .event-description {
                background-color: #2a2a2a;
                padding: 2rem;
                border-radius: 8px;
                margin-bottom: 2rem;
            }

            .description-title {
                color: var(--accent-color);
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            .description-content {
                color: #ccc;
                line-height: 1.6;
            }

            .event-location {
                background-color: #2a2a2a;
                padding: 2rem;
                border-radius: 8px;
            }

            .map-container {
                height: 300px;
                background-color: #1a1a1a;
                border-radius: 8px;
                margin-top: 1rem;
            }

            /* Footer */
            .footer {
                background-color: var(--primary-color);
                padding: 2rem;
                color: white;
                margin-top: 2rem;
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

            .footer-info p {
                max-width: 300px;
                color: #ccc;
            }

            .contact-info {
                color: #ccc;
            }

            @media (max-width: 768px) {
                .event-header {
                    grid-template-columns: 1fr;
                }

                .event-image {
                    height: 300px;
                }

                .footer-content {
                    flex-direction: column;
                    gap: 2rem;
                }
            }

            /* Enhanced Modal Styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(5px);
            }

            .modal-content {
                background-color: var(--dark-bg);
                color: white;
                margin: 5% auto;
                padding: 2rem;
                border-radius: 12px;
                width: 90%;
                max-width: 500px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                position: relative;
                animation: modalSlideIn 0.3s ease-out;
            }

            @keyframes modalSlideIn {
                from {
                    transform: translateY(-100px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .close-btn {
                position: absolute;
                right: 1.5rem;
                top: 1.5rem;
                color: #888;
                font-size: 1.5rem;
                font-weight: bold;
                cursor: pointer;
                transition: color 0.3s;
            }

            .close-btn:hover {
                color: var(--accent-color);
            }

            .modal-content h2 {
                color: var(--accent-color);
                margin-bottom: 1.5rem;
                font-size: 1.8rem;
            }

            #orderForm {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            #orderForm label {
                color: #ccc;
                margin-bottom: 0.3rem;
                font-size: 0.9rem;
            }

            #orderForm input,
            #orderForm select {
                width: 100%;
                padding: 0.8rem;
                border: 1px solid #444;
                border-radius: 6px;
                background-color: #2a2a2a;
                color: white;
                font-size: 1rem;
                transition: border-color 0.3s;
            }

            #orderForm input:focus,
            #orderForm select:focus {
                border-color: var(--accent-color);
                outline: none;
            }

            #orderForm button[type="submit"] {
                background-color: var(--accent-color);
                color: black;
                padding: 1rem;
                border: none;
                border-radius: 6px;
                font-weight: bold;
                font-size: 1rem;
                cursor: pointer;
                transition: transform 0.2s, background-color 0.3s;
                margin-top: 1rem;
            }

            #orderForm button[type="submit"]:hover {
                transform: translateY(-2px);
                background-color: #b4ff33;
            }

            #orderForm button[type="submit"]:active {
                transform: translateY(0);
            }

            .form-group {
                position: relative;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .modal-content {
                    margin: 10% auto;
                    width: 95%;
                    padding: 1.5rem;
                }

                #orderForm input,
                #orderForm select {
                    padding: 0.7rem;
                }
            }

            /* Optional: Add icons to form fields */
            .input-icon {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #666;
            }
        </style>
    </head>

    <body>
        <!-- Header -->
        <header class="header">
            <a href="#" class="logo">UGTIX</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="events.php">Event</a>
                <a href="#">About</a>
            </nav>
            <a href="#" class="login-btn">Login</a>
        </header>

        <!-- Main Content -->
        <main class="event-container">
            <div class="event-header">
                <div class="event-image">
                    <img src="uploads/events/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>

                <div class="event-info">
                    <h1 class="event-title"><?php echo $row['nama']; ?></h1>

                    <div class="event-meta">
                        <div class="meta-item">
                            <span>📅 Tanggal:</span>
                            <span><?php echo $row['tanggal']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span>⏰ Waktu:</span>
                            <span><?php echo $row['waktu']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span>📍 Lokasi:</span>
                            <span><?php echo $row['lokasi']; ?></span>
                        </div>
                    </div>

                    <p class="event-price-tag">Harga: <?php echo $row['harga']; ?></p>

                    <div class="event-description">
                        <h2 class="description-title">Deskripsi Event</h2>
                        <div class="description-content">
                            <p><?php echo $row['deskripsi']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesan Tiket Button -->
            <button class="buy-btn">Pesan Tiket</button>
        </main>

        <!-- Modal Structure -->
        <div id="orderModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>🎫 Pesan Tiket</h2>
                <form id="orderForm">
                    <input type="hidden" name="id_event" value="<?php echo $event_id; ?>">

                    <div class="form-group">
                        <label for="email">📧 Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan email anda">
                    </div>

                    <div class="form-group">
                        <label for="nama">👤 Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="form-group">
                        <label for="npm">🎓 NPM</label>
                        <input type="text" id="npm" name="npm" required placeholder="Masukkan NPM">
                    </div>

                    <div class="form-group">
                        <label for="tanggal">📅 Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>

                    <div class="form-group">
                        <label for="metode">💳 Metode Pembayaran</label>
                        <select id="metode" name="metode" required>
                            <option value="" disabled selected>Pilih metode pembayaran</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="ewallet">E-Wallet</option>
                        </select>
                    </div>

                    <input type="hidden" name="total" value="<?php echo $row['harga']; ?>">

                    <button type="submit">Pesan Sekarang</button>
                </form>
            </div>
        </div>

        <!-- Add a success message div -->
        <div id="successMessage" style="display: none; position: fixed; top: 20px; right: 20px; background-color: var(--accent-color); color: black; padding: 1rem; border-radius: 6px; z-index: 1001;">
            Pesanan berhasil dibuat!
        </div>

        <script>
            // Get modal element
            var modal = document.getElementById("orderModal");
            var btn = document.querySelector(".buy-btn");
            var span = document.getElementsByClassName("close-btn")[0];

            // Open modal
            btn.onclick = function() {
                modal.style.display = "block";
            }

            // Close modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Enhanced form submission with success message
            document.getElementById("orderForm").onsubmit = function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                fetch('process/order.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const successMessage = document.getElementById('successMessage');
                            successMessage.style.display = 'block';

                            // Redirect after a short delay
                            setTimeout(() => {
                                window.location.href = 'invoice.php?id=' + data.order_id;
                            }, 1500); // 1.5 seconds delay
                        } else {
                            alert('Order failed to process: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your order');
                    });
            }
        </script>

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

<?php
} else {
    echo "Event not found.";
}

$conn->close();
?>