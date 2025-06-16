<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="header-container">
        <div class="logo">
            <h1>📚 My Library</h1>
        </div>

        <input type="checkbox" id="menu-toggle" />
        <label for="menu-toggle" class="menu-icon">☰</label>

        <nav class="nav-links">
            <a href="library.php">Library</a>
            <a href="upload.php">➕ Add Book</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php" class="logout">Logout</a>
            <span class="user">👋 <?= htmlspecialchars($_SESSION["user_name"] ?? "User") ?></span>
        </nav>
    </div>
</header>

<style>
    header {
        background: #004d40;
        color: white;
        padding: 1rem 2rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: 0 auto;
    }

    .logo h1 {
        font-size: 1.6rem;
        margin: 0;
        font-weight: 700;
    }

    nav.nav-links {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    nav a {
        background: #00695c;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    nav a.logout {
        background: #d32f2f;
    }

    nav a:hover {
        background: #00897b;
    }

    nav a.logout:hover {
        background: #b71c1c;
    }

    .user {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        color: #e0f2f1;
    }

    /* Toggle menu button */
    .menu-icon {
        display: none;
        font-size: 1.5rem;
        cursor: pointer;
        user-select: none;
    }

    #menu-toggle {
        display: none;
    }

    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .menu-icon {
            display: block;
        }

        .nav-links {
            display: none;
            flex-direction: column;
            width: 100%;
            margin-top: 0.5rem;
        }

        #menu-toggle:checked+.menu-icon+.nav-links {
            display: flex;
        }

        nav a,
        .user {
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            border-radius: 0;
        }

        nav a.logout {
            margin-top: 0.5rem;
        }
    }
</style>