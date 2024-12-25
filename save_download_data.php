<?php
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

// Insert data into the table
$stmt = $conn->prepare("INSERT INTO download_guide_data (name, email, phone, caravan_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $caravanType);
if ($stmt->execute()) {
    $dataSaved = true;
} else {
    $dataSaved = false;
    echo json_encode(["status" => "error", "message" => "Error saving data: " . $stmt->error]);
    exit;
}

// Close the database connection
$stmt->close();
$conn->close();

// Return a downloadable PDF response
if ($dataSaved) {
    $pdfFilePath = 'images/information_PDF/Motohom_Caravans_Information.pdf'; // Replace with the actual path to your PDF
    if (file_exists($pdfFilePath)) {
        echo json_encode(["status" => "success", "pdfUrl" => $pdfFilePath]);
    } else {
        echo json_encode(["status" => "error", "message" => "PDF file not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "There was an issue saving your data."]);
}
?>
