<?php
require '../includes/config.php';

// Check if patient is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

$patient_id = $_SESSION['user']['id'];

// Initialize success/error message
$success_message = '';
$error_message = '';

// Function to log activity
function logActivity($pdo, $user_id, $action) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
        return true;
    } catch (PDOException $e) {
        error_log("Activity logging failed: " . $e->getMessage());
        return false;
    }
}

// Fetch doctors for dropdown
$stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) as name, specialty FROM users WHERE role = 'doctor'");
$doctors = $stmt->fetchAll();

// Fetch patient's appointments
$stmt = $pdo->prepare("
    SELECT a.id, a.appointment_date, a.status, a.reason, 
           CONCAT(d.first_name, ' ', d.last_name) as doctor_name
    FROM appointments a
    JOIN users d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date ASC
");
$stmt->execute([$patient_id]);
$appointments = $stmt->fetchAll();

// Fetch patient's medicine orders
$stmt = $pdo->prepare("
    SELECT id, medicine_name, quantity, status
    FROM medicine_orders
    WHERE patient_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$patient_id]);
$medicine_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch wellness tips (limit 4)
$stmt = $pdo->prepare("SELECT title, content FROM wellness_content WHERE type = 'tip' ORDER BY created_at DESC LIMIT 4");
$stmt->execute();
$wellness_tips = $stmt->fetchAll();

// Fetch wellness videos (limit 4)
$stmt = $pdo->prepare("SELECT title, content FROM wellness_content WHERE type = 'video' ORDER BY created_at DESC LIMIT 4");
$stmt->execute();
$wellness_videos = $stmt->fetchAll();

// Handle book appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $reason = $_POST['reason'];

    // Insert appointment
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$patient_id, $doctor_id, $appointment_date, $reason]);

    // Get the new appointment ID
    $appointment_id = $pdo->lastInsertId();

    // Log activity
    logActivity($pdo, $patient_id, "Booked appointment (ID: $appointment_id)");

    $success_message = "Appointment booked successfully!";
    $_SESSION['success_message'] = $success_message;
    header('Location: dashboard.php');
    exit;
}

// Handle cancel/reschedule appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    if ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'canceled' WHERE id = ? AND patient_id = ?");
        $stmt->execute([$appointment_id, $patient_id]);
        // Log activity
        logActivity($pdo, $patient_id, "Canceled appointment (ID: $appointment_id)");
    } elseif ($action === 'reschedule') {
        $new_date = $_POST['new_date'];
        $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, status = 'pending' WHERE id = ? AND patient_id = ?");
        $stmt->execute([$new_date, $appointment_id, $patient_id]);
        // Log activity
        logActivity($pdo, $patient_id, "Rescheduled appointment (ID: $appointment_id)");
    }

    header('Location: dashboard.php');
    exit;
}

// Handle medicine order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_medicine'])) {
    $medicine_name = $_POST['medicine_name'];
    $quantity = $_POST['quantity'];

    // Insert medicine order
    $stmt = $pdo->prepare("INSERT INTO medicine_orders (patient_id, medicine_name, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$patient_id, $medicine_name, $quantity]);

    // Get the new order ID
    $order_id = $pdo->lastInsertId();

    // Log activity
    logActivity($pdo, $patient_id, "Placed medicine order (ID: $order_id)");

    $success_message = "Medicine order placed successfully!";
    $_SESSION['success_message'] = $success_message;
    header('Location: dashboard.php');
    exit;
}

// Handle cancel medicine order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        // Check if order is pending before canceling
        $stmt = $pdo->prepare("SELECT status FROM medicine_orders WHERE id = ? AND patient_id = ?");
        $stmt->execute([$order_id, $patient_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order && $order['status'] === 'pending') {
            // Update status to canceled
            $stmt = $pdo->prepare("UPDATE medicine_orders SET status = 'canceled' WHERE id = ? AND patient_id = ?");
            $rows_affected = $stmt->execute([$order_id, $patient_id]);

            if ($rows_affected > 0) {
                // Log activity
                logActivity($pdo, $patient_id, "Canceled medicine order (ID: $order_id)");
                $success_message = "Medicine order canceled successfully!";
            } else {
                $error_message = "Failed to cancel order. Please try again.";
            }
        } else {
            $error_message = "Order not found or already canceled/delivered.";
        }
    } catch (PDOException $e) {
        $error_message = "Error canceling order: " . $e->getMessage();
        error_log("Cancel order failed: " . $e->getMessage());
    }

    // Store messages in session
    if ($success_message) {
        $_SESSION['success_message'] = $success_message;
    } elseif ($error_message) {
        $_SESSION['error_message'] = $error_message;
    }
    header('Location: dashboard.php');
    exit;
}

// Display success/error message if set
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - HealthSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Existing Styles */
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .wellness-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .wellness-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .wellness-item h3 {
            margin: 0 0 10px;
            color: #2980b9;
        }
        .wellness-item p {
            margin: 0;
            color: #2c3e50;
        }
        .wellness-item iframe {
            max-width: 100%;
            height: 200px;
        }
        .wellness-subsection {
            margin-bottom: 30px;
        }
        .wellness-subsection h3 {
            color: #2980b9;
            margin-bottom: 15px;
        }

        /* New Pill-Shaped Button Styles */
        .btn {
            display: inline-block;
            padding: 10px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 50px; /* Pill-shaped corners */
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #ecf0f1; /* Fallback color */
            color: #2c3e50; /* Default text color */
        }

        .btn:hover {
            transform: scale(1.05); /* Slight scale-up for hover */
        }

        .btn:active {
            transform: scale(1); /* Reset scale on click */
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #3498db;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: #e74c3c; /* Matches screenshot */
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-green {
            background-color: #2ecc71; /* Green for Order button */
            color: white;
        }

        .btn-green:hover {
            background-color: #27ae60;
        }

        /* Ensure navigation buttons (e.g., Logout) align with pill shape */
        .index-header nav a.btn-danger {
            padding: 10px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
        }

        /* Adjust button spacing in forms and tables */
        form .btn {
            margin: 5px;
        }

        td .btn {
            margin-right: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn {
                padding: 8px 20px;
                font-size: 0.9rem;
            }

            .index-header nav a.btn-danger {
                padding: 8px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header class="index-header">
        <div class="logo">HealthSync</div>
        <nav>
            <a href="#welcome">Home</a>
            <a href="#appointments">Appointments</a>
            <a href="#medicine">Medicine</a>
            <a href="#wellness">Wellness</a>
            <a href="../index.php">Main Site</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </nav>
    </header>

    <div class="dashboard-container">
        <!-- Success/Error Message -->
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Welcome Message -->
        <section id="welcome" class="dashboard-section">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']); ?>!</h1>
            <p>Manage your healthcare needs here.</p>
            <button id="book-btn" class="btn btn-primary">Book Appointment</button>
        </section>

        <!-- Book Appointment Form (Hidden Initially) -->
        <section id="book-form" class="dashboard-section" style="display: none;">
            <h2>Book an Appointment</h2>
            <form method="POST" class="appointment-form">
                <label for="doctor_id">Select Doctor:</label>
                <select name="doctor_id" id="doctor_id" required>
                    <option value="" disabled selected>Choose a doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['id']; ?>">
                            <?php echo htmlspecialchars($doctor['name']) . ' - ' . htmlspecialchars($doctor['specialty']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="appointment_date">Appointment Time:</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" required>
                <label for="reason">Reason:</label>
                <textarea name="reason" id="reason" required placeholder="Describe your reason"></textarea>
                <button type="submit" name="book_appointment" class="btn btn-secondary">Submit</button>
            </form>
        </section>

        <!-- Appointments -->
        <section id="appointments" class="dashboard-section">
            <h2>Your Appointments</h2>
            <?php if (empty($appointments)): ?>
                <p>No appointments scheduled.</p>
            <?php else: ?>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($appt['appointment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($appt['reason']); ?></td>
                                <td><?php echo ucfirst($appt['status']); ?></td>
                                <td>
                                    <?php if ($appt['status'] !== 'canceled'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                            <button type="submit" name="action" value="cancel" class="btn btn-danger">Cancel</button>
                                        </form>
                                        <button class="btn btn-primary reschedule-btn" data-id="<?php echo $appt['id']; ?>">Reschedule</button>
                                        <form method="POST" class="reschedule-form" id="reschedule-<?php echo $appt['id']; ?>" style="display:none;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                            <input type="datetime-local" name="new_date" required>
                                            <button type="submit" name="action" value="reschedule" class="btn btn-secondary">Update</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Order Medicine -->
        <section id="medicine" class="dashboard-section">
            <h2>Order Medicine</h2>
            <form method="POST" class="medicine-form">
                <label for="medicine_name">Medicine Name:</label>
                <input type="text" name="medicine_name" id="medicine_name" required placeholder="e.g., Paracetamol">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" required placeholder="e.g., 10">
                <button type="submit" name="order_medicine" class="btn btn-green">Order</button>
            </form>

            <!-- Medicine Orders -->
            <h3>Your Medicine Orders</h3>
            <?php if (empty($medicine_orders)): ?>
                <p>No medicine orders placed.</p>
            <?php else: ?>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medicine Name</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicine_orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['medicine_name']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo ucfirst($order['status']); ?></td>
                                <td>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Wellness Tips & Videos -->
        <section id="wellness" class="dashboard-section">
            <h2>Wellness Tips & Videos</h2>
            <!-- Wellness Tips -->
            <div class="wellness-subsection">
                <h3>Health Tips</h3>
                <?php if (empty($wellness_tips)): ?>
                    <p>No wellness tips available.</p>
                <?php else: ?>
                    <div class="wellness-grid">
                        <?php foreach ($wellness_tips as $tip): ?>
                            <div class="wellness-item">
                                <h3><?php echo htmlspecialchars($tip['title']); ?></h3>
                                <p><?php echo htmlspecialchars($tip['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Wellness Videos -->
            <div class="wellness-subsection">
                <h3>Health Videos</h3>
                <?php if (empty($wellness_videos)): ?>
                    <p>No wellness videos available.</p>
                <?php else: ?>
                    <div class="wellness-grid">
                        <?php foreach ($wellness_videos as $video): ?>
                            <div class="wellness-item">
                                <iframe width="100%" height="200" src="<?php echo htmlspecialchars($video['content']); ?>" frameborder="0" allowfullscreen></iframe>
                                <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php require '../includes/footer.php'; ?>

    <!-- JavaScript for Book Form and Reschedule -->
    <script>
        // Toggle Book Appointment Form
        document.getElementById('book-btn').addEventListener('click', function() {
            const form = document.getElementById('book-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Toggle Reschedule Forms
        document.querySelectorAll('.reschedule-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('reschedule-' + id);
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            });
        });

        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 60,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>