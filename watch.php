<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database config
$servername = "localhost";
$username   = "u459954629_hostinger";
$password   = "Root@2004@2004";
$dbname     = "u459954629_ecommercestore";

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

// Allowed image types
$allowed_extensions = ['jpg','jpeg','png','gif','webp'];
$upload_dir = __DIR__ . "/uploads/";  
$public_path = "uploads/"; // relative path for display in admin/home

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'] ?? 0;
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $img_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_name = $_FILES['image']['name'];
        $tmp_name  = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_extensions)) {
            $new_filename = uniqid('watch_', true) . "." . $ext;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($tmp_name, $destination)) {
                $img_path = $public_path . $new_filename; // store relative path
            } else {
                $_SESSION['message'] = "❌ Failed to save image!";
                header("Location: watch.php"); exit;
            }
        } else {
            $_SESSION['message'] = "❌ Invalid image type!";
            header("Location: watch.php"); exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO watches (name, image, description, price, sale_price, stock, category) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssddis", $name, $img_path, $description, $price, $sale_price, $stock, $category);
    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Watch added successfully!";
        header("Location: admin.php"); exit;
    } else {
        $_SESSION['message'] = "❌ Database error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Watches</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" sizes="32x32" href="https://wristwin.shop/static/icon.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://wristwin.shop/static/icon.png">
<style>
/* Copy your CSS from Flask template */
body { font-family:"Poppins",sans-serif; margin:0; padding:40px 20px; min-height:100vh; background: linear-gradient(135deg,#1b2735,#2c3e50,#243b55); background-size:400% 400%; animation:gradientFlow 18s ease infinite; display:flex; justify-content:center; align-items:flex-start; color:#eaeaea; overflow:hidden; }
@keyframes gradientFlow {0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
.form-container { margin-top:50px; background: rgba(255,255,255,0.06); backdrop-filter: blur(14px); border:1px solid rgba(255,255,255,0.12); border-radius:18px; padding:40px 45px; max-width:750px; width:100%; box-shadow:0 10px 35px rgba(0,0,0,0.6); animation:fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
h2 { text-align:center; color:#00c6ff; font-size:28px; font-weight:700; text-transform:uppercase; margin-bottom:25px; text-shadow:0 0 18px rgba(0,198,255,0.5); display:flex; align-items:center; justify-content:center; gap:10px; }
form { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px 25px; }
label { font-size:13px; font-weight:600; color:#f0f0f0; display:block; margin-bottom:6px; }
input, textarea, select { width:350px; padding:10px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.18); background: rgba(255,255,255,0.08); color:#fff; font-size:14px; outline:none; transition:0.3s; }
input:focus, textarea:focus, select:focus { border-color:#00c6ff; box-shadow:0 0 6px rgba(0,198,255,0.4); }
textarea { resize:none; height:90px; }
.btn-group { grid-column: span 2; display:flex; justify-content:center; align-items:center; gap:15px; margin-top:10px; }
.btn-submit { flex:1; max-width:250px; text-align:center; padding:12px; border-radius:10px; font-size:15px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; cursor:pointer; border:none; background: linear-gradient(90deg,#00c6ff,#0072ff); color:#fff; box-shadow:0 5px 20px rgba(0,198,255,0.35); transition:0.3s; }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(0,198,255,0.45); }
.btn-back { flex:1; max-width:250px; text-align:center; padding:12px; border-radius:10px; font-size:15px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; cursor:pointer; background: rgba(255,255,255,0.1); color:#fff; text-decoration:none; transition:0.3s; }
.btn-back:hover { background: rgba(255,255,255,0.2); transform:translateY(-2px); }
@media(max-width:600px){form{grid-template-columns:1fr;} .btn-group{grid-column:span 1; flex-direction:column;} .btn-submit, .btn-back{max-width:100%; width:100%;}}
</style>
</head>
<body>

<?php
if (isset($_SESSION['message'])) {
    echo '<div class="alert" style="position:fixed;top:20px;right:20px;background:rgba(0,0,0,0.7);color:#00ff00;padding:12px 18px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.3);z-index:1000;">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
?>

<div class="form-container">
<h2><i class="fa-solid fa-clock"></i> Add New Watch</h2>
<form method="POST" enctype="multipart/form-data">
    <div>
        <label for="name"><i class="fa-solid fa-font"></i> Watch Name</label>
        <input type="text" name="name" id="name" placeholder="Enter Watch Name" required>
    </div>
    <div>
        <label for="price"><i class="fa-solid fa-tag"></i> Regular Price</label>
        <input type="number" step="0.01" name="price" id="price" placeholder="Enter Regular Price" required>
    </div>
    <div>
        <label for="sale_price"><i class="fa-solid fa-dollar-sign"></i> Sale Price</label>
        <input type="number" step="0.01" name="sale_price" id="sale_price" placeholder="Enter Sale Price">
    </div>
    <div>
        <label for="stock"><i class="fa-solid fa-boxes-stacked"></i> Stock Quantity</label>
        <input type="number" name="stock" id="stock" placeholder="Enter Stock Quantity" required>
    </div>
    <div>
        <label for="category"><i class="fa-solid fa-venus-mars"></i> Category</label>
        <select name="category" id="category" required>
            <option value="" disabled selected>Select Category</option>
            <option value="Men">Men</option>
            <option value="Women">Women</option>
        </select>
    </div>
    <div style="grid-column: span 2;">
        <label for="description"><i class="fa-solid fa-align-left"></i> Description</label>
        <textarea name="description" id="description" placeholder="Enter Description" required></textarea>
    </div>
    <div style="grid-column: span 2;">
        <label for="image"><i class="fa-solid fa-image"></i> Product Image</label>
        <input type="file" name="image" id="image" accept="image/*" required>
    </div>
    <div class="btn-group">
        <button type="submit" class="btn-submit"><i class="fa-solid fa-upload"></i> Upload Watch</button>
        <a href="admin.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</form>
</div>

<script>
document.addEventListener("DOMContentLoaded",()=>{
    const alerts=document.querySelectorAll('.alert');
    alerts.forEach(alert=>setTimeout(()=>alert.remove(),3000));
});
</script>

</body>
</html>
