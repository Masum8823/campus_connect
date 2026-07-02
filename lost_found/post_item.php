<?php
include '../config.php';
session_start();

// Redirect to login if not authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if(isset($_POST['post_item'])){
    $user_id = $_SESSION['user_id'];
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $status = $_POST['status'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    // Handle Image Upload
    $image_name = $_FILES['item_image']['name'];
    $target = "../uploads/items/" . time() . "_" . $image_name;
    $db_path = "uploads/items/" . time() . "_" . $image_name;

    // Create folder if not exists
    if (!file_exists('../uploads/items')) {
        mkdir('../uploads/items', 0777, true);
    }

    if(move_uploaded_file($_FILES['item_image']['tmp_name'], $target)){
        $query = "INSERT INTO lost_found (user_id, item_name, category, description, item_status, item_image, contact_info) 
                  VALUES ('$user_id', '$item_name', '$category', '$desc', '$status', '$db_path', '$contact')";
    } else {
        $query = "INSERT INTO lost_found (user_id, item_name, category, description, item_status, contact_info) 
                  VALUES ('$user_id', '$item_name', '$category', '$desc', '$status', '$contact')";
    }

    if(mysqli_query($conn, $query)){
        echo "<script>alert('Item Posted Successfully!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Item - Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 card p-4 shadow border-0">
                <h4 class="text-center text-primary mb-4">Post Lost or Found Item</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="item_name" class="form-control mb-3" placeholder="Item Name (e.g. Wallet, ID Card)" required>
                    <select name="category" class="form-control mb-3">
                        <option value="Electronics">Electronics</option>
                        <option value="Documents">Documents/Books</option>
                        <option value="Personal Items">Personal Items</option>
                        <option value="Others">Others</option>
                    </select>
                    <select name="status" class="form-control mb-3">
                        <option value="lost">I Lost This</option>
                        <option value="found">I Found This</option>
                    </select>
                    <textarea name="description" class="form-control mb-3" rows="3" placeholder="Description (Color, Location, etc.)" required></textarea>
                    <input type="text" name="contact" class="form-control mb-3" placeholder="Contact Info (Phone/Email)" required>
                    <label class="small text-muted">Upload Image (Optional)</label>
                    <input type="file" name="item_image" class="form-control mb-3">
                    <button name="post_item" class="btn btn-primary w-100 fw-bold">Post Item</button>
                </form>
                <a href="../user/dashboard.php" class="text-center d-block mt-3">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>