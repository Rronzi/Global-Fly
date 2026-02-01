<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username ose password gabim!";
        }
    } else {
        $error = "Te lutem plotesoni te gjitha fushat!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
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
    <div class="login">
        <br>
        <img  alt="Icon" src="photos/login.png" width="125" style="border: 2px solid black;">
        
        <br><br><br><br>
  
        <form method="POST">
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required>
            <br>
            <button type="submit" style="color: white;">Log in</button>
            <p>Shkoni te regjistrimi nëse nuk keni llogari.</p>
            <button type="button"><a href="Register.php">Regjistrohu</a></button>
            <br>
        </form>
   
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