<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all fields.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    include 'db_connect.php';

    if($connextion->connect_error){
        die('Connection Failed: '.$connextion->connect_error);
    } else {
        $stmt = $connextion->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");

        if($stmt === false) {
            die('Statement preparation failed: ' . $connextion->error);
        }

        $stmt->bind_param("sss", $name, $email, $message);

        if($stmt->execute()){
 
            $mail = new PHPMailer(true);

            try {
                // Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;          // Enable verbose debug output (optional)
                $mail->isSMTP();                                    // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';             // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                           // Enable SMTP authentication
                $mail->Username   = 'russelguiwan@gmail.com';             // SMTP username
                $mail->Password   = 'jfrt khhf iijo pbai';                       // SMTP password
                $mail->SMTPSecure = "ssl";               // Enable implicit TLS encryption
                $mail->Port       = 465;                            // TCP port to connect to

                // Recipients
                $mail->setFrom('russelguiwan@gmail.com', $name);
                $mail->addAddress($_POST["email"], 'John Russel');   // Add your email and name
                $mail->addReplyTo($email, $name);                           // Reply to the user's email

                // Content
                $mail->isHTML(true);  
                $mail->Subject = 'New Contact Form Submission';
                $mail->Body    = '
                    <h1>Contact Form Submission</h1>
                    <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                    <p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>';
                $mail->AltBody = "Name: $name\nEmail: $email\nMessage:\n$message";

                $mail->send();
                
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            echo "Error in execution: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }

}
?>

