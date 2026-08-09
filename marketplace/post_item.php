<?php
include '../config.php';
// সেশন config.php তেই স্টার্ট করা আছে

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_POST['post_ad'])){
    $seller_id = $_SESSION['user_id'];
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = $_POST['category'];
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $price_type = $_POST['price_type'];
    $condition = mysqli_real_escape_string($conn, $_POST['item_condition']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // ইমেজ আপলোড লজিক
    $db_image_path = "uploads/marketplace/no_product.png";
    if(!empty($_FILES['product_img']['name'])){
        $img_name = time() . "_" . $_FILES['product_img']['name'];
        $target = "../uploads/marketplace/" . $img_name;
        if(move_uploaded_file($_FILES['product_img']['tmp_name'], $target)){
            $db_image_path = "uploads/marketplace/" . $img_name;
        }
    }

    $query = "INSERT INTO marketplace_items (seller_id, item_name, category, price, price_type, item_condition, description, item_image) 
              VALUES ('$seller_id', '$item_name', '$category', '$price', '$price_type', '$condition', '$desc', '$db_image_path')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Your Ad has been posted successfully!'); window.location='index.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Ad | Campus Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 50px 0; }
        .form-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card p-4 p-md-5 bg-white">
                    <div class="text-center mb-5">
                        <i class="bi bi-bag-plus-fill text-warning display-4"></i>
                        <h2 class="fw-bold mt-2">Sell Something on Campus</h2>
                        <p class="text-muted">Turn your used items into cash easily</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Item Name</label>
                            <input type="text" name="item_name" class="form-control premium-input" placeholder="e.g. Calculus Book, Scientific Calculator" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Category</label>
                                <select name="category" class="form-select premium-input">
                                    <option value="Books">Books</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Cycles">Cycles</option>
                                    <option value="Hostel Essentials">Hostel Essentials</option>
                                    <option value="Lab Tools">Lab Tools</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Condition</label>
                                <input type="text" name="item_condition" class="form-control premium-input" placeholder="e.g. 9/10 or Used 1 year" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Price (BDT)</label>
                                <input type="number" name="price" class="form-control premium-input" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Pricing Type</label>
                                <select name="price_type" class="form-select premium-input">
                                    <option value="Fixed">Fixed Price</option>
                                    <option value="Negotiable">Negotiable</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Description</label>
                            <textarea name="description" class="form-control premium-input" rows="4" placeholder="Mention why you are selling and other details..." required></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold small text-uppercase">Upload Item Image</label>
                            <input type="file" name="product_img" class="form-control premium-input" accept="image/*">
                        </div>

                        <div class="d-grid">
                            <button name="post_ad" class="btn btn-warning btn-lg fw-bold rounded-pill shadow py-3 text-dark">
                                PUBLISH MY AD
                            </button>
                            <a href="index.php" class="btn btn-link text-muted mt-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>