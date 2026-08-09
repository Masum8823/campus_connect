<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

// ফিল্টার প্যারামিটার ধরা
$search = $_GET['search'] ?? '';
$cat_filter = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

// কুয়েরি তৈরি: সেলারের নামসহ সব আইটেম আনা
$sql = "SELECT marketplace_items.*, users.full_name, users.profile_pic FROM marketplace_items 
        JOIN users ON marketplace_items.seller_id = users.id WHERE status = 'available'";

if($search) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (item_name LIKE '%$safe_search%' OR description LIKE '%$safe_search%')";
}

if($cat_filter) {
    $safe_cat = mysqli_real_escape_string($conn, $cat_filter);
    $sql .= " AND category = '$safe_cat'";
}

$sql .= " ORDER BY created_at " . ($sort == 'asc' ? 'ASC' : 'DESC');
$items = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Marketplace | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }

        /* Navbar */
        .navbar { background: #0d6efd !important; }

        /* Search & Filter Bar */
        .filter-section { background: white; border-radius: 20px; padding: 20px; box-shadow: var(--card-shadow); margin-bottom: 30px; }
        .search-input { border-radius: 50px; background: #f0f2f5; border: 1px solid #eee; padding-left: 45px; }

        /* Product Card Style */
        .product-card { border-radius: 22px; border: none; background: white; transition: 0.3s; box-shadow: var(--card-shadow); overflow: hidden; height: 100%; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        
        .img-container { height: 220px; overflow: hidden; position: relative; background: #eee; }
        .product-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .product-card:hover .product-img { transform: scale(1.1); }
        
        .price-badge { position: absolute; bottom: 15px; left: 15px; background: rgba(0, 0, 0, 0.7); color: white; padding: 5px 15px; border-radius: 50px; font-weight: 700; backdrop-filter: blur(5px); }
        .cat-tag { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 10px; border-radius: 50px; background: #fff4e6; color: #d9480f; }
        
        .seller-info { background: #f8f9fa; border-radius: 12px; padding: 8px 12px; display: flex; align-items: center; margin-top: 15px; }
        .seller-img { width: 28px; height: 28px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">
                <i class="bi bi-cart-check-fill me-2"></i>Campus Marketplace
            </a>
            <div class="ms-auto">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4 me-2">Dashboard</a>
                <a href="post_item.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 shadow-sm text-dark">+ Sell Item</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <!-- Search and Filter Bar -->
        <div class="filter-section mt-4">
            <form method="GET" class="row g-3">
                <div class="col-md-5 position-relative">
                    <i class="bi bi-search position-absolute" style="left: 30px; top: 12px; color: #aaa;"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="What are you looking for?" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select rounded-pill shadow-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="Books" <?php if($cat_filter == 'Books') echo 'selected'; ?>>Books</option>
                        <option value="Electronics" <?php if($cat_filter == 'Electronics') echo 'selected'; ?>>Electronics</option>
                        <option value="Cycles" <?php if($cat_filter == 'Cycles') echo 'selected'; ?>>Cycles</option>
                        <option value="Hostel Essentials" <?php if($cat_filter == 'Hostel Essentials') echo 'selected'; ?>>Hostel Essentials</option>
                        <option value="Lab Tools" <?php if($cat_filter == 'Lab Tools') echo 'selected'; ?>>Lab Tools</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select rounded-pill shadow-sm" onchange="this.form.submit()">
                        <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest</option>
                        <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Search</button>
                </div>
            </form>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($items) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($items)): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card">
                            <div class="img-container">
                                <img src="../<?php echo $row['item_image']; ?>" class="product-img" alt="Product Image">
                                <div class="price-badge">৳ <?php echo number_format($row['price']); ?></div>
                            </div>

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="cat-tag"><?php echo $row['category']; ?></span>
                                    <small class="text-muted fw-bold"><?php echo $row['price_type']; ?></small>
                                </div>

                                <h5 class="fw-bold text-dark mb-1 text-truncate"><?php echo $row['item_name']; ?></h5>
                                <p class="text-muted small mb-3"><i class="bi bi-info-circle"></i> Condition: <?php echo $row['item_condition']; ?></p>
                                
                                <p class="text-secondary small" style="height: 40px; overflow: hidden;"><?php echo nl2br(substr($row['description'], 0, 75)); ?>...</p>

                                <div class="seller-info">
                                    <?php $p_pic = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                    <img src="<?php echo $p_pic; ?>" class="seller-img me-2">
                                    <small class="text-dark fw-bold">Seller: <?php echo explode(' ', $row['full_name'])[0]; ?></small>
                                </div>

                                <div class="mt-4">
                                    <a href="view_item.php?id=<?php echo $row['id']; ?>" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm py-2">
                                        VIEW DETAILS
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-shop display-1 text-muted opacity-25"></i>
                    <h4 class="mt-3 text-muted fw-bold">Marketplace is empty!</h4>
                    <p class="text-muted small">Be the first one to sell something on campus.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>