<?php
$servername = "localhost";
$username = "u459954629_hostinger";
$password = "Root@2004@2004";
$dbname = "u459954629_ecommercestore";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die(json_encode(["success"=>false, "error"=>$conn->connect_error]));

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i",$id);
    if($stmt->execute()){
        echo json_encode(["success"=>true]);
    } else {
        echo json_encode(["success"=>false,"error"=>$stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success"=>false,"error"=>"ID not provided"]);
}

$conn->close();
?>
