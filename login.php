<?php
require 'includes/config.php';

// Start session if not already started (handled in config.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role === 'patient') {
        header('Location: patient/dashboard.php');
    } elseif ($role === 'doctor') {
        header('Location: doctor/dashboard.php');
    } elseif ($role === 'admin') {
        header('Location: admin/dashboard.php');
    }
    exit;
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    $error = null;

    // Validate CSRF token
    if ($csrf_token !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token. Please try again.";
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $error = "Please enter your password.";
    } else {
        // Query the database
        $stmt = $pdo->prepare("SELECT id, email, password, role, first_name, last_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            // Store user data in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name']
            ];

            // Redirect based on role
            if ($user['role'] === 'patient') {
                header('Location: patient/dashboard.php');
            } elseif ($user['role'] === 'doctor') {
                header('Location: doctor/dashboard.php');
            } elseif ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HealthSync</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="index-header">
        <div class="logo">HealthSync</div>
        <nav>
            <a href="index.php">Home</a>
        </nav>
    </header>
    <div class="login-container">
        <h1>Log In to HealthSync</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
        <p>Don’t have an account? <a href="register.php">Register here</a>. | <a href="forgot_password.php">Forgot Password?</a></p>
    </div>
    <?php require 'includes/footer.php'; ?>
</body>
</html>