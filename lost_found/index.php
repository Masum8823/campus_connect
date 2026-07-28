<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? ''; 
$sort = $_GET['sort'] ?? 'desc';

$sql = "SELECT lost_found.*, users.full_name, users.profile_pic FROM lost_found 
        JOIN users ON lost_found.user_id = users.id WHERE 1=1";

if($search) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (item_name LIKE '%$safe_search%' OR category LIKE '%$safe_search%' OR location LIKE '%$safe_search%')";
}

if($status_filter == 'lost') $sql .= " AND item_status = 'lost' AND is_resolved = 0";
elseif($status_filter == 'found') $sql .= " AND item_status = 'found' AND is_resolved = 0";
elseif($status_filter == 'resolved') $sql .= " AND is_resolved = 1";

$sql .= " ORDER BY created_at " . ($sort == 'asc' ? 'ASC' : 'DESC');
$items = mysqli_query($conn, $sql);

function getCategoryIcon($category) {
    switch ($category) {
        case 'Electronics': return 'bi-laptop';
        case 'Documents': return 'bi-file-earmark-medical';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f0f2f5; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }

        .sidebar-filter { border-radius: 20px; border: none; background: white; box-shadow: var(--card-shadow); padding: 25px; }
        .filter-title { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; color: #adb5bd; margin-bottom: 20px; display: block; }
        
        .filter-link { border-radius: 12px; margin-bottom: 6px; font-size: 14px; font-weight: 500; transition: 0.2s; border: none; padding: 12px 15px; color: #4b4f56; }
        .filter-link:hover { background-color: #f8f9fa; color: var(--primary-color); transform: translateX(5px); }
        .filter-link.active { background-color: #e7f3ff; color: var(--primary-color); }

        .item-card { border-radius: 22px; border: none; background: white; transition: all 0.3s ease; box-shadow: var(--card-shadow); overflow: hidden; height: 100%; display: flex; flex-direction: column; }
        .item-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        
        .img-container { height: 200px; overflow: hidden; position: relative; background: #eee; }
        .item-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .item-card:hover .item-img { transform: scale(1.1); }
        
        .status-badge { position: absolute; top: 15px; left: 15px; padding: 6px 15px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .resolved-overlay { position: absolute; inset: 0; background: rgba(25, 135, 84, 0.7); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; z-index: 3; backdrop-filter: blur(2px); }

        .location-tag { font-size: 11px; font-weight: 700; color: #d85140; background: #fff1f0; padding: 5px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; }
        .category-icon { width: 35px; height: 35px; background: #f0f7ff; color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }

        .publisher-info { background: #f8f9fa; border-radius: 12px; padding: 8px 12px; display: flex; align-items: center; margin-bottom: 15px; }
        .publisher-img-sm { width: 24px; height: 24px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd; }

        .search-wrapper { position: relative; margin-bottom: 20px; }
        .search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px 12px 12px 45px; font-size: 14px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">
                <i class="bi bi-search-heart me-2"></i>Lost & Found
            </a>
            <div class="ms-auto d-flex align-items-center">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4 shadow-sm me-2">Feed</a>
                <a href="post_item.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 shadow-sm text-dark">
                    <i class="bi bi-plus-lg me-1"></i> Post Item
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="sidebar-filter sticky-top" style="top: 100px;">
                    <span class="filter-title">Search & Filter</span>
                    <form method="GET">
                        <div class="search-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" class="form-control premium-input" placeholder="Find items..." value="<?php echo $search; ?>">
                        </div>

                        <div class="list-group list-group-flush mb-4">
                            <a href="?status=&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == '' ? 'active' : ''; ?>">All Reports</a>
                            <a href="?status=lost&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'lost' ? 'active' : ''; ?>">Lost Items</a>
                            <a href="?status=found&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'found' ? 'active' : ''; ?>">Found Items</a>
                            <a href="?status=resolved&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'resolved' ? 'active' : ''; ?>">Solved Cases</a>
                        </div>

                        <label class="small fw-bold text-muted mb-2 ps-1">Sort by Date</label>
                        <select name="sort" class="form-select border-0 bg-light rounded-3 mb-4" style="padding: 10px;" onchange="this.form.submit()">
                            <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Oldest First</option>
                        </select>

                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Update Hub</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8 col-lg-9">
                <div class="row">
                    <?php if(mysqli_num_rows($items) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($items)): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card item-card">
                                    <div class="img-container">
                                        <span class="status-badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger' : 'bg-primary'; ?> text-white">
                                            <?php echo $row['item_status']; ?>
                                        </span>

                                        <?php if($row['is_resolved'] == 1): ?>
                                            <div class="resolved-overlay">
                                                <i class="bi bi-check-circle-fill me-2"></i> RESOLVED
                                            </div>
                                        <?php endif; ?>

                                        <a href="view_item.php?id=<?php echo $row['id']; ?>">
                                            <?php if($row['item_image'] != 'uploads/items/no_image.png'): ?>
                                                <img src="../<?php echo $row['item_image']; ?>" class="item-img <?php echo ($row['is_resolved'] == 1) ? 'filter: grayscale(100%);' : ''; ?>">
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted">
                                                    <i class="bi <?php echo getCategoryIcon($row['category']); ?> display-2 opacity-25"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="category-icon" title="<?php echo $row['category']; ?>">
                                                <i class="bi <?php echo getCategoryIcon($row['category']); ?>"></i>
                                            </div>
                                            <div class="location-tag">
                                                <i class="bi bi-geo-alt-fill"></i> <?php echo $row['location']; ?>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-2 <?php echo ($row['is_resolved'] == 1) ? 'text-muted text-decoration-line-through' : ''; ?>">
                                            <?php echo $row['item_name']; ?>
                                        </h6>

                                        <p class="text-secondary small mb-3" style="height: 40px; overflow: hidden; line-height: 1.5;">
                                            <?php echo nl2br(substr($row['description'], 0, 70)); ?>...
                                        </p>

                                        <!-- NEW: Publisher Name Section -->
                                        <div class="publisher-info">
                                            <?php $p_pic = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                            <img src="<?php echo $p_pic; ?>" class="publisher-img-sm me-2 shadow-sm">
                                            <small class="text-dark fw-bold" style="font-size: 11px;"><?php echo $row['full_name']; ?></small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-clock text-muted me-1" style="font-size: 12px;"></i>
                                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d', strtotime($row['created_at'])); ?></small>
                                            </div>
                                            <a href="view_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-link text-primary fw-bold p-0 text-decoration-none">Details <i class="bi bi-arrow-right"></i></a>
                                        </div>

                                        <?php if($row['user_id'] == $_SESSION['user_id']): ?>
                                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border flex-grow-1 text-secondary" style="font-size: 11px;"><i class="bi bi-pencil"></i></a>
                                                <a href="delete_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border flex-grow-1 text-danger" onclick="return confirm('Delete?')" style="font-size: 11px;"><i class="bi bi-trash"></i></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-search display-1 text-muted opacity-25"></i>
                            <h5 class="mt-3 text-muted fw-bold">No results found</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>