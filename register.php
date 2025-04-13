<?php
require 'includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle form submissions
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = isset($_POST['role']) ? $_POST['role'] : null;
    $username = trim($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $specialty = trim($_POST['specialty'] ?? '');

    // Validation
    if (!in_array($role, ['patient', 'doctor'])) {
        $error = "Invalid registration type.";
    } elseif (empty($username) || strlen($username) < 3) {
        $error = "Full name must be at least 3 characters.";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($password) || strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($role === 'doctor' && empty($specialty)) {
        $error = "Specialty is required for doctors.";
    } else {
        // Split username into first_name and last_name
        $name_parts = explode(' ', $username, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert into database
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, role, first_name, last_name, specialty)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            try {
                $stmt->execute([
                    $email,
                    $hashed_password,
                    $role,
                    $first_name,
                    $last_name,
                    $role === 'doctor' ? $specialty : null
                ]);
                $success = "Registration successful! Redirecting to login...";
                // Redirect after 2 seconds
                header("Refresh: 2; url=login.php");
            } catch (PDOException $e) {
                $error = "Registration failed: " . htmlspecialchars($e->getMessage());
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
    <title>Register - HealthSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Form Styles */
        .register-container {
            width: 100%;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .register-options {
            display: flex;
            justify-content: space-between;
        }

        .form-section {
            width: 45%;
            padding: 20px;
            background-color: #ecf0f1;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            text-align: center;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .form-section label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-section input[type="text"],
        .form-section input[type="email"],
        .form-section input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-section button {
            width: 100%;
            padding: 12px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .form-section button:hover {
            background-color: #3498db;
        }

        /* Error and Success Messages */
        .error {
            color: #e74c3c;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            color: #2ecc71;
            font-size: 1rem;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2980b9;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="index-header">
        <div class="logo">HealthSync</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <div class="register-container">
        <h1>Register with HealthSync</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <div class="register-options">
            <!-- Patient Registration Form -->
            <div class="form-section">
                <h2>Register as Patient</h2>
                <form method="POST" action="" autocomplete="off">
                    <input type="hidden" name="role" value="patient">
                    <label for="p-username">Full Name:</label>
                    <input type="text" name="username" id="p-username" required>
                    <label for="p-email">Email:</label>
                    <input type="email" name="email" id="p-email" required>
                    <label for="p-password">Password:</label>
                    <input type="password" name="password" id="p-password" required>
                    <button type="submit" class="btn btn-secondary">Register</button>
                </form>
            </div>

            <!-- Doctor Registration Form -->
            <div class="form-section">
                <h2>Register as Doctor</h2>
                <form method="POST" action="" autocomplete="off">
                    <input type="hidden" name="role" value="doctor">
                    <label for="d-username">Full Name:</label>
                    <input type="text" name="username" id="d-username" required>
                    <label for="d-email">Email:</label>
                    <input type="email" name="email" id="d-email" required>
                    <label for="d-password">Password:</label>
                    <input type="password" name="password" id="d-password" required>
                    <label for="d-specialty">Specialty:</label>
                    <input type="text" name="specialty" id="d-specialty" required>
                    <button type="submit" class="btn btn-secondary">Register</button>
                </form>
            </div>
        </div>
        <p class="login-link">Already have an account? <a href="login.php">Log in here</a>.</p>
    </div>

    <!-- Footer -->
    <?php require 'includes/footer.php'; ?>
</body>
</html>