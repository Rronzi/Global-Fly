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
    <title>Global Fly</title>
    <link rel="stylesheet" href="style.css">
</head> 
<body class="index-body">
    <!--Headeri/navbari-->
    <header>
        <nav class="header">
            <div class="logo" onclick="window.location.href='#top'">
                <img src="photos/logo.png" alt="Global Fly Logo" width="80" height="80">
                <h1>Global Fly</h1>
            </div>
                <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
            <ul class="nav-links">
                <li><a href="#top">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="flights.php">Flights</a></li>
                <li><a href="news.php">News</a></li>
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
    <!--permbajtja kryesore-->
    <main>
        <div class="bgfoto">
            <form action="flights.php" method="get" role="search" aria-label="Search flights">
                <input type="search" name="q" class="search-input" placeholder="Search by destination or departure city" aria-label="Search flights">
                <button type="submit" class="search-btn">Search flights</button>
            </form>
        </div>
        <div class="above-fotografite">
            <h3>Suggestions</h3>
        </div>
        <div class="fotografite">
            <?php
            // Select a small random set of flights to show as suggestions
            try {
                $stmt = $conn->prepare("SELECT * FROM flights ORDER BY RAND() LIMIT 9");
                $stmt->execute();
                $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $suggestions = [];
            }

            if (empty($suggestions)) {
                echo '<p style="padding:20px;">No suggestions available right now.</p>';
            } else {
                foreach ($suggestions as $s) {
                    $dest = htmlspecialchars($s['destination'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
                    $img  = htmlspecialchars($s['image'] ?? 'photos/default.jpg', ENT_QUOTES, 'UTF-8');
                    $rating = mt_rand(40,50) / 10; // random rating 4.0 - 5.0
                    echo '<div class="rubrika">';
                    echo '<img src="' . $img . '" alt="' . $dest . '" class="img">';
                    echo '<div class="qyteti">';
                    echo '<p>' . $dest . '</p>';
                    echo '<p>Rating: ' . number_format($rating, 1) . '/5★</p>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </main>
    
    <!--Footeri-->
    <footer class="footer">
        <p>&copy; 2025 Global Fly</p>
        <ul class="footer-links">
        
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="#help">Help</a></li>
        </ul>
        
</footer>
    <script src="js/nav.js"></script>
</body>
</html>