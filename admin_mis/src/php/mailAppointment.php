<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve and sanitize POST data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $adminMessage = trim($_POST['adminMessage'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all fields.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    include 'db_connect.php';

    if ($connection->connect_error) { // Assuming 'db_connect.php' defines $connection
        die('Connection Failed: ' . $connection->connect_error);
    } else {
        // Prepare the INSERT statement
        $stmt = $connection->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");

        if ($stmt === false) {
            die('Statement preparation failed: ' . $connection->error);
        }

        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {

            $mail = new PHPMailer(true);

            try {
                // Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;          // Enable verbose debug output (optional)
                $mail->isSMTP();                                    // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';               // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                           // Enable SMTP authentication
                $mail->Username   = 'russelguiwan@gmail.com';       // SMTP username
                $mail->Password   = 'jfrt khhf iijo pbai';          // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable implicit TLS encryption
                $mail->Port       = 465;                            // TCP port to connect to

                // Recipients
                $mail->setFrom('russelguiwan@gmail.com', 'Museo Bulawan'); // Set sender's email and name
                $mail->addAddress('admin@museobulawan.online', 'Admin');     // Add admin recipient
                // Optionally, send a copy to the user
                // $mail->addAddress($email, $name); // Uncomment if needed
                $mail->addReplyTo($email, $name);                           // Reply to the user's email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'New Contact Form Submission';
                $mail->Body    = '
                    <h1>Contact Form Submission</h1>
                    <p><strong>Name:</strong> ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>
                    <p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</p>
                    <p><strong>Admin Message:</strong><br>' . nl2br(htmlspecialchars($adminMessage, ENT_QUOTES, 'UTF-8')) . '</p>';
                $mail->AltBody = "Name: $name\nEmail: $email\nMessage:\n$message\n\nAdmin Message:\n$adminMessage";

                $mail->send();
                echo "Your message has been sent successfully.";
                
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            echo "Error in execution: " . $stmt->error;
        }

        $stmt->close();
        $connection->close();
    }

}
?>
