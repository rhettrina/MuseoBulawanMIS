<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Include database connection
include 'db_connect.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path as needed

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);
$appointmentId = $data['appointmentId'] ?? null;
$status = $data['status'] ?? null;
$adminMessage = $data['adminMessage'] ?? null;

// Validate input
if (!$appointmentId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

if (!$adminMessage) {
    echo json_encode(['success' => false, 'message' => 'Admin message is required.']);
    exit;
}

// Fetch appointment details to get user information
$querySelect = "SELECT name, email FROM appointment WHERE appointmentID = ?";
$stmtSelect = $connextion->prepare($querySelect);
$stmtSelect->bind_param("i", $appointmentId);
$stmtSelect->execute();
$result = $stmtSelect->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
    exit;
}

$appointment = $result->fetch_assoc();
$userEmail = $appointment['email'];
$userName = $appointment['name'];

// Update appointment status
$queryUpdate = "
    UPDATE appointment 
    SET status = ?, confirmation_date = NOW(), admin_message = ? 
    WHERE appointmentID = ?
";

$stmtUpdate = $connextion->prepare($queryUpdate);
$stmtUpdate->bind_param("ssi", $status, $adminMessage, $appointmentId);

if ($stmtUpdate->execute()) {
    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.example.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'russelguiwan@gmail.com';               // SMTP username
        $mail->Password   = 'your_email_password';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('russelguiwan@gmail.com', 'Bomb Squad');
        $mail->addAddress($userEmail, $userName);                   // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = "Your Appointment has been " . $status;
        $mail->Body    = "
            <p>Dear {$userName},</p>
            <p>Your appointment request has been <strong>{$status}</strong>.</p>
            <p><strong>Admin Message:</strong></p>
            <p>{$adminMessage}</p>
            <p>Thank you!</p>
        ";
        $mail->AltBody = "Dear {$userName},\n\nYour appointment request has been {$status}.\n\nAdmin Message:\n{$adminMessage}\n\nThank you!";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Appointment updated and email sent successfully.']);
    } catch (Exception $e) {
        // Optionally, you might want to rollback the status update if email fails
        // $stmtUpdate->rollback(); // Requires transaction management
        echo json_encode(['success' => false, 'message' => "Appointment updated but email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment.']);
}
?>
