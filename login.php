<?php
require_once "includes/Database.php";
$db = new Database();
$conn = $db->getConnection();
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
                <li><a href="#top">Login</a></li>
            </ul>
        </nav>
      </header>
      <div class="login">
        <br>
        <img  alt="Icon" src="photos/login.png" width="125" style="border: 2px solid black;">
        
        <br><br><br><br>
  
        <input type="text" placeholder="Username"><br>
            
        <input type="password" placeholder="Password">
        <br>
        <button type="submit"><a href="index.php">Log in</a></button>
        <p>Shkoni te regjistrimi nëse nuk keni llogari.</p>
        <button><a href="Register.php">Regjistrohu</a></button>
        <br>
   
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