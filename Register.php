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
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="flights.php">Flights</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
   
    <div class="register">
        <br>
        <img alt="Icon" src="photos/43916.jpg" width="125" style="border: 2px solid black;"><br><br><br>
        
        
        <input type="text" placeholder="Username" style="background-color: rgb(253, 245, 245);">
        <input type="text" placeholder="First Name" style="background-color: rgb(253, 245, 245);">
        <input type="text" placeholder="Last Name" style="background-color: rgb(253, 245, 245);">
        <input type="email" placeholder="Email" style="background-color: rgb(253, 245, 245);">
        <input type="password" placeholder="Password" style="background-color: rgb(253, 245, 245);">
        
        
        <br>
        <button type="submit"><a href="index.php">Log in</a></button>
        <p>Nuk deshironi te regjistroheni?</p>
        <button><a href="index.php">Go Back</a></button>
        <br>

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

    
</body>
</html>