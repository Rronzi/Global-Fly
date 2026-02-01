<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : 0;

$message = '';
$flight = null;
if ($flight_id) {
    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $passengers = intval($_POST['passengers']);
    $class = htmlspecialchars($_POST['class']);
$class_multipliers = [
    'Basic Economy' => 1,
    'Economy' => 1.3,
    'Premium Economy' => 2.6,
    'Business' => 4.2,
    'First' => 6.3,
    'Ultra Luxury' => 10.5
];
$cost_per_passenger = $flight ? $flight['price'] * $class_multipliers[$class] : 190;
    $total_cost = $passengers * $cost_per_passenger;
    // Insert into database
    if ($flight) {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, flight_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $flight_id]);
    }
    $message = "Booking confirmed for flight to " . ($flight ? $flight['destination'] : 'Unknown') . " in $class class. Total cost: $$total_cost. Details: Name: $name, Email: $email, Phone: $phone, Passengers: $passengers.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
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
            <h2 style="text-align: center; font-size: 2em; margin-bottom: 30px;">Book Your Flight<?php if ($flight) echo ' to ' . htmlspecialchars($flight['destination']); ?></h2>
            
            <?php if ($message): ?>
                <div class="success-message" style="text-align: center; padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: max(40px, 8vw); align-items: start;">
                <form method="POST" action="" class="booking-form" style="background-color: #f9f9f9; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" style="display: block; margin-bottom: 8px; font-weight: bold;">Full Name:</label>
                        <input type="text" id="name" name="name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 8px; font-weight: bold;">Email Address:</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="phone" style="display: block; margin-bottom: 8px; font-weight: bold;">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="passengers" style="display: block; margin-bottom: 8px; font-weight: bold;">Number of Passengers:</label>
                        <input type="number" id="passengers" name="passengers" min="1" value="1" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="class" style="display: block; margin-bottom: 8px; font-weight: bold;">Flight Class:</label>
                        <select id="class" name="class" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                            <option value="Basic Economy">Basic Economy - $<?php echo $flight ? number_format($flight['price'] * 1, 0) : 190; ?></option>
                            <option value="Economy">Economy - $<?php echo $flight ? number_format($flight['price'] * 1.3, 0) : 250; ?></option>
                            <option value="Premium Economy">Premium Economy - $<?php echo $flight ? number_format($flight['price'] * 2.6, 0) : 500; ?></option>
                            <option value="Business">Business - $<?php echo $flight ? number_format($flight['price'] * 4.2, 0) : 800; ?></option>
                            <option value="First">First - $<?php echo $flight ? number_format($flight['price'] * 6.3, 0) : 1200; ?></option>
                            <option value="Ultra Luxury">Ultra Luxury - $<?php echo $flight ? number_format($flight['price'] * 10.5, 0) : 2000; ?></option>
                        </select>
                    </div>

                    <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
                    
                    <?php if ($flight): ?>
                    <p style="margin-bottom: 20px;"><strong>Flight Details:</strong> From <?php echo htmlspecialchars($flight['departure']); ?> to <?php echo htmlspecialchars($flight['destination']); ?> on <?php echo htmlspecialchars($flight['departure_date']); ?></p>
                    <?php endif; ?>
                    
                    <p id="total-price" style="margin-bottom: 20px; font-weight: bold; font-size: 1.2em;">Total Price: $0</p>
                    
                    <button type="submit" class="submit-btn" style="width: 100%; padding: 12px; background-color: #0A1A2F; color: orange; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; font-weight: bold;">Confirm Booking</button>
                </form>

                <div>
                    <h2 style="font-size: 2em; margin-bottom: 20px;">Flight Policies and Services</h2>
                    <p>At GlobalFly, our flight classes are designed to offer varying levels of comfort and service based on your budget. Each class comes with specific policies and amenities to ensure a tailored travel experience.</p>
                    
                    <p><strong>Basic Economy :</strong> Includes essential services such as seat selection, one checked bag, and basic in-flight refreshments. Cancellation policy allows changes up to 24 hours before departure with a fee.</p>
                    
                    <p><strong>Economy :</strong> Offers priority boarding, two checked bags, complimentary snacks and drinks, and flexible cancellation up to 48 hours before flight.</p>
                    
                    <p><strong>Premium Economy :</strong> Features extra legroom, three checked bags, gourmet meals, and lounge access. Cancellation and changes allowed up to 72 hours with minimal fees.</p>
                    
                    <p><strong>Business :</strong> Includes lie-flat seats, four checked bags, premium dining, private lounge, and full refund on cancellations up to 7 days before departure.</p>
                    
                    <p><strong>First Class :</strong> Provides ultimate luxury with personal concierge, unlimited checked bags, chef-prepared meals, and complimentary upgrades. Flexible policies with no fees for changes or cancellations.</p>
                    
                    <p><strong>Ultra Luxury :</strong> The pinnacle of travel with bespoke services, including private jet options, VIP ground transportation, and exclusive amenities. All policies are fully flexible with priority handling.</p>
                    
                    <p>All bookings are subject to our general terms: Baggage allowances vary by class, and additional fees may apply for overweight items. For more details, please review our full terms and conditions.</p>
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
    <script>
        var flightPrice = <?php echo $flight ? $flight['price'] : 0; ?>;
        var multipliers = {
            'Basic Economy': 1,
            'Economy': 1.3,
            'Premium Economy': 2.6,
            'Business': 4.2,
            'First': 6.3,
            'Ultra Luxury': 10.5
        };

        function updateTotal() {
            var selectedClass = document.getElementById('class').value;
            var passengers = parseInt(document.getElementById('passengers').value) || 1;
            var costPerPassenger = flightPrice * multipliers[selectedClass];
            var total = costPerPassenger * passengers;
            document.getElementById('total-price').textContent = 'Total Price: $' + total.toFixed(0);
        }

        document.getElementById('class').addEventListener('change', updateTotal);
        document.getElementById('passengers').addEventListener('input', updateTotal);
        updateTotal(); // Initial call
    </script>
</body>
</html>