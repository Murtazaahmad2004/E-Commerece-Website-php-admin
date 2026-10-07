<?php
$servername = "localhost";               
$username = "u459954629_hostinger";     
$password = "Root@2004@2004";          
$dbname = "u459954629_ecommercestore";  

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: manage_sales.php");
    exit();
}

$sale_id = intval($_GET['id']);

// Get current status
$sql = "SELECT status FROM sales WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<script>alert('Sale not found!'); window.location='manage_sales.php';</script>";
    exit();
}

$current_status = $row['status'];
$new_status = ($current_status === "active") ? "inactive" : "active";

// Update new status
$update = "UPDATE sales SET status = ? WHERE id = ?";
$stmt2 = $conn->prepare($update);
$stmt2->bind_param("si", $new_status, $sale_id);
$stmt2->execute();

echo "<script>alert('Sale status changed to $new_status'); window.location='manage_sales.php';</script>";

// Close connections
$stmt->close();
$stmt2->close();
$conn->close();
?>
