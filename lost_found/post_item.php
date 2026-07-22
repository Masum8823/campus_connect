<?php
include '../config.php';
session_start();

// 1. Check if user is authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if(isset($_POST['post_item'])){
    $user_id = $_SESSION['user_id'];
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $location = mysqli_real_escape_string($conn, $_POST['location']); // New Location Field
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $status = $_POST['status'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    // Default image if not uploaded
    $db_image_path = "uploads/items/no_image.png";

    // 2. Handle Image Upload logic
    if (!empty($_FILES['item_image']['name'])) {
        $file_name = time() . "_" . $_FILES['item_image']['name'];
        $target = "../uploads/items/" . $file_name;
        $db_image_path = "uploads/items/" . $file_name;

        // Create folder if not exists
        if (!file_exists('../uploads/items')) {
            mkdir('../uploads/items', 0777, true);
        }
        move_uploaded_file($_FILES['item_image']['tmp_name'], $target);
    }

    // 3. Insert into lost_found table
    $query = "INSERT INTO lost_found (user_id, item_name, category, location, description, item_status, item_image, contact_info) 
              VALUES ('$user_id', '$item_name', '$category', '$location', '$desc', '$status', '$db_image_path', '$contact')";
    
    if(mysqli_query($conn, $query)){
        // 4. AUTO-ANNOUNCE: Create a post in the main campus feed automatically
        $status_text = ($status == 'lost') ? "LOST my " : "FOUND a ";
        $feed_content = "[L&F ANNOUNCEMENT] 📢 I have " . $status_text . $item_name . " at " . $location . ". Please check the Lost & Found section for details. \nContact: " . $contact;
        
        mysqli_query($conn, "INSERT INTO posts (user_id, content) VALUES ('$user_id', '$feed_content')");

        echo "<script>alert('Item Posted & Announced on Campus Feed!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Item - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 50px; padding-bottom: 50px; }
        .post-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card post-card p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Post Lost/Found Item</h2>
                        <p class="text-muted small">Help your campus community by sharing details</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <!-- Item Name -->
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item_name" class="form-control" placeholder="e.g. CASIO Calculator, Blue Wallet" required>
                        </div>

                        <div class="row">
                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Documents">Documents/Books</option>
                                    <option value="Personal Items">Personal Items</option>
                                    <option value="Wallets/Bags">Wallets/Bags</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="lost" class="text-danger">I Lost This</option>
                                    <option value="found" class="text-success">I Found This</option>
                                </select>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label class="form-label">Where was it lost/found?</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Library, Canteen, Room 402" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Detailed Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Color, brand, unique marks, etc." required></textarea>
                        </div>

                        <!-- Contact -->
                        <div class="mb-3">
                            <label class="form-label">Your Contact Info</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone-fill text-primary"></i></span>
                                <input type="text" name="contact" class="form-control" placeholder="Phone number or Email" required>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label class="form-label">Upload Photo (Optional)</label>
                            <input type="file" name="item_image" class="form-control" accept="image/*">
                        </div>

                        <!-- Submit Button -->
                        <button name="post_item" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow">
                            <i class="bi bi-send-check-fill me-2"></i> PUBLISH POST
                        </button>
                    </form>

                    <div class="text-center mt-4 d-flex justify-content-center gap-3">
                        <a href="index.php" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left"></i> Back to Hub</a>
                        <span class="text-muted">|</span>
                        <a href="../user/dashboard.php" class="text-decoration-none small text-primary fw-bold">Dashboard Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>