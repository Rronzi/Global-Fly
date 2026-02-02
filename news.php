<?php
session_start();
// Enable temporary error reporting for debugging (remove after issue is fixed)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "database.php";
$db = new Database();
$conn = $db->getConnection();
if (!$conn) { echo 'Database connection failed. Check your database settings.'; exit; }

$stmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC");
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
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
        <div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
            <h2 style="text-align: center; font-size: 2em; margin-bottom: 30px;">Latest News</h2>
            
            <?php if (empty($news)): ?>
                <p style="text-align: center;">No news available at the moment.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($news as $item): ?>
                        <div style="background-color: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 15px;">
                            <?php endif; ?>
                            <h3 style="font-size: 1.5em; margin-bottom: 10px;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p style="color: #666; font-size: 0.9em; margin-bottom: 10px;">Published on <?php echo date('F j, Y', strtotime($item['created_at'])); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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