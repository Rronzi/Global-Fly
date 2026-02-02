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
    <title>Flights</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="page1-body">
   <header>
        <nav class="header">
            <div class="logo" onclick="location.href='index.php'">
                <img src="photos/logo.png" alt="Global Fly Logo" width="80" height="80">
                <h1>Global Fly</h1>
            </div>
            <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="#top">Flights</a></li>
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
    <main>
        <div class="bgfoto1" id="bgfoto1">
            <button id="prevBtn" class="slider-btn prev-btn">&lt;</button>
            <button id="nextBtn" class="slider-btn next-btn">&gt;</button>
        </div>
       
        <div class="above-fotografite">
            <h3>Flights</h3>
        </div>
        <div class="fotografite">
            <?php
            $search = trim($_GET['q'] ?? '');
            if ($search !== '') {
                $like = '%' . str_replace('%','\%',$search) . '%';
                $stmt = $conn->prepare("SELECT * FROM flights WHERE destination LIKE ? OR departure LIKE ? ORDER BY departure_date");
                $stmt->execute([$like, $like]);
            } else {
                $stmt = $conn->prepare("SELECT * FROM flights ORDER BY departure_date");
                $stmt->execute();
            }
            $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($flights)) {
                echo '<p style="padding:20px;">No flights found matching "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '".</p>';
            }
            foreach ($flights as $flight) {
                $formatted_date = date('F j, Y \a\t g:i A', strtotime($flight['departure_date']));
                echo '<div class="rubrika">';
                echo '<img src="' . htmlspecialchars($flight['image']) . '" alt="' . htmlspecialchars($flight['destination']) . '" class="img">';
                echo '<div class="qyteti" style="width: 400px;">';
                echo '<p style="padding: 5px; margin: 0; font-weight: bold;">To: ' . htmlspecialchars($flight['destination']) . '</p>';
                echo '<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">';
                echo '<tr>';
                echo '<td style="padding: 5px; font-weight: bold;">From: ' . htmlspecialchars($flight['departure']) . '</td>';
                echo '<td style="padding: 5px; font-weight: bold;">Departs: ' . htmlspecialchars($formatted_date) . '</td>';
                echo '</tr>';
                echo '<tr>';
                echo '<td style="padding: 5px; font-weight: bold;">Price: $' . htmlspecialchars($flight['price']) . '</td>';
                if (($flight['status'] ?? 'active') === 'cancelled') {
                    echo '<td style="padding: 5px;"><span style="color:#c00;font-weight:bold;">Cancelled</span>';
                    $reason = htmlspecialchars($flight['cancellation_reason'] ?? 'Unspecified', ENT_QUOTES, 'UTF-8');
                    if ($reason) { echo '<div style="font-size:0.9em;color:#666;">Reason: ' . $reason . '</div>'; }
                    echo '</td>';
                } else {
                    echo '<td style="padding: 5px;">';
                    echo '<form action="booking.php" method="get" style="display:inline;">';
                    echo '<input type="hidden" name="flight_id" value="' . $flight['id'] . '">';
                    echo '<button type="submit">Book Now</button>';
                    echo '</form>';
                    echo '</td>';
                }
                echo '</tr>';
                echo '</table>';
                echo '</div>';
                echo '</div>';
            }
            ?>
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
</footer>
    <script src="js/nav.js"></script>
</body>
<script>
let fotot = ['photos/parisi.png', 'photos/roma.png', 'photos/jesusi.jpg', 'photos/liberty.jpg', 'photos/piramidat.jpg', 'photos/mahali.jpg', 'photos/wallofchina.jpg','photos/meksika.jpg'];
let index = 0;

function changeBackground() {
    document.getElementById('bgfoto1').style.backgroundImage = 'url(' + fotot[index] + ')';
}

function nextImage() {
    index = (index + 1) % fotot.length;
    changeBackground();
}

function prevImage() {
    index = (index - 1 + fotot.length) % fotot.length;
    changeBackground();
}

changeBackground();

document.getElementById('nextBtn').addEventListener('click', nextImage);
document.getElementById('prevBtn').addEventListener('click', prevImage);
</script>
</html>