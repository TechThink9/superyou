<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';
require 'PHPMailer/PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 1: Trim the input and check for required email field
    $email = trim($_POST['email']);
    
    // Check if email is empty
    if (empty($email)) {
        http_response_code(400);  // Bad Request
        echo json_encode(['message' => 'Email address is required.']);
        exit;
    }

    // Step 2: Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);  // Bad Request
        echo json_encode(['message' => 'Invalid email format. Please enter a valid email address.']);
        exit;  // Exit script if email is invalid
    }

    // Step 3: Sanitize input to prevent email header injection
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Additional validation: Ensure no CRLF injection (to avoid header injection attacks)
    if (preg_match('/[\r\n]/', $email)) {
        http_response_code(400);  // Bad Request
        echo json_encode(['message' => 'Invalid email input detected.']);
        exit;  // Exit script if email injection detected
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hypedesk@superyou.in';
        $mail->Password = 'itbv xviy swcu ieyw';  // Use your app password for Gmail
        $mail->SMTPSecure = 'tls';  // For TLS (Port 587)
        $mail->Port = 587;

        // Email content
        $mail->setFrom('hypedesk@superyou.in', 'SuperYou');
        
        // Send the email only to the admin
        $mail->addAddress('marketing@superyou.in');  // Admin's email address

        // Email subject and body
        $mail->Subject = 'New Subscriber on SuperYou!';
        $mail->Body = "A new user has subscribed with the following email: $email";

        // Send the email
        $mail->send();
        echo json_encode(['message' => 'Email sent successfully to the admin!']);
    } catch (Exception $e) {
        http_response_code(500);  // Internal Server Error
        echo json_encode(['message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    }
} else {
    http_response_code(405);  // Method Not Allowed
    echo json_encode(['message' => 'Method not allowed']);
}
?>
