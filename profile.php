<?php
session_start();
require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stats = [];
if ($user['role'] === 'admin') {
    $tables = ['users', 'flights', 'news', 'bookings'];
    foreach ($tables as $table) {
        $count_query = "SELECT COUNT(*) as count FROM " . $table;
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->execute();
        $result = $count_stmt->fetch(PDO::FETCH_ASSOC);
        $stats[$table] = $result['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="profile-container">
        <?php if ($user['role'] === 'admin'): ?>
            <!-- ADMIN DASHBOARD -->
            <div class="profile-box" style="max-width: 800px;">
                <h2>Admin Dashboard</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                    <div class="profile-item" style="text-align: center;">
                        <label>Total Users</label>
                        <p style="font-size: 24px; font-weight: bold; color: #0A1A2F; margin: 10px 0;"><?php echo $stats['users']; ?></p>
                    </div>

                    <div class="profile-item" style="text-align: center;">
                        <label>Total Flights</label>
                        <p style="font-size: 24px; font-weight: bold; color: #0A1A2F; margin: 10px 0;"><?php echo $stats['flights']; ?></p>
                    </div>

                    <div class="profile-item" style="text-align: center;">
                        <label>Total News</label>
                        <p style="font-size: 24px; font-weight: bold; color: #0A1A2F; margin: 10px 0;"><?php echo $stats['news']; ?></p>
                    </div>

                    <div class="profile-item" style="text-align: center;">
                        <label>Total Bookings</label>
                        <p style="font-size: 24px; font-weight: bold; color: #0A1A2F; margin: 10px 0;"><?php echo $stats['bookings']; ?></p>
                    </div>
                </div>

                <div class="profile-item">
                    <label>Admin User: <?php echo htmlspecialchars($user['username']); ?></label>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <button onclick="location.href='index.php'">Go Back Home</button>
            </div>
        <?php else: ?>
            <!-- REGULAR USER PROFILE -->
            <div class="profile-box">
                <h2>My Profile</h2>
                
                <div class="profile-item">
                    <label>Username:</label>
                    <p><?php echo htmlspecialchars($user['username']); ?></p>
                </div>

                <div class="profile-item">
                    <label>Email:</label>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <div class="profile-item">
                    <label>Role:</label>
                    <p><?php echo ucfirst($user['role']); ?></p>
                </div>

                <div class="profile-item">
                    <label>Member Since:</label>
                    <p><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                </div>

                <button onclick="location.href='index.php'">Go Back Home</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="middle">
        <img src="photos/background1.jpg" class="background1">
    </div>

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