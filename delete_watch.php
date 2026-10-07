<?php
// Database connection
$servername = "gateway01.ap-northeast-1.prod.aws.tidbcloud.com";
$username = getenv("DB_USERNAME");
$password = getenv("DB_PASSWORD");
$dbname = "ecommerece";
$dbport = 4000;

// TiDB Cloud TLS configuration
$ssl_ca = __DIR__ . "/ca.pem";

$conn = mysqli_init();

mysqli_ssl_set(
    $conn,
    NULL,       // client key
    NULL,       // client certificate
    $ssl_ca,    // CA certificate
    NULL,
    NULL
);

mysqli_real_connect(
    $conn,
    $servername,
    $username,
    $password,
    $dbname,
    $dbport,
    NULL,
    MYSQLI_CLIENT_SSL
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
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
