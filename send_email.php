<?php
require '../includes/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require '../vendor/PHPMailer/PHPMailer.php';
require '../vendor/PHPMailer/Exception.php';
require '../vendor/PHPMailer/SMTP.php';

function sendEmail($type, $id) {
    global $pdo;

    $mail = new PHPMailer(true);
    try {
        // Disable SMTP debugging for production
        $mail->SMTPDebug = 0; // 0 = off, SMTP::DEBUG_SERVER for debugging only

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'healthsync14@gmail.com';
        $mail->Password = 'xwpt qean vxsg eoqt'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Fetch data based on email type
        $patient = null;
        $subject = '';
        $body = '';

        if ($type === 'appointment_approve' || $type === 'appointment_cancel' || $type === 'appointment_remind') {
            // Fetch appointment details
            $stmt = $pdo->prepare("
                SELECT u.email, CONCAT(u.first_name, ' ', u.last_name) as patient_name,
                       a.appointment_date, CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.id
                JOIN users d ON a.doctor_id = d.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            $patient = $stmt->fetch();

            if (!$patient) {
                error_log("No patient found for appointment ID: $id");
                return "Error: No patient found for appointment.";
            }

            $appointment_time = date('M d, Y H:i', strtotime($patient['appointment_date']));
            $patient_name = htmlspecialchars($patient['patient_name']);
            $doctor_name = htmlspecialchars($patient['doctor_name']);

            if ($type === 'appointment_approve') {
                $subject = 'Your Appointment Has Been Approved!';
                $body = "Dear $patient_name,<br><br>Your appointment with Dr. $doctor_name on $appointment_time has been approved. We look forward to seeing you.<br><br>Regards,<br>HealthSync Team";
            } elseif ($type === 'appointment_cancel') {
                $subject = 'Your Appointment Has Been Canceled';
                $body = "Dear $patient_name,<br><br>Your appointment with Dr. $doctor_name on $appointment_time has been canceled. Please contact us to reschedule if needed.<br><br>Regards,<br>HealthSync Team";
            } elseif ($type === 'appointment_remind') {
                $subject = 'Reminder: Upcoming Appointment';
                $body = "Dear $patient_name,<br><br>This is a reminder for your appointment with Dr. $doctor_name on $appointment_time. Please arrive on time or contact us to reschedule.<br><br>Regards,<br>HealthSync Team";
            }
        } elseif ($type === 'medicine_deliver') {
            // Fetch medicine order details
            $stmt = $pdo->prepare("
                SELECT u.email, CONCAT(u.first_name, ' ', u.last_name) as patient_name,
                       mo.medicine_name, mo.quantity
                FROM medicine_orders mo
                JOIN users u ON mo.patient_id = u.id
                WHERE mo.id = ?
            ");
            $stmt->execute([$id]);
            $patient = $stmt->fetch();

            if (!$patient) {
                error_log("No patient found for medicine order ID: $id");
                return "Error: No patient found for medicine order.";
            }

            $patient_name = htmlspecialchars($patient['patient_name']);
            $medicine_name = htmlspecialchars($patient['medicine_name']);
            $quantity = $patient['quantity'];

            $subject = 'Your Medicine Order Has Been Delivered';
            $body = "Dear $patient_name,<br><br>Your order of $quantity unit(s) of $medicine_name has been delivered. Thank you for using HealthSync.<br><br>Regards,<br>HealthSync Team";
        } else {
            error_log("Invalid email type: $type");
            return "Error: Invalid email type.";
        }

        // Recipients
        $mail->setFrom('healthsync14@gmail.com', 'HealthSync');
        $mail->addAddress($patient['email'], $patient['patient_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // Plain-text fallback

        // Send the email
        $mail->send();
        error_log("Email sent successfully: Type=$type, ID=$id, To={$patient['email']}");
        return true; // Success
    } catch (Exception $e) {
        $error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        error_log($error);
        return $error; // Return error message
    }
}
?>