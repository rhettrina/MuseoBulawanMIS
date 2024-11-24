<?php
// Enable strict error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the path to the log file
define('LOG_FILE', __DIR__ . '/logs/app.log');

/**
 * Custom logging function
 *
 * @param string $message The message to log
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[{$timestamp}] {$message}\n";
    error_log($formattedMessage, 3, LOG_FILE);
}

// Log the start of the script
logMessage("=== New Request Started ===");

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Include database connection
include 'db_connect.php';
logMessage("Database connection included.");

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure the path is correct
logMessage("PHPMailer autoload included.");

// Get the POST data
$rawInput = file_get_contents("php://input");
logMessage("Raw POST input: " . $rawInput);

$data = json_decode($rawInput, true);

// Retrieve data with null coalescing
$appointmentId = $data['appointmentId'] ?? null;
$status = $data['status'] ?? null;
$adminMessage = $data['adminMessage'] ?? null;

// Log the parsed data
logMessage("Parsed Data - Appointment ID: {$appointmentId}, Status: {$status}, Admin Message: {$adminMessage}");

// Validate input
if (!$appointmentId || !$status) {
    logMessage("Validation Failed: Invalid input. Appointment ID or Status missing.");
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

if (!$adminMessage) {
    logMessage("Validation Failed: Admin message is required.");
    echo json_encode(['success' => false, 'message' => 'Admin message is required.']);
    exit;
}

try {
    // Fetch appointment details to get user information
    $querySelect = "SELECT name, email FROM appointment WHERE appointmentID = ?";
    $stmtSelect = $connextion->prepare($querySelect);

    if (!$stmtSelect) {
        throw new Exception("Prepare statement failed: " . $connextion->error);
    }

    $stmtSelect->bind_param("i", $appointmentId);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    if ($result->num_rows === 0) {
        logMessage("Fetch Failed: Appointment ID {$appointmentId} not found.");
        echo json_encode(['success' => false, 'message' => 'Appointment not found.']);
        exit;
    }

    $appointment = $result->fetch_assoc();
    $userEmail = $appointment['email'];
    $userName = $appointment['name'];

    logMessage("Fetched Appointment - User Name: {$userName}, User Email: {$userEmail}");

    // Update appointment status
    $queryUpdate = "
        UPDATE appointment 
        SET status = ?, confirmation_date = NOW(), admin_message = ? 
        WHERE appointmentID = ?
    ";

    $stmtUpdate = $connextion->prepare($queryUpdate);

    if (!$stmtUpdate) {
        throw new Exception("Prepare statement failed: " . $connextion->error);
    }

    $stmtUpdate->bind_param("ssi", $status, $adminMessage, $appointmentId);

    if ($stmtUpdate->execute()) {
        logMessage("Appointment ID {$appointmentId} updated to status '{$status}' with admin message.");

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output (uncomment for debugging)
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through (Gmail SMTP)
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'russelguiwan@gmail.com';               // SMTP username
            $mail->Password   = 'jfrt khhf iijo pbai';                  // SMTP password (use app-specific password)
            $mail->SMTPSecure = "ssl";                                   // Enable SSL encryption
            $mail->Port       = 465;                                    // TCP port to connect to (465 for SSL)

            logMessage("SMTP configured with Host: {$mail->Host}, Port: {$mail->Port}");

            // Recipients
            $mail->setFrom('russelguiwan@gmail.com', 'Bomb Squad');     // Sender's email and name
            $mail->addAddress($userEmail, $userName);                   // Add a recipient

            logMessage("Email recipient set to {$userEmail} ({$userName}).");

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

            logMessage("Email content prepared with Subject: {$mail->Subject}");

            // Send the email
            $mail->send();
            logMessage("Email sent successfully to {$userEmail} regarding appointment ID {$appointmentId}.");
            echo json_encode(['success' => true, 'message' => 'Appointment updated and email sent successfully.']);
        } catch (Exception $e) {
            // Log PHPMailer exception
            logMessage("PHPMailer Exception: " . $mail->ErrorInfo);
            // Optionally, you might want to rollback the status update if email fails
            // $stmtUpdate->rollback(); // Requires transaction management
            echo json_encode(['success' => false, 'message' => "Appointment updated but email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        logMessage("Database Update Failed: Could not update appointment ID {$appointmentId}. Error: " . $stmtUpdate->error);
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment.']);
    }
} catch (Exception $ex) {
    // Log the exception
    logMessage("Exception Caught: " . $ex->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing the appointment.']);
}

// Log the end of the script
logMessage("=== Request Ended ===\n");
?>
