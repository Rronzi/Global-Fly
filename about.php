<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AboutUs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="about-body">
    <header>
        <nav class="header">
            <div class="logo" onclick="location.href='index.php'">
                <img src="photos/logo.png" alt="Global Fly Logo" width="80" height="80">
                <h1>Global Fly</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="flights.php">Flights</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
                <li><a href="contact_us.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>
    <main class="about-main">
        <section class="section">
            <h2>About Us</h2>

            <p>Welcome to GlobalFly, a modern travel company dedicated to redefining the way the world moves. Founded on the belief that travel should be simple, inspiring, and accessible to everyone, GlobalFly has grown into a trusted partner for individuals, families, and businesses searching for seamless global journeys.</p>
            <p>At the heart of GlobalFly lies a passion for exploration. We believe that every trip—whether it's a short domestic flight or an international adventure—has the power to broaden horizons, connect people, and create meaningful memories. Our mission is to make these experiences easier to reach by offering a platform that combines innovative technology, real-time information, and exceptional customer care.</p>
            <p>Over the years, we have developed a service model focused on reliability, transparency, and convenience. Our tools are designed to help travelers find the best flight options, compare destinations, access detailed travel information, and receive 24/7 support from a dedicated team of experts. Every feature we introduce is centered around helping our customers travel with confidence.</p>
            <p>GlobalFly is more than just a booking service—we are a community of explorers. We listen to the needs of modern travelers and continuously enhance our system to meet the demands of an evolving world. Whether you're planning a business trip, a dream vacation, or a last-minute getaway, we work to ensure that the journey is just as enjoyable and stress-free as the destination.</p>
            <p>Our vision is to become a global leader in travel innovation by staying true to our core values: quality, trust, and customer-first service. With GlobalFly, every journey begins with clarity, comfort, and the promise of a smooth travel experience from takeoff to landing.</p>
            
            <h3><a href="contact_us.php" style="text-decoration: none; color: inherit;">Contact Us</a></h3>
            <p>If you have any questions or need assistance, feel free to reach out to our customer support team.</p>
            <div class="about-apps">
                <a href="#"><img src="photos/facebook.png" alt=""></a>
                <a href="#"><img src="photos/twitter.png" alt=""></a>
                <a href="#"><img src="photos/instagram.png" alt=""></a>
                <a href="#"><img src="photos/pinterest.png" alt=""></a>
            </div>
        </section>
        <div class ="about-image">
            <img src="photos/logo.png" alt="Fotografia e kompanise" width="500" height="500" style="display: block; margin: auto;">
        </div>
    </main>
    <footer class="footer">
        <p>&copy; 2025 Global Fly</p>
        <ul class="footer-links">
        
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="#help">Help</a></li>
        </ul>
        
    </footer>
</body>
</html>