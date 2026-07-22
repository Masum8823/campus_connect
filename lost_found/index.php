<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? ''; // lost, found, resolved
$sort = $_GET['sort'] ?? 'desc';

$sql = "SELECT lost_found.*, users.full_name FROM lost_found 
        JOIN users ON lost_found.user_id = users.id WHERE 1=1";

if($search) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (item_name LIKE '%$safe_search%' OR category LIKE '%$safe_search%')";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found Hub - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .sidebar-card { border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .item-card { transition: all 0.3s ease; border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .item-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }
        .resolved-badge { background: #198754; color: white; padding: 5px 15px; border-radius: 50px; font-size: 11px; font-weight: bold; }
        .filter-link { border-radius: 10px; margin-bottom: 5px; font-size: 14px; transition: 0.2s; }
        .filter-link.active { background-color: #0d6efd !important; color: white !important; }
        .search-input { border-radius: 50px; background: #f0f2f5; border: none; padding-left: 40px; }
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
                <a href="post_item.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">+ Post Item</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3 mb-4">
                <div class="card sidebar-card p-3 bg-white">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel"></i> Filters</h6>
                    
                    <form method="GET" action="">
                        <!-- Search Input -->
                        <div class="position-relative mb-3">
                            <i class="bi bi-search position-absolute" style="left: 15px; top: 10px; color: #aaa;"></i>
                            <input type="text" name="search" class="form-control search-input" placeholder="Search items..." value="<?php echo $search; ?>">
                        </div>

                        <!-- Status Links -->
                        <div class="list-group list-group-flush mb-3">
                            <a href="?status=&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == '' ? 'active' : ''; ?>">All Items</a>
                            <a href="?status=lost&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'lost' ? 'active' : ''; ?>">Lost Items</a>
                            <a href="?status=found&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'found' ? 'active' : ''; ?>">Found Items</a>
                            <a href="?status=resolved&search=<?php echo $search; ?>" class="list-group-item list-group-item-action filter-link <?php echo $status_filter == 'resolved' ? 'active' : ''; ?>">Resolved Case</a>
                        </div>

                        <!-- Sort Selection -->
                        <div class="mb-3 px-2">
                            <label class="small fw-bold text-muted mb-1">Sort By Date</label>
                            <select name="sort" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                <option value="desc" <?php echo $sort == 'desc' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="asc" <?php echo $sort == 'asc' ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">Apply Search</button>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <h4 class="fw-bold text-dark">
                        <?php 
                            if($status_filter == 'lost') echo "Lost Items Feed";
                            elseif($status_filter == 'found') echo "Found Items Feed";
                            elseif($status_filter == 'resolved') echo "Resolved Cases";
                            else echo "Lost & Found Feed";
                        ?>
                    </h4>
                    <span class="badge bg-secondary rounded-pill"><?php echo mysqli_num_rows($items); ?> Total Items</span>
                </div>

                <div class="row">
                    <?php if(mysqli_num_rows($items) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($items)): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 item-card">
                                    
                                    <!-- Resolved Status Overlay -->
                                    <?php if($row['is_resolved'] == 1): ?>
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                                            <span class="resolved-badge shadow-sm"><i class="bi bi-check-circle-fill"></i> RESOLVED</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Clickable Image Container -->
                                    <div class="position-relative" style="height: 200px; overflow: hidden; background: #eee;">
                                        <a href="view_item.php?id=<?php echo $row['id']; ?>">
                                            <?php if($row['item_image'] != 'no_image.png' && !empty($row['item_image'])): ?>
                                                <img src="../<?php echo $row['item_image']; ?>" class="card-img-top h-100 w-100" style="object-fit: cover; <?php echo ($row['is_resolved'] == 1) ? 'filter: grayscale(100%); opacity: 0.7;' : ''; ?>">
                                            <?php else: ?>
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-image text-muted display-1"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-2">
                                            <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-success-subtle text-success border-success-subtle'; ?> border px-3 rounded-pill" style="font-size: 10px; text-transform: uppercase;">
                                                <i class="bi <?php echo $row['item_status'] == 'lost' ? 'bi-exclamation-triangle' : 'bi-check-circle'; ?>"></i> <?php echo $row['item_status']; ?>
                                            </span>
                                        </div>

                                        <h5 class="card-title fw-bold text-dark <?php echo ($row['is_resolved'] == 1) ? 'text-muted text-decoration-line-through' : ''; ?>">
                                            <?php echo $row['item_name']; ?>
                                        </h5>
                                        
                                        <p class="card-text small text-secondary flex-grow-1">
                                            <?php echo nl2br(substr($row['description'], 0, 70)); ?>...
                                        </p>
                                        
                                        <div class="mb-3 pt-2 border-top">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-primary me-2"></i>
                                                <small class="text-dark fw-bold"><?php echo $row['full_name']; ?></small>
                                            </div>
                                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                        </div>

                                        <a href="view_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary w-100 fw-bold rounded-pill shadow-sm">View Details</a>

                                        <?php if($row['user_id'] == $_SESSION['user_id']): ?>
                                            <div class="d-flex gap-2 mt-2">
                                                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary flex-grow-1" style="font-size: 11px;">Edit Status</a>
                                                <a href="delete_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No matching items found.</h4>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>