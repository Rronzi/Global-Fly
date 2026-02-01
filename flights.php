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
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="#top">Flights</a></li>
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
            <div class="rubrika">
                <img src="photos/BasicEconomy.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>Basic Economy class</p>
                    <p>Cost: 190$</p>
                    <button>Book Now</button>
                </div>
            </div>
            <div class="rubrika">
                <img src="photos/economy.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>Economy class</p>
                    <p>Cost: 250$</p>
                    <button>Book Now</button>
                </div>
            </div>
            <div class="rubrika">
                <img src="photos/PremiumEconomy.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>Premium Economy class</p>
                    <p>Cost: 500$</p>
                    <button>Book Now</button>
                </div>
            </div>
            <div class="rubrika">
                <img src="photos/BusinessClass.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>Business class</p>
                    <p>Cost: 800$</p>
                    <button>Book Now</button>
                </div>
            </div>
            <div class="rubrika">
                <img src="photos/FirstClass.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>First class</p>
                    <p>Cost: 1200$</p>
                    <button>Book Now</button>
                </div>
            </div>
            <div class="rubrika">
                <img src="photos/UltraLuxury.jpg" alt="" class="img">
                <div class="qyteti">
                    <p>Ultra Luxury class</p>
                    <p>Cost: 2000$</p>
                    <button>Book Now</button>
                </div>
            </div>
            
            
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