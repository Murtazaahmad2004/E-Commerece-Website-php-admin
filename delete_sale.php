<?php
// Show errors (remove after debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "u459954629_hostinger";
$password = "Root@2004@2004";
$dbname = "u459954629_ecommercestore";

// 1. Create DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// 2. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Check if ID exists in URL
if (!isset($_GET['id'])) {
    header("Location: manage_sales.php");
    exit();
}

$sale_id = intval($_GET['id']);

// 4. Delete query
$sql = "DELETE FROM sales WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_id);

if ($stmt->execute()) {
    echo "<script>alert('Sale deleted successfully!'); window.location='manage_sales.php';</script>";
} else {
    echo "<script>alert('Failed to delete sale!'); window.location='manage_sales.php';</script>";
}

// 5. Close connection
$stmt->close();
$conn->close();
?>
