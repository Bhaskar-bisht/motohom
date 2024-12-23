<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpMailer/src/PHPMailer.php';
require 'phpMailer/src/Exception.php';
require 'phpMailer/src/SMTP.php';

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP setup
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  // SMTP server (Gmail example)
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bantu8120@gmail.com';  // Your email address
    $mail->Password   = 'jjoixpijvcbvmupa';  // Your email password (or app password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;  // SMTP port

    // Sanitize and collect form data
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $city = htmlspecialchars($_POST['city']);
    $startLocation = htmlspecialchars($_POST['startLocation']);
    $destination = htmlspecialchars($_POST['destination']);
    $startDate = htmlspecialchars($_POST['startDate']);
    $endDate = htmlspecialchars($_POST['endDate']);
    $caravanType = htmlspecialchars($_POST['caravanType']);
    $totalPeople = htmlspecialchars($_POST['totalPeople']);
    $dob = htmlspecialchars($_POST['dob']);
    $additional_Information = htmlspecialchars($_POST['additional_Information']);

    // Set email details
    $mail->setFrom('bantu8120@gmail.com', 'Bhaskar');  // Your email
    $mail->addAddress('sometime068@gmail.com', 'Admin');  // Admin email

    // Email subject and body
    $mail->isHTML(true);
    $mail->Subject = 'New Booking Inquiry from: ' . $firstName . ' ' . $lastName;
    $mail->Body    = "
        <html>
        <head>
            <title>New Booking Inquiry</title>
        </head>
        <body>
            <h2>New Booking Inquiry</h2>
            <p><strong>First Name:</strong> $firstName</p>
            <p><strong>Last Name:</strong> $lastName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Location:</strong> $city</p>
            <p><strong>Start Location:</strong> $startLocation</p>
            <p><strong>Destination:</strong> $destination</p>
            <p><strong>Start Date:</strong> $startDate</p>
            <p><strong>End Date:</strong> $endDate</p>
            <p><strong>Caravan Type:</strong> $caravanType</p>
            <p><strong>Total People:</strong> $totalPeople</p>
            <p><strong>Date of Birth:</strong> $dob</p>
            <p><strong>additional_Information:</strong> $additional_Information</p>
        </body>
        </html>
    ";

    // Send the email
    if ($mail->send()) {
        echo 'Success';  // Email sent successfully
    } else {
        echo 'Error: Unable to send email. Please try again later.';
    }
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;  // Error handling
}
?>