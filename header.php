<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="logo">HealthSync</div>
    <nav>
        <?php if (isset($_SESSION['admin'])): ?>
            <div class="dropdown">
                <button class="dropdown-toggle">Admin Actions</button>
                <div class="dropdown-menu">
                    <a href="../admin/dashboard.php">Dashboard</a>
                    <a href="../admin/view_patients.php">View Patients</a>
                    <a href="../admin/view_doctors.php">View Doctors</a>
                    <a href="../admin/view_appointments.php">View Appointments</a>
                    <a href="../admin/view_medicine_orders.php">View Medicine Orders</a>
                    <a href="../admin/logout.php">Log Out</a>
                </div>
            </div>
        <?php elseif (isset($_SESSION['patient'])): ?>
            <a href="../patient/dashboard.php">Dashboard</a>
            <a href="../patient/logout.php">Log Out</a>
        <?php elseif (isset($_SESSION['doctor'])): ?>
            <a href="../doctor/dashboard.php">Dashboard</a>
            <a href="../doctor/logout.php">Log Out</a>
        <?php else: ?>
            <a href="../index.php">Home</a>
            <a href="../patient/login.php">Patient Login</a>
            <a href="../doctor/login.php">Doctor Login</a>
            <a href="../admin/login.php">Admin Login</a>
        <?php endif; ?>
    </nav>
</header>