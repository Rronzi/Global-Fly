<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ---- Load flight ----
// Accept `flight_id` from GET when arriving, or from POST when submitting the form
$flight_id = 0;
if (isset($_GET['flight_id'])) {
    $flight_id = (int)$_GET['flight_id'];
} elseif (isset($_POST['flight_id'])) {
    $flight_id = (int)$_POST['flight_id'];
}

$message = '';
$error   = '';
$flight  = null;

if ($flight_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flight) {
        $error = 'The selected flight could not be found.';
    } else {
        if (($flight['status'] ?? 'active') === 'cancelled') {
            $error = 'This flight has been cancelled. Reason: ' . ($flight['cancellation_reason'] ?? 'Unspecified') . '.';
        }

        // Only process form input when the request is a POST (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Raw values; escape only when outputting
            $name       = trim($_POST['name'] ?? '');
            $email      = trim($_POST['email'] ?? '');
            $phone      = trim($_POST['phone'] ?? '');
            $passengers = isset($_POST['passengers']) ? (int)$_POST['passengers'] : 0;
            $class      = $_POST['class'] ?? '';
            $posted_flight_id = isset($_POST['flight_id']) ? (int)$_POST['flight_id'] : 0;

            // Validation
            if ($posted_flight_id !== $flight_id) {
                $error = 'Invalid flight selection.';
            } elseif (!$flight) {
                $error = 'Cannot book this flight because it does not exist.';
            } elseif ($passengers < 1) {
                $error = 'Number of passengers must be at least 1.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
        $class_multipliers = [
            'Basic Economy'   => 1,
            'Economy'         => 1.3,
            'Premium Economy' => 2.6,
            'Business'        => 4.2,
            'First'           => 6.3,
            'Ultra Luxury'    => 10.5
        ];

        if (!array_key_exists($class, $class_multipliers)) {
            $error = 'Invalid class selection.';
        } else {
            $cost_per_passenger = (float)$flight['price'] * $class_multipliers[$class];
            $total_cost = $passengers * $cost_per_passenger;

            try {
                $stmt = $conn->prepare("
                    INSERT INTO bookings (user_id, flight_id, name, email, phone, passengers, class, total_cost, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $flight_id,
                    $name,
                    $email,
                    $phone,
                    $passengers,
                    $class,
                    $total_cost
                ]);

                $safe_destination = htmlspecialchars($flight['destination'], ENT_QUOTES, 'UTF-8');
                $safe_class       = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
                $message = "Booking confirmed for flight to {$safe_destination} in {$safe_class} class. Total cost: $" . number_format($total_cost, 2) . ".";
            } catch (PDOException $e) {
                // In production, log $e
                $error = 'An error occurred while saving your booking. Please try again later.';
            }
        }
    }
    }
}
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

    <main>
        <div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
            <h2 style="text-align: center; font-size: 2em; margin-bottom: 30px;">
                Book Your Flight<?php if ($flight) echo ' to ' . htmlspecialchars($flight['destination'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>

            <?php if (!empty($error)): ?>
                <div class="error-message" style="text-align: center; padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="success-message" style="text-align: center; padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="booking-grid container">
                <form method="POST" action="" class="booking-form">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="name" style="display: block; margin-bottom: 8px; font-weight: bold;">Full Name:</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="email" style="display: block; margin-bottom: 8px; font-weight: bold;">Email Address:</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="phone" style="display: block; margin-bottom: 8px; font-weight: bold;">Phone Number:</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            required
                            value="<?php echo isset($phone) ? htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') : ''; ?>"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="passengers" style="display: block; margin-bottom: 8px; font-weight: bold;">Number of Passengers:</label>
                        <input
                            type="number"
                            id="passengers"
                            name="passengers"
                            min="1"
                            value="<?php echo isset($passengers) && $passengers > 0 ? (int)$passengers : 1; ?>"
                            required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="class" style="display: block; margin-bottom: 8px; font-weight: bold;">Flight Class:</label>
                        <select id="class" name="class" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; box-sizing: border-box;">
                            <?php
                                $selectedClass = isset($class) ? $class : 'Basic Economy';
                            ?>
                            <option value="Basic Economy" <?php echo $selectedClass === 'Basic Economy' ? 'selected' : ''; ?>>
                                Basic Economy - $<?php echo $flight ? number_format($flight['price'] * 1, 0) : 190; ?>
                            </option>
                            <option value="Economy" <?php echo $selectedClass === 'Economy' ? 'selected' : ''; ?>>
                                Economy - $<?php echo $flight ? number_format($flight['price'] * 1.3, 0) : 250; ?>
                            </option>
                            <option value="Premium Economy" <?php echo $selectedClass === 'Premium Economy' ? 'selected' : ''; ?>>
                                Premium Economy - $<?php echo $flight ? number_format($flight['price'] * 2.6, 0) : 500; ?>
                            </option>
                            <option value="Business" <?php echo $selectedClass === 'Business' ? 'selected' : ''; ?>>
                                Business - $<?php echo $flight ? number_format($flight['price'] * 4.2, 0) : 800; ?>
                            </option>
                            <option value="First" <?php echo $selectedClass === 'First' ? 'selected' : ''; ?>>
                                First - $<?php echo $flight ? number_format($flight['price'] * 6.3, 0) : 1200; ?>
                            </option>
                            <option value="Ultra Luxury" <?php echo $selectedClass === 'Ultra Luxury' ? 'selected' : ''; ?>>
                                Ultra Luxury - $<?php echo $flight ? number_format($flight['price'] * 10.5, 0) : 2000; ?>
                            </option>
                        </select>
                    </div>

                    <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">

                    <?php if ($flight): ?>
                        <p style="margin-bottom: 20px;">
                            <strong>Flight Details:</strong>
                            From <?php echo htmlspecialchars($flight['departure'], ENT_QUOTES, 'UTF-8'); ?>
                            to <?php echo htmlspecialchars($flight['destination'], ENT_QUOTES, 'UTF-8'); ?>
                            on <?php echo htmlspecialchars($flight['departure_date'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
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
    <script src="js/nav.js"></script>
    <script>
        var flightPrice = <?php echo $flight ? (float)$flight['price'] : 0; ?>;
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