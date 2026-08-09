<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$item_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// ইউজারের তথ্যসহ পণ্যের বিস্তারিত আনা
$query = "SELECT marketplace_items.*, users.full_name, users.dept, users.profile_pic, users.id as user_id 
          FROM marketplace_items 
          JOIN users ON marketplace_items.seller_id = users.id 
          WHERE marketplace_items.id = '$item_id'";
$res = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($res);

if(!$item){ echo "Product not found!"; exit(); }

$seller_pic = ($item['profile_pic'] != 'default.png') ? "../" . $item['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($item['full_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $item['item_name']; ?> | Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 100px; }
        .product-img-full { width: 100%; max-height: 500px; object-fit: contain; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .details-card { border-radius: 25px; border: none; background: white; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .price-text { font-size: 2.5rem; font-weight: 800; color: #0d6efd; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i> Back to Marketplace</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row g-4">
            <!-- Left: Image -->
            <div class="col-md-6">
                <img src="../<?php echo $item['item_image']; ?>" class="product-img-full border">
            </div>

            <!-- Right: Details -->
            <div class="col-md-6">
                <div class="details-card">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-2"><?php echo $item['category']; ?></span>
                    <h1 class="fw-bold text-dark mb-1"><?php echo $item['item_name']; ?></h1>
                    <div class="price-text mb-4">৳ <?php echo number_format($item['price']); ?> <small class="text-muted fs-6 fw-normal">(<?php echo $item['price_type']; ?>)</small></div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase">Condition</h6>
                        <p class="fw-bold text-dark"><?php echo $item['item_condition']; ?></p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase">Description</h6>
                        <p class="text-secondary"><?php echo nl2br($item['description']); ?></p>
                    </div>

                    <!-- Seller Info -->
                    <div class="d-flex align-items-center p-3 bg-light rounded-4 mb-4 border">
                        <img src="<?php echo $seller_pic; ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit:cover;">
                        <div>
                            <small class="text-muted d-block small">Listed By</small>
                            <h6 class="mb-0 fw-bold"><?php echo $item['full_name']; ?></h6>
                            <small class="text-muted"><?php echo $item['dept']; ?> Department</small>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <?php if($item['seller_id'] == $current_user_id): ?>
                            <!-- যদি নিজের পণ্য হয় -->
                            <a href="edit_item.php?id=<?php echo $item_id; ?>" class="btn btn-dark btn-lg rounded-pill fw-bold">EDIT MY AD</a>
                            <a href="delete_item.php?id=<?php echo $item_id; ?>" class="btn btn-outline-danger btn-sm border-0 mt-1" onclick="return confirm('Delete Ad?')">Remove Ad</a>
                        <?php else: ?>
                            <!-- যদি অন্যের পণ্য হয় (Chat Integration) -->
                            <a href="../user/profile.php?id=<?php echo $item['seller_id']; ?>" class="btn btn-primary btn-lg rounded-pill fw-bold shadow">
                                <i class="bi bi-chat-dots-fill me-2"></i> CONTACT SELLER
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>