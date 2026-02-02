<?php
session_start();
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

$section = $_GET['section'] ?? 'flights';
$message = '';
$error = '';

// Ensure photos directory exists
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// --- Flights CRUD handlers ---
if ($section === 'flights') {
    // Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        // fetch image path to unlink
        $s = $conn->prepare('SELECT image FROM flights WHERE id = ?');
        $s->execute([$id]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $stmt = $conn->prepare('DELETE FROM flights WHERE id = ?');
            $stmt->execute([$id]);
            if (!empty($row['image'])) {
                $imgPath = __DIR__ . DIRECTORY_SEPARATOR . $row['image'];
                if (file_exists($imgPath)) {@unlink($imgPath);}    
            }
            $message = 'Flight deleted.';
        } else {
            $error = 'Flight not found.';
        }
    }

    // Add / Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_flight'])) {
        $fid = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $flight_number = trim($_POST['flight_number'] ?? '');
        $departure = trim($_POST['departure'] ?? '');
        $destination = trim($_POST['destination'] ?? '');
        $departure_date = trim($_POST['departure_date'] ?? '');
        $price = (int)($_POST['price'] ?? 0);

        // handle image upload
        $imagePath = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $info = @getimagesize($tmp);
            if ($info === false) {
                $error = 'Uploaded file is not a valid image.';
            } else {
                $ext = image_type_to_extension($info[2], false);
                $safe = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . $safe;
                if (move_uploaded_file($tmp, $dest)) {
                    // store relative path
                    $imagePath = 'photos/' . $safe;
                } else {
                    $error = 'Failed to move uploaded file.';
                }
            }
        }

        if (empty($error)) {
            if ($fid) {
                // update; if new image uploaded, replace
                if ($imagePath) {
                    // fetch old image to unlink
                    $old = $conn->prepare('SELECT image FROM flights WHERE id = ?');
                    $old->execute([$fid]);
                    $orow = $old->fetch(PDO::FETCH_ASSOC);
                    if ($orow && !empty($orow['image'])) {
                        $oldpath = __DIR__ . DIRECTORY_SEPARATOR . $orow['image'];
                        if (file_exists($oldpath)) {@unlink($oldpath);}    
                    }
                    $q = 'UPDATE flights SET flight_number = ?, departure = ?, destination = ?, departure_date = ?, price = ?, image = ?, created_by = ? WHERE id = ?';
                    $stmt = $conn->prepare($q);
                    $stmt->execute([$flight_number, $departure, $destination, $departure_date, $price, $imagePath, $_SESSION['user_id'], $fid]);
                } else {
                    $q = 'UPDATE flights SET flight_number = ?, departure = ?, destination = ?, departure_date = ?, price = ?, created_by = ? WHERE id = ?';
                    $stmt = $conn->prepare($q);
                    $stmt->execute([$flight_number, $departure, $destination, $departure_date, $price, $_SESSION['user_id'], $fid]);
                }
                $message = 'Flight updated.';
            } else {
                $q = 'INSERT INTO flights (flight_number, departure, destination, departure_date, price, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)';
                $stmt = $conn->prepare($q);
                $stmt->execute([$flight_number, $departure, $destination, $departure_date, $price, $imagePath ?? '', $_SESSION['user_id']]);
                $message = 'Flight added.';
            }
        }
    }

    // Fetch flights for list or edit
    $flights = $conn->query('SELECT * FROM flights ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    $editFlight = null;
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $eid = (int)$_GET['id'];
        $st = $conn->prepare('SELECT * FROM flights WHERE id = ?');
        $st->execute([$eid]);
        $editFlight = $st->fetch(PDO::FETCH_ASSOC);
    }
}

// --- News CRUD handlers ---
if ($section === 'news') {
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $s = $conn->prepare('SELECT image FROM news WHERE id = ?');
        $s->execute([$id]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $conn->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
            if (!empty($row['image'])) { $imgPath = __DIR__ . DIRECTORY_SEPARATOR . $row['image']; if (file_exists($imgPath)) {@unlink($imgPath);} }
            $message = 'News deleted.';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
        $nid = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $imagePath = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $info = @getimagesize($tmp);
            if ($info === false) { $error = 'Uploaded file is not a valid image.'; }
            else {
                $ext = image_type_to_extension($info[2], false);
                $safe = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . $safe;
                if (move_uploaded_file($tmp, $dest)) { $imagePath = 'photos/' . $safe; } else { $error = 'Failed to move uploaded file.'; }
            }
        }
        if (empty($error)) {
            if ($nid) {
                if ($imagePath) {
                    $old = $conn->prepare('SELECT image FROM news WHERE id = ?'); $old->execute([$nid]); $orow = $old->fetch(PDO::FETCH_ASSOC);
                    if ($orow && !empty($orow['image'])) { $oldpath = __DIR__ . DIRECTORY_SEPARATOR . $orow['image']; if (file_exists($oldpath)) {@unlink($oldpath);} }
                    $conn->prepare('UPDATE news SET title=?,content=?,image=?,created_by=? WHERE id=?')->execute([$title,$content,$imagePath,$_SESSION['user_id'],$nid]);
                } else { $conn->prepare('UPDATE news SET title=?,content=?,created_by=? WHERE id=?')->execute([$title,$content,$_SESSION['user_id'],$nid]); }
                $message = 'News updated.';
            } else {
                $conn->prepare('INSERT INTO news (title,content,image,created_by) VALUES (?,?,?,?)')->execute([$title,$content,$imagePath ?? '',$_SESSION['user_id']]);
                $message = 'News added.';
            }
        }
    }

    $newsList = $conn->query('SELECT * FROM news ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    $editNews = null;
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) { $eid=(int)$_GET['id']; $st=$conn->prepare('SELECT * FROM news WHERE id=?'); $st->execute([$eid]); $editNews=$st->fetch(PDO::FETCH_ASSOC); }
}

// --- Users management ---
if ($section === 'users') {
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        // prevent deleting self
        if ($id === (int)$_SESSION['user_id']) { $error = 'Cannot delete your own account.'; }
        else { $conn->prepare('DELETE FROM users WHERE id = ?')->execute([$id]); $message = 'User deleted.'; }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
        $uid = (int)($_POST['id'] ?? 0);
        $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
        if ($uid > 0) { $conn->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$role, $uid]); $message = 'User updated.'; }
    }

    // Create new user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_new_user'])) {
        $uname = trim($_POST['username'] ?? '');
        $uemail = trim($_POST['email'] ?? '');
        $upass = $_POST['password'] ?? '';
        $urole = ($_POST['role'] === 'admin') ? 'admin' : 'user';
        if ($uname === '' || $uemail === '' || $upass === '') {
            $error = 'Please fill username, email and password.';
        } else {
            $hash = password_hash($upass, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)');
            $ins->execute([$uname, $uemail, $hash, $urole]);
            $message = 'User created.';
        }
    }

    $users = $conn->query('SELECT id,username,email,role,created_at FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

// --- Bookings management ---
if ($section === 'bookings') {
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id']; $conn->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]); $message = 'Booking deleted.';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_booking_status'])) {
        $bid = (int)($_POST['id'] ?? 0);
        $status = in_array($_POST['status'], ['pending','confirmed','cancelled']) ? $_POST['status'] : 'pending';
        if ($bid > 0) { $conn->prepare('UPDATE bookings SET status = ? WHERE id = ?')->execute([$status, $bid]); $message = 'Booking status updated.'; }
    }

    // Create new booking
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_booking'])) {
        $uid = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $fid = isset($_POST['flight_id']) ? (int)$_POST['flight_id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $passengers = max(1, min(9, (int)($_POST['passengers'] ?? 1)));
        $class = trim($_POST['class'] ?? 'Basic Economy');
        $total_cost = (float)($_POST['total_cost'] ?? 0);
        $status = in_array($_POST['status'] ?? 'pending', ['pending','confirmed','cancelled']) ? $_POST['status'] : 'pending';

        // Require an existing user account for bookings
        if ($uid <= 0) {
            $error = 'Please select an existing user for this booking.';
        } elseif ($fid <= 0) {
            $error = 'Please select a flight for this booking.';
        }

        if (empty($error)) {
            $stmt = $conn->prepare('INSERT INTO bookings (user_id, flight_id, name, email, phone, passengers, class, total_cost, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$uid, $fid, $name, $email, $phone, $passengers, $class, $total_cost, $status]);
            $message = 'Booking created.';
        }
    }

    $bookingsList = $conn->query('SELECT b.*, u.username, f.flight_number, f.departure, f.destination FROM bookings b LEFT JOIN users u ON b.user_id=u.id LEFT JOIN flights f ON b.flight_id=f.id ORDER BY b.id DESC')->fetchAll(PDO::FETCH_ASSOC);

    // For add form selects
    $allUsers = $conn->query('SELECT id,username FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
    $allFlights = $conn->query('SELECT id,flight_number,departure,destination,price FROM flights ORDER BY departure_date')->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - Global Fly</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-wrap { max-width: 1200px; margin: 24px auto; padding: 12px; }
        .admin-nav { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
        .admin-nav a { padding:8px 12px; background:#0A1A2F; color:orange; text-decoration:none; border-radius:4px; }
        .admin-box { background:#fff; padding:16px; border-radius:8px; box-shadow:0 6px 20px rgba(10,26,47,0.06); }

        /* Table styling */
        .table-wrapper { width:100%; overflow-x:auto; }
        table.admin-table { width:100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e6e6e6; background:#fff; }
        table.admin-table thead th { background: #0A1A2F; color: orange; font-weight: 700; padding: 12px 10px; text-align:left; position: sticky; top:0; }
        table.admin-table tbody td { padding: 12px 10px; border-top:1px solid #f0f0f0; vertical-align: middle; }
        table.admin-table tbody tr:nth-child(odd) { background: #fbfbfb; }
        table.admin-table tbody tr:hover { background: #f0f8ff; }
        table.admin-table img { height:48px; border-radius:6px; display:block; }

        /* Action buttons */
        .action-link { display:inline-block; padding:6px 8px; margin-right:6px; border-radius:4px; text-decoration:none; color:#0A1A2F; background:#fff3e6; border:1px solid #f0d7bf; }
        .action-link.danger { background:#ffecec; border-color:#f4c6c6; color:#900; }

        form input, form textarea, form select { width:100%; padding:8px; margin:6px 0; box-sizing:border-box; }
        .small { width:120px; display:inline-block; }

        @media (max-width:800px) {
            table.admin-table thead th, table.admin-table tbody td { padding:10px 8px; font-size:14px; }
        }
    </style>
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="admin-wrap">
        <div class="admin-nav">
            <a href="admin.php?section=flights">Flights</a>
            <a href="admin.php?section=news">News</a>
            <a href="admin.php?section=users">Users</a>
            <a href="admin.php?section=bookings">Bookings</a>
        </div>

        <div class="admin-box">
            <?php if (!empty($message)): ?>
                <div style="padding:8px;background:#e6ffed;border:1px solid #c5f0d1;margin-bottom:8px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div style="padding:8px;background:#ffdede;border:1px solid #f0c5c5;margin-bottom:8px;color:#800;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($section === 'flights'): ?>
                <h2>Flights</h2>
                <p><a class="action-link" href="admin.php?section=flights">List</a> <span style="margin:0 8px;color:#999;">|</span> <a class="action-link" href="admin.php?section=flights&action=add">Add Flight</a></p>

                <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || $editFlight): ?>
                    <?php $f = $editFlight ?? ['id'=>'','flight_number'=>'','departure'=>'','destination'=>'','departure_date'=>'','price'=>'','image'=>'']; ?>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($f['id']); ?>">
                        <label>Flight number</label>
                        <input name="flight_number" required value="<?php echo htmlspecialchars($f['flight_number']); ?>">
                        <label>Departure</label>
                        <input name="departure" required value="<?php echo htmlspecialchars($f['departure']); ?>">
                        <label>Destination</label>
                        <input name="destination" required value="<?php echo htmlspecialchars($f['destination']); ?>">
                        <label>Departure date (YYYY-mm-dd HH:MM:SS)</label>
                        <input name="departure_date" required value="<?php echo htmlspecialchars($f['departure_date']); ?>">
                        <label>Price (integer)</label>
                        <input name="price" required value="<?php echo htmlspecialchars($f['price']); ?>">
                        <label>Image (upload will overwrite)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if (!empty($f['image'])): ?>
                            <p>Current: <img src="<?php echo htmlspecialchars($f['image']); ?>" style="height:48px;vertical-align:middle;"> <?php echo htmlspecialchars($f['image']); ?></p>
                        <?php endif; ?>
                        <button type="submit" name="save_flight">Save Flight</button>
                    </form>
                <?php else: ?>
                    <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr><th style="width:60px;">ID</th><th>Flight #</th><th>From</th><th>To</th><th>Departure</th><th style="width:90px;">Price</th><th style="width:80px;">Image</th><th style="width:160px;">Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($flights as $row): ?>
                            <tr>
                                <td><?php echo (int)$row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['departure']); ?></td>
                                <td><?php echo htmlspecialchars($row['destination']); ?></td>
                                <td><?php echo htmlspecialchars($row['departure_date']); ?></td>
                                <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php if (!empty($row['image'])): ?><img src="<?php echo htmlspecialchars($row['image']); ?>" style="height:48px;"><?php endif; ?></td>
                                <td class="actions-cell" style="white-space:nowrap;">
                                    <a class="action-link" href="admin.php?section=flights&action=edit&id=<?php echo (int)$row['id']; ?>">Edit</a>
                                    <a class="action-link danger" href="admin.php?section=flights&action=delete&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Delete this flight?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>

            <?php elseif ($section === 'news'): ?>
                <h2>News</h2>
                <p><a class="action-link" href="admin.php?section=news">List</a> <span style="margin:0 8px;color:#999">|</span> <a class="action-link" href="admin.php?section=news&action=add">Add News</a></p>
                <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || isset($editNews) && $editNews): ?>
                    <?php $n = $editNews ?? ['id'=>'','title'=>'','content'=>'','image'=>'']; ?>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($n['id']); ?>">
                        <label>Title</label>
                        <input name="title" required value="<?php echo htmlspecialchars($n['title']); ?>">
                        <label>Content</label>
                        <textarea name="content" rows="6"><?php echo htmlspecialchars($n['content']); ?></textarea>
                        <label>Image (upload will overwrite)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if (!empty($n['image'])): ?>
                            <p>Current: <img src="<?php echo htmlspecialchars($n['image']); ?>" style="height:48px;vertical-align:middle;"> <?php echo htmlspecialchars($n['image']); ?></p>
                        <?php endif; ?>
                        <button type="submit" name="save_news">Save News</button>
                    </form>
                <?php else: ?>
                    <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th style="width:60px;">ID</th><th>Title</th><th>Image</th><th style="width:160px">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($newsList as $row): ?>
                            <tr>
                                <td><?php echo (int)$row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php if (!empty($row['image'])): ?><img src="<?php echo htmlspecialchars($row['image']); ?>" style="height:48px;"><?php endif; ?></td>
                                <td style="white-space:nowrap;"><a class="action-link" href="admin.php?section=news&action=edit&id=<?php echo (int)$row['id']; ?>">Edit</a> <a class="action-link danger" href="admin.php?section=news&action=delete&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Delete this news item?')">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>

            <?php elseif ($section === 'users'): ?>
                <h2>Users</h2>
                <p>
                    <button type="button" class="action-link" onclick="togglePanel('users','list')">User List</button>
                    <button type="button" class="action-link" onclick="togglePanel('users','add')">Add User</button>
                </p>

                <div id="users-list">
                <div class="table-wrapper">
                <table class="admin-table">
                    <thead><tr><th style="width:60px">ID</th><th>Username</th><th>Email</th><th style="width:120px">Role</th><th style="width:160px">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo (int)$u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                    <select name="role">
                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="save_user" class="action-link">Save</button>
                                </form>
                            </td>
                            <td style="white-space:nowrap;"><a class="action-link danger" href="admin.php?section=users&action=delete&id=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                </div>

                <div id="users-add" style="display:none;">
                    <form method="post" class="admin-form">
                        <input type="hidden" name="save_new_user" value="1">
                        <label>Username</label>
                        <input name="username" required>
                        <label>Email</label>
                        <input name="email" type="email" required>
                        <label>Password</label>
                        <input name="password" type="password" required>
                        <label>Role</label>
                        <select name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="form-actions"><button type="submit" class="btn">Create User</button></div>
                    </form>
                </div>

            <?php elseif ($section === 'bookings'): ?>
                <h2>Bookings</h2>
                <p>
                    <button type="button" class="action-link" onclick="togglePanel('bookings','list')">Booking List</button>
                    <button type="button" class="action-link" onclick="togglePanel('bookings','add')">Add Booking</button>
                </p>

                <div id="bookings-add" style="display:none;">
                <div class="card">
                    <h3>Add Booking</h3>
                    <form method="post" class="admin-form">
                        <input type="hidden" name="save_booking" value="1">
                        <label>User</label>
                        <select name="user_id" required>
                            <option value="">Select a user</option>
                            <?php foreach ($allUsers as $u): ?>
                                <option value="<?=htmlspecialchars($u['id'])?>"><?=htmlspecialchars($u['username'])?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Flight</label>
                        <select name="flight_id" required id="flight_select">
                            <option value="0">Select a flight</option>
                            <?php foreach ($allFlights as $f): ?>
                                <option value="<?=htmlspecialchars($f['id'])?>" data-price="<?=htmlspecialchars($f['price'])?>"><?=htmlspecialchars($f['flight_number'].' — '. $f['departure'].' → '.$f['destination'].' ($'.number_format($f['price'],2).')')?></option>
                            <?php endforeach; ?>
                        </select>
                        <p>Price per passenger: $<span id="per_price">0.00</span></p>
                        <label>Contact name</label>
                        <input name="name" required>
                        <label>Email</label>
                        <input name="email" type="email">
                        <label>Phone</label>
                        <input name="phone">
                        <label>Passengers</label>
                        <input name="passengers" id="booking_passengers" type="number" value="1" min="1" max="9" step="1">
                                                <label>Class</label>
                                                <select name="class" id="class_select">
                                                    <option value="Basic Economy">Basic Economy</option>
                                                    <option value="Economy">Economy</option>
                                                    <option value="Premium Economy">Premium Economy</option>
                                                    <option value="Business">Business</option>
                                                    <option value="First">First</option>
                                                    <option value="Ultra Luxury">Ultra Luxury</option>
                                                </select>
                        <label>Total Cost</label>
                        <input name="total_cost" id="total_cost" type="number" step="0.01" value="0" readonly>
                        <label>Status</label>
                        <select name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div class="form-actions"><button type="submit" class="btn">Create Booking</button></div>
                    </form>
                </div>
                </div>

                <div id="bookings-list">
                <div class="table-wrapper">
                <table class="admin-table">
                    <thead><tr><th style="width:60px">ID</th><th>User</th><th>Flight</th><th>Passengers</th><th style="width:120px">Total</th><th style="width:140px">Status</th><th style="width:160px">Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($bookingsList as $b): ?>
                        <tr>
                            <td><?php echo (int)$b['id']; ?></td>
                            <td><?php echo htmlspecialchars($b['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(($b['flight_number'] ?? '') . ' — ' . ($b['departure'] ?? '') . ' → ' . ($b['destination'] ?? '')); ?></td>
                            <td><?php echo (int)($b['passengers'] ?? 0); ?></td>
                            <td>$<?php echo number_format((float)($b['total_cost'] ?? 0),2); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo $b['status']==='pending'?'selected':''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $b['status']==='confirmed'?'selected':''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $b['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="save_booking_status" class="action-link">Save</button>
                                </form>
                            </td>
                            <td style="white-space:nowrap;"><a class="action-link danger" href="admin.php?section=bookings&action=delete&id=<?php echo (int)$b['id']; ?>" onclick="return confirm('Delete this booking?')">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                </div>

            <?php else: ?>
                <p>Section not implemented yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function togglePanel(section, panel) {
        var list = document.getElementById(section + '-list');
        var add = document.getElementById(section + '-add');
        if (!list || !add) return;
        if (panel === 'add') {
            list.style.display = 'none';
            add.style.display = '';
        } else {
            add.style.display = 'none';
            list.style.display = '';
        }
        // small scroll into view
        if (list && list.style.display !== 'none') list.scrollIntoView({behavior:'smooth'});
        if (add && add.style.display !== 'none') add.scrollIntoView({behavior:'smooth'});
    }
    
    // Booking total updater
    function initBookingTotal() {
        var flightSelect = document.getElementById('flight_select');
        var passengersInput = document.querySelector('input[name="passengers"]');
        var totalInput = document.getElementById('total_cost');
        var perPrice = document.getElementById('per_price');
        if (!flightSelect || !passengersInput || !totalInput || !perPrice) return;

        function update() {
            var opt = flightSelect.options[flightSelect.selectedIndex];
            var basePrice = opt && opt.dataset && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
            var pax = parseInt(passengersInput.value, 10) || 0;
            // enforce min/max on client
            var minP = parseInt(passengersInput.min, 10) || 1;
            var maxP = parseInt(passengersInput.max, 10) || 9;
            pax = Math.max(minP, Math.min(maxP, pax));
            if (String(passengersInput.value) !== String(pax)) passengersInput.value = pax;
            var classSelect = document.getElementById('class_select');
            var cls = classSelect ? classSelect.value : 'Basic Economy';
            // class multipliers match booking.php
            var multipliers = {
                'Basic Economy': 1,
                'Economy': 1.3,
                'Premium Economy': 2.6,
                'Business': 4.2,
                'First': 6.3,
                'Ultra Luxury': 10.5
            };
            var mult = multipliers[cls] || 1;
            var per = basePrice * mult;
            perPrice.textContent = per.toFixed(2);
            totalInput.value = (per * pax).toFixed(2);
        }

        flightSelect.addEventListener('change', update);
        passengersInput.addEventListener('input', update);
        var classSelectEl = document.getElementById('class_select');
        if (classSelectEl) classSelectEl.addEventListener('change', update);
        // init
        update();
    }

    document.addEventListener('DOMContentLoaded', function(){ initBookingTotal(); });
    </script>

</body>
</html>
