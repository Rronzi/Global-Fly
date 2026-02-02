<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = 'user';

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$username, $email, $password, $role]);
        
        $query = "SELECT * FROM users WHERE username = ?";
        $select_stmt = $conn->prepare($query);
        $select_stmt->execute([$username]);
        $user = $select_stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "User ekziston!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
   
    <div class="register">
        <br>
        <img alt="Icon" src="photos/43916.jpg" width="125" style="border: 2px solid black;"><br><br><br>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" style="background-color: rgb(253, 245, 245);" required>
            <input type="email" name="email" placeholder="Email" style="background-color: rgb(253, 245, 245);" required>
            <input type="password" name="password" placeholder="Password" style="background-color: rgb(253, 245, 245);" required>
            <button type="submit" style="margin-top: 15px;color: orange;">Register</button>
        </form>
        
        <br>
        <p>Already have an account? <a href="login.php">Login here</a></p>
        <button><a href="index.php">Go Back</a></button>
        

    </div>
   
    <div class="middle">
        <img src="photos/dubai.webp" class="background2">
    </div>
   
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