<header class="header">
    <a href="#" class="logo">UGTIX</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="events.php">Event</a>
        <a href="about.php">About</a>
        <a href="history.php">History</a>
    </nav>
    <a href="admin/login.php" class="login-btn">Login</a>
</header>

<style>
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
</style>