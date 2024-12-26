<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Debugging for development (Disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Clean output buffer
if (ob_get_length()) {
    ob_clean();
}
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "motohom_newuser";
$conn = new mysqli($servername, $username, $password, $database, 3307);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Collect form data
$name = htmlspecialchars($_POST['name'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$phone = htmlspecialchars($_POST['phone'] ?? '');
$caravanType = htmlspecialchars($_POST['caravanType'] ?? '');

// Validate data
if (empty($name) || empty($email) || empty($phone) || empty($caravanType)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Create table if not exists
$tableQuery = "
    CREATE TABLE IF NOT EXISTS download_guide_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        caravan_type VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";
if (!$conn->query($tableQuery)) {
    echo json_encode(["status" => "error", "message" => "Error creating table: " . $conn->error]);
    exit;
}

// Insert data
$stmt = $conn->prepare("INSERT INTO download_guide_data (name, email, phone, caravan_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $caravanType);
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Error saving data: " . $stmt->error]);
    exit;
}

// Send email to admin using PHPMailer
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'bantu8120@gmail.com'; // SMTP username
    $mail->Password = 'jjoixpijvcbvmupa'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('noreply@motohom.com', 'Motohom');
    $mail->addAddress('sometime068@gmail.com', 'Admin'); // Replace with actual admin email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New User Downloaded the PDF Guide';
    $mail->Body = "
        <html>
    <head>
        <title>New PDF guide Download</title>
    </head>
    <body>
        <h3 style=\"color: green;\">A new user has downloaded the PDF guide. Details:</h3>
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

    $mail->send();
} catch (Exception $e) {
    error_log("PHPMailer Error: {$mail->ErrorInfo}");
    // Log email error but continue to provide PDF download response
}

// Return JSON response with PDF URL
$pdfFilePath = 'images/information_PDF/Motohom_Caravans_Information.pdf';
if (file_exists($pdfFilePath)) {
    echo json_encode(["status" => "success", "pdfUrl" => $pdfFilePath]);
} else {
    echo json_encode(["status" => "error", "message" => "PDF file not found."]);
}

exit;
?>
