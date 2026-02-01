<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $message = isset($_POST['message']) ? trim($_POST['message']) : "";

    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                $success_message = "Thank you for your message! We will get back to you soon.";
                $name = "";
                $email = "";
                $message = "";
            } else {
                $error_message = "An error occurred. Please try again later.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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
            <h2 style="text-align: center; font-size: 2em; margin-bottom: 30px;">Send us a Message</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message" style="text-align: center; padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="text-align: center; padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: max(40px, 8vw); align-items: start;">
                <form method="POST" action="contact_us.php" class="contact-form" style="background-color: #f9f9f9; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" style="display: block; margin-bottom: 8px; font-weight: bold;">Full Name:</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 8px; font-weight: bold;">Email Address:</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="message" style="display: block; margin-bottom: 8px; font-weight: bold;">Message:</label>
                        <textarea id="message" name="message" rows="8" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box; font-family: Arial, sans-serif;"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn" style="width: 100%; padding: 12px; background-color: #0A1A2F; color: orange; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; font-weight: bold;">Send Message</button>
                </form>

                <div>
                    <h2 style="font-size: 2em; margin-bottom: 20px;">Get in Touch</h2>
                    <p>Have questions or feedback? We'd love to hear from you. Fill out the form below and our team will respond as soon as possible.</p>
                    
                    <p>At GlobalFly, we are committed to providing exceptional customer service and support. Whether you have questions about booking flights, need assistance with your account, or simply want to share your travel experience, our dedicated support team is here to help.</p>
                    
                    <p>We pride ourselves on quick response times and personalized attention to each inquiry. Your satisfaction is our priority, and we work tirelessly to ensure that every customer receives the support they need for a smooth and enjoyable travel experience.</p>
                    
                    <div class="contact-details" style="margin-top: 30px;">
                        <h3>Contact Information</h3>
                        <p><strong>Email:</strong> support@globalfly.com</p>
                        <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                        <p><strong>Address:</strong> 123 Aviation Street, Sky City, SC 12345</p>
                        <p><strong>Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM (EST)</p>
                    </div>

                    <div class="about-apps" style="margin-top: 20px;">
                        <a href="#"><img src="photos/facebook.png" alt="Facebook"></a>
                        <a href="#"><img src="photos/twitter.png" alt="Twitter"></a>
                        <a href="#"><img src="photos/instagram.png" alt="Instagram"></a>
                        <a href="#"><img src="photos/pinterest.png" alt="Pinterest"></a>
                    </div>
                </div>
            </div>
        </div>

    <footer class="footer">
        <p>&copy; 2025 Global Fly</p>
        <ul class="footer-links">
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </footer>
</body>
</html>
