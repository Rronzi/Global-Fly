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

$bookings = [];
if ($user['role'] !== 'admin') {
    $bq = "SELECT b.*, f.departure, f.destination, f.departure_date
           FROM bookings b
           LEFT JOIN flights f ON b.flight_id = f.id
           WHERE b.user_id = ?
           ORDER BY b.id DESC";
    $bst = $conn->prepare($bq);
    $bst->execute([$_SESSION['user_id']]);
    $bookings = $bst->fetchAll(PDO::FETCH_ASSOC);
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
            <button class="nav-toggle" aria-label="Toggle navigation">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="flights.php">Flights</a></li>
                <li><a href="news.php">News</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="contact_us.php">Contact Us</a></li>
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

                <div style="display:flex; gap:12px; margin-top:12px;">
                    <button onclick="location.href='admin.php'">Dashboard</button>
                    <button onclick="location.href='index.php'">Go Back Home</button>
                </div>
            </div>
        <?php else: ?>
            <!-- REGULAR USER PROFILE -->
            <div class="profile-box" style="max-width:360px; flex:0 0 360px; margin-right:12px;">
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
            
            <div class="profile-box" style="flex:1; max-width:820px; min-width:300px; margin-top: 0;">
                <h2>My Bookings</h2>
                <?php if (empty($bookings)): ?>
                    <p>You have no bookings yet. Browse available <a href="flights.php">flights</a> to book.</p>
                <?php else: ?>
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background:#f0f0f0; text-align:left;">
                                <th style="padding:8px; border:1px solid #ddd">Booking #</th>
                                <th style="padding:8px; border:1px solid #ddd">Flight</th>
                                <th style="padding:8px; border:1px solid #ddd">Departure</th>
                                <th style="padding:8px; border:1px solid #ddd">Class</th>
                                <th style="padding:8px; border:1px solid #ddd">Passengers</th>
                                <th style="padding:8px; border:1px solid #ddd">Total</th>
                                <th style="padding:8px; border:1px solid #ddd">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo (int)$b['id']; ?></td>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo htmlspecialchars(($b['departure'] ?? 'Unknown') . ' → ' . ($b['destination'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo htmlspecialchars($b['departure_date'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo htmlspecialchars($b['class'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo (int)$b['passengers']; ?></td>
                                    <td style="padding:8px; border:1px solid #ddd">$<?php echo number_format((float)$b['total_cost'], 2); ?></td>
                                    <td style="padding:8px; border:1px solid #ddd"><?php echo htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
</footer>
    <script src="js/nav.js"></script>
</body>
</html>