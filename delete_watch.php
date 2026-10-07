<?php
// -----------------------
// Database Connection
// -----------------------
$servername = "localhost";               // Usually localhost on Hostinger
$username = "u459954629_hostinger";     // Your MySQL user
$password = "Root@2004@2004";          // Your MySQL password
$dbname = "u459954629_ecommercestore";  // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Connection Check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -----------------------
// Validate Watch ID
// -----------------------
if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid watch ID!'); window.location='admin.php';</script>";
    exit();
}

$watch_id = intval($_GET['id']);

// -----------------------
// Fetch Watch Image
// -----------------------
$sql = "SELECT image FROM watches WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $watch_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Watch not found!'); window.location='admin.php';</script>";
    exit();
}

$watch = $result->fetch_assoc();
$imagePath = $watch['image'];

$stmt->close();

// -----------------------
// Delete Image from File System
// -----------------------

// Default image (don’t delete this)
$DEFAULT_IMAGE = "uploads/default.png";

// If a custom image exists → delete it
if (!empty($imagePath) && $imagePath !== $DEFAULT_IMAGE) {
    $fullPath = __DIR__ . "/" . $imagePath;

    if (file_exists($fullPath)) {
        unlink($fullPath);  // Delete image file
    }
}

// -----------------------
// Delete Watch from DB
// -----------------------
$sql_delete = "DELETE FROM watches WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $watch_id);

if ($stmt_delete->execute()) {
    echo "<script>alert('🗑️ Watch deleted successfully!'); window.location='admin.php';</script>";
} else {
    echo "<script>alert('❌ Failed to delete watch!'); window.location='admin.php';</script>";
}

$stmt_delete->close();
$conn->close();
?>
