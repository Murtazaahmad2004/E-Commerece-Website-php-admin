<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "u459954629_hostinger", "Root@2004@2004", "u459954629_ecommercestore");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => $conn->connect_error]);
    exit;
}

$id = intval($_GET['id']);
$status = $_GET['status'];

$sql = "UPDATE orders SET status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
