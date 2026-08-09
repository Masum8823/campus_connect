<?php
include '../config.php';


if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$id = $_GET['id'];
$u_id = $_SESSION['user_id'];

// ১. ডাটা তুলে আনা এবং মালিকানা চেক করা
$query = mysqli_query($conn, "SELECT * FROM marketplace_items WHERE id='$id' AND seller_id='$u_id'");
$item = mysqli_fetch_assoc($query);

if(!$item){ header("Location: index.php"); exit(); }

// ২. আপডেট লজিক
if(isset($_POST['update_ad'])){
    $name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $price_type = $_POST['price_type'];
    $condition = mysqli_real_escape_string($conn, $_POST['item_condition']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $category = $_POST['category'];

    $image_sql = "";
    if(!empty($_FILES['product_img']['name'])){
        $img_name = time() . "_" . $_FILES['product_img']['name'];
        $target = "../uploads/marketplace/" . $img_name;
        if(move_uploaded_file($_FILES['product_img']['tmp_name'], $target)){
            $image_sql = ", item_image='uploads/marketplace/$img_name'";
        }
    }

    $update = "UPDATE marketplace_items SET 
               item_name='$name', price='$price', price_type='$price_type', 
               item_condition='$condition', description='$desc', category='$category' 
               $image_sql WHERE id='$id'";
    
    if(mysqli_query($conn, $update)){
        echo "<script>alert('Ad Updated Successfully!'); window.location='index.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Ad | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 50px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card edit-card p-4 p-md-5 bg-white">
                    <h2 class="fw-bold text-primary mb-4">Edit Your Advertisement</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">ITEM NAME</label>
                            <input type="text" name="item_name" class="form-control premium-input" value="<?php echo $item['item_name']; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">CATEGORY</label>
                                <select name="category" class="form-select premium-input">
                                    <option value="Books" <?php if($item['category']=='Books') echo 'selected'; ?>>Books</option>
                                    <option value="Electronics" <?php if($item['category']=='Electronics') echo 'selected'; ?>>Electronics</option>
                                    <option value="Cycles" <?php if($item['category']=='Cycles') echo 'selected'; ?>>Cycles</option>
                                    <option value="Hostel Essentials" <?php if($item['category']=='Hostel Essentials') echo 'selected'; ?>>Hostel Essentials</option>
                                    <option value="Lab Tools" <?php if($item['category']=='Lab Tools') echo 'selected'; ?>>Lab Tools</option>
                                    <option value="Others" <?php if($item['category']=='Others') echo 'selected'; ?>>Others</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">CONDITION</label>
                                <input type="text" name="item_condition" class="form-control premium-input" value="<?php echo $item['item_condition']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">PRICE (BDT)</label>
                                <input type="number" name="price" class="form-control premium-input" value="<?php echo $item['price']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">PRICING TYPE</label>
                                <select name="price_type" class="form-select premium-input">
                                    <option value="Fixed" <?php if($item['price_type']=='Fixed') echo 'selected'; ?>>Fixed</option>
                                    <option value="Negotiable" <?php if($item['price_type']=='Negotiable') echo 'selected'; ?>>Negotiable</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted">DESCRIPTION</label>
                            <textarea name="description" class="form-control premium-input" rows="4" required><?php echo $item['description']; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold small text-muted">UPDATE IMAGE (OPTIONAL)</label>
                            <input type="file" name="product_img" class="form-control premium-input" accept="image/*">
                        </div>

                        <div class="d-grid gap-2">
                            <button name="update_ad" class="btn btn-primary btn-lg fw-bold rounded-pill shadow">SAVE CHANGES</button>
                            <a href="index.php" class="btn btn-link text-muted">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>