<?php
require 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSync - Healthcare Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="index-header">
        <div class="logo">HealthSync</div>
        <nav>
            <a href="#hero">Home</a>
            <a href="#features">Features</a>
            <a href="#about">About Us</a>
            <a href="#contact">Contact Us</a>
        </nav>
    </header>

    <section class="hero" id="hero">
        <div class="hero-content">
            <h1>Welcome to HealthSync</h1>
            <p>Your trusted platform for seamless healthcare management.</p>
            <div class="button-pair">
                <a href="login.php" class="btn btn-primary">Log In</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Features Offered</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <h3>Book Appointments</h3>
                    <p>Easily schedule, reschedule, or cancel appointments with your doctor.</p>
                </div>
                <div class="feature-item">
                    <h3>Order Medicine</h3>
                    <p>Order your prescribed medications directly through the platform.</p>
                </div>
                <div class="feature-item">
                    <h3>Wellness Tips & Videos</h3>
                    <p>Access helpful wellness tips and educational videos to stay healthy.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2>About HealthSync</h2>
            <p>HealthSync is a comprehensive healthcare platform designed to streamline the interaction between patients, doctors, and administrators. Our mission is to make healthcare accessible, efficient, and patient-centered.</p>
            <p>With features like appointment scheduling, real-time wait times, medicine ordering, and wellness resources, HealthSync empowers users to take control of their healthcare journey.</p>
        </div>
    </section>

    <!-- Contact Section with Icons -->
    <section class="contact" id="contact">
        <div class="container">
            <h2>Contact Us</h2>
            <p>Have questions or need support? Reach out to our team!</p>
            <ul class="contact-list">
                <li><i class="fas fa-envelope"></i> <strong>Email:</strong> healthsync14@gmail.com</li>
                <li><i class="fas fa-phone"></i> <strong>Phone:</strong> +254110078420</li>
                <li><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> Machakos</li>
            </ul>
            <p>We’re here to assist you 24/7.</p>
        </div>
    </section>

    <!-- Footer -->
    <?php require 'includes/footer.php'; ?>

    <!-- Smooth Scrolling JavaScript -->
    <script>
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