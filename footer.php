<?php
// Prevent direct access to this file
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}
?>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-logo">HealthSync</div>
        <p>© <?php echo date('Y'); ?> HealthSync. All rights reserved.</p>
    </div>
</footer>