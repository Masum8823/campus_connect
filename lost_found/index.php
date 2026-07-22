<?php
include '../config.php';
session_start();

// 1. Auth Check
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// 2. Filter Parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? ''; // lost, found, resolved
$sort = $_GET['sort'] ?? 'desc';

// 3. Dynamic SQL Query
$sql = "SELECT lost_found.*, users.full_name FROM lost_found 
        JOIN users ON lost_found.user_id = users.id WHERE 1=1";

if($search) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    // Searching name, category, or location
    $sql .= " AND (item_name LIKE '%$safe_search%' OR category LIKE '%$safe_search%' OR location LIKE '%$safe_search%')";
}

if($status_filter == 'lost') {
    $sql .= " AND item_status = 'lost' AND is_resolved = 0";
} elseif($status_filter == 'found') {
    $sql .= " AND item_status = 'found' AND is_resolved = 0";
} elseif($status_filter == 'resolved') {
    $sql .= " AND is_resolved = 1";
}

$sql .= " ORDER BY created_at " . ($sort == 'asc' ? 'ASC' : 'DESC');
$items = mysqli_query($conn, $sql);

// 4. Helper Function for Icons
function getCategoryIcon($category) {
    switch ($category) {
        case 'Electronics': return 'bi-laptop';
        case 'Documents': return 'bi-file-earmark-text';
        case 'Personal Items': return 'bi-person-badge';
        case 'Wallets/Bags': return 'bi-wallet2';
        default: return 'bi-box-seam';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .sidebar-card { border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .item-card { transition: all 0.3s ease; border: none; border-radius: 22px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: white; }
        .item-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }
        
        .location-badge { background: #fff1f0; color: #d85140; font-size: 11px; padding: 4px 12px; border-radius: 8px; font-weight: 700; display: inline-block; }
        .category-icon-box { width: 38px; height: 38px; background: #f0f7ff; color: #0d6efd; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        
        .filter-link { border-radius: 12px; margin-bottom: 6px; font-size: 14px; transition: 0.2s; border: none; padding: 10px 15px; }
        .filter-link.active { background-color: #0d6efd !important; color: white !important; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
        .search-input { border-radius: 50px; background: #f0f2f5; border: none; padding-left: 45px; height: 45px; }
        .resolved-tag { background: #198754; color: white; padding: 5px 15px; border-radius: 0 0 0 15px; font-size: 10px; font-weight: 800; position: absolute; top: 0; right: 0; z-index: 5; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            <div class="d-flex align-items-center">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3 me-2">Dashboard</a>
                <a href="post_item.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm">+ Post Item</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card sidebar-card p-3 bg-white sticky-top" style="top: 100px;">
                    <h6 class="fw-bold mb-3 px-2"><i class="bi bi-funnel-fill text-primary"></i> Filter Feed</h6>
                    
                    <form method="GET" action="">
                        <div class="position-relative mb-4">
                            <i class="bi bi-search position-absolute" style="left: 18px; top: 13px; color: #aaa;"></i>
                            <input type="text" name="search" class="form-control search-input" placeholder="Search items..." value="<?php echo $search; ?>">
                        </div>

                        <div class="list-group list-group-flush mb-4">
                            <a href="?status=&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == '' ? 'active' : ''; ?>">
                                <i class="bi bi-grid-fill me-2"></i> All Resources
                            </a>
                            <a href="?status=lost&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'lost' ? 'active' : ''; ?>">
                                <i class="bi bi-patch-question-fill me-2"></i> Missing Items
                            </a>
                            <a href="?status=found&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'found' ? 'active' : ''; ?>">
                                <i class="bi bi-bag-check-fill me-2"></i> Found Items
                            </a>
                            <a href="?status=resolved&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'resolved' ? 'active' : ''; ?>">
                                <i class="bi bi-check-circle-fill me-2"></i> Solved Cases
                            </a>
                        </div>

                        <div class="mb-4 px-2">
                            <label class="small fw-bold text-muted mb-2">Sort Order</label>
                            <select name="sort" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                <option value="desc" <?php echo $sort == 'desc' ? 'selected' : ''; ?>>Newest Uploads</option>
                                <option value="asc" <?php echo $sort == 'asc' ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill py-2 fw-bold">Update Results</button>
                    </form>
                </div>
            </div>

            <!-- Feed Area -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <h4 class="fw-bold text-dark mb-0">
                        <?php 
                            if($status_filter == 'lost') echo "Lost Items";
                            elseif($status_filter == 'found') echo "Found Items";
                            elseif($status_filter == 'resolved') echo "Resolved Cases";
                            else echo "Lost & Found Feed";
                        ?>
                    </h4>
                    <span class="badge bg-secondary rounded-pill px-3"><?php echo mysqli_num_rows($items); ?> Items Total</span>
                </div>

                <div class="row">
                    <?php if(mysqli_num_rows($items) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($items)): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 item-card position-relative">
                                    
                                    <?php if($row['is_resolved'] == 1): ?>
                                        <div class="resolved-tag shadow-sm"><i class="bi bi-check-lg"></i> RESOLVED</div>
                                    <?php endif; ?>

                                    <!-- Image Container with Grayscale for Resolved -->
                                    <div class="position-relative" style="height: 180px; overflow: hidden; background: #eee;">
                                        <a href="view_item.php?id=<?php echo $row['id']; ?>">
                                            <?php if($row['item_image'] != 'uploads/items/no_image.png'): ?>
                                                <img src="../<?php echo $row['item_image']; ?>" class="h-100 w-100" style="object-fit: cover; <?php echo ($row['is_resolved'] == 1) ? 'filter: grayscale(100%); opacity: 0.6;' : ''; ?>">
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                                    <i class="bi <?php echo getCategoryIcon($row['category']); ?> display-2 opacity-25"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                    <div class="card-body d-flex flex-column p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="category-icon-box" title="<?php echo $row['category']; ?>">
                                                <i class="bi <?php echo getCategoryIcon($row['category']); ?>"></i>
                                            </div>
                                            <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'; ?> text-uppercase border-0 px-3" style="font-size: 9px; letter-spacing: 0.5px;">
                                                <?php echo $row['item_status']; ?>
                                            </span>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-2 <?php echo ($row['is_resolved'] == 1) ? 'text-muted text-decoration-line-through' : ''; ?>">
                                            <?php echo $row['item_name']; ?>
                                        </h6>
                                        
                                        <!-- New: Location Badge -->
                                        <div class="mb-3">
                                            <span class="location-badge">
                                                <i class="bi bi-geo-alt-fill me-1"></i> <?php echo $row['location']; ?>
                                            </span>
                                        </div>

                                        <p class="text-muted small mb-4 flex-grow-1" style="font-size: 12px; line-height: 1.5;">
                                            <?php echo nl2br(substr($row['description'], 0, 65)); ?>...
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-primary me-2" style="font-size: 14px;"></i>
                                                <small class="text-dark fw-bold" style="font-size: 11px;"><?php echo explode(' ', $row['full_name'])[0]; ?></small>
                                            </div>
                                            <a href="view_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-link text-primary fw-bold p-0 text-decoration-none" style="font-size: 12px;">Details <i class="bi bi-arrow-right"></i></a>
                                        </div>

                                        <?php if($row['user_id'] == $_SESSION['user_id']): ?>
                                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary flex-grow-1 border-0 bg-light" style="font-size: 10px;"><i class="bi bi-pencil"></i> Edit</a>
                                                <a href="delete_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger flex-grow-1 border-0 bg-light" onclick="return confirm('Delete?')" style="font-size: 10px;"><i class="bi bi-trash"></i></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                            <h5 class="mt-3 text-muted fw-bold">No results found</h5>
                            <p class="text-muted small">Try adjusting your filters or search keywords.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>