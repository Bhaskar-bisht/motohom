<?php
// Include PHPMailer files
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection (example)
$servername = "localhost";
$username = "root";
$password = "";
$database = "motohom_newuser";
$conn = new mysqli($servername, $username, $password, $database, 3307);

// Check the database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Sanitize and collect form data
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$caravanType = htmlspecialchars($_POST['caravanType']);

// Insert data into the database
$stmt = $conn->prepare("INSERT INTO bookingdata (name, email, phone, caravan_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $caravanType);
if ($stmt->execute()) {
    $dataSaved = true;
} else {
    $dataSaved = false;
    echo json_encode(["status" => "error", "message" => "Error saving data: " . $stmt->error]);
    exit;
}

// Initialize PHPMailer
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  // SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bantu8120@gmail.com';  // Your email
    $mail->Password   = 'jjoixpijvcbvmupa';     // Your email password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender and recipient details
    $mail->setFrom('bantu8120@gmail.com', 'bhaskar');
    $mail->addAddress('sometime068@gmail.com', 'Admin'); // Admin email

    // Email content
    $mail->isHTML(true);
$mail->Subject = 'New Booking Inquiry from: ' . $name;
$mail->Body = "
    <html>
    <head>
        <title>New Booking Inquiry</title>
    </head>
    <body>
        <h2 style=\"color: green;\">New Booking Inquiry</h2>
        <table style=\"width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;\">
            <tr style=\"background-color: #f2f2f2;\">
                <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Name</td>
                <td style=\"padding: 10px; border: 1px solid #ddd;\">$name</td>
            </tr>
            <tr>
                <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Email</td>
                <td style=\"padding: 10px; border: 1px solid #ddd;\">$email</td>
            </tr>
            <tr style=\"background-color: #f2f2f2;\">
                <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Phone</td>
                <td style=\"padding: 10px; border: 1px solid #ddd;\">$phone</td>
            </tr>
            <tr>
                <td style=\"padding: 10px; border: 1px solid #ddd; font-weight: bold;\">Caravan Type</td>
                <td style=\"padding: 10px; border: 1px solid #ddd;\">$caravanType</td>
            </tr>
        </table>
    </body>
    </html>
";

    // Send the email
    if ($mail->send()) {
        $emailSent = true;
    } else {
        $emailSent = false;
        echo json_encode(["status" => "error", "message" => "Unable to send email."]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
    exit;
}

// Close the database connection
$conn->close();

// Return response based on success or failure
if ($dataSaved && $emailSent) {
    echo json_encode(["status" => "success", "message" => "Data saved and email sent successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "There was an issue with the process."]);
}
?>
