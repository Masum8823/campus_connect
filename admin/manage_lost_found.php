<?php
include '../config.php';
// config.php-তে সেশন অলরেডি চেক করা আছে

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// ১. আইটেম ডিলিট করার লজিক (ডাটাবেস + ইমেজ ক্লিনআপ)
if(isset($_GET['delete_item'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_item']);
    
    // ইমেজ খুঁজে বের করা
    $img_query = mysqli_query($conn, "SELECT item_image FROM lost_found WHERE id='$id'");
    $img_data = mysqli_fetch_assoc($img_query);
    
    // সার্ভার থেকে ছবি মুছে ফেলা (no_image.png বাদে)
    if($img_data['item_image'] && $img_data['item_image'] != 'uploads/items/no_image.png' && file_exists("../".$img_data['item_image'])){
        unlink("../".$img_data['item_image']);
    }

    // ডাটাবেস থেকে ডিলিট
    if(mysqli_query($conn, "DELETE FROM lost_found WHERE id='$id'")){
        header("Location: manage_lost_found.php?msg=deleted");
        exit();
    }
}

// --- ২. সার্চ লজিক ইমপ্লিমেন্টেশন ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_sql = "";
if(!empty($search)){
    // আইটেম নাম, লোকেশন, ক্যাটাগরি অথবা ইউজারের নাম দিয়ে সার্চ
    $where_sql = " AND (lost_found.item_name LIKE '%$search%' 
                    OR lost_found.location LIKE '%$search%' 
                    OR lost_found.category LIKE '%$search%' 
                    OR users.full_name LIKE '%$search%')";
}

// ৩. পরিসংখ্যান (সবসময় টোটাল কাউন্ট দেখাবে)
$lost_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lost_found WHERE item_status='lost'"))['total'];
$found_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lost_found WHERE item_status='found'"))['total'];

// ৪. সব আইটেম তুলে আনা (সার্চ ফিল্টারসহ)
$all_items_query = "SELECT lost_found.*, users.full_name, users.dept FROM lost_found 
                    JOIN users ON lost_found.user_id = users.id 
                    WHERE 1=1 $where_sql
                    ORDER BY created_at DESC";
$all_items = mysqli_query($conn, $all_items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lost & Found | Admin Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1a1d20; --bg-light: #f4f7f6; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 20px; }
        
        /* Sidebar Styling */
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: var(--sidebar-bg); padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #adb5bd; padding: 12px; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; border: none; text-align: left; display: flex; align-items: center; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: var(--primary-color); color: white; }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        /* Stats & Search Styles */
        .stat-mini-card { border-radius: 20px; border: none; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); padding: 15px 25px; display: flex; align-items: center; }
        .search-container { background: white; border-radius: 15px; padding: 15px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #eee; margin-bottom: 25px; }
        .search-input { border: none; outline: none; background: #f8f9fa; border-radius: 50px; padding: 10px 20px 10px 45px; width: 100%; font-size: 14px; }

        /* Table Card Styling */
        .table-card { border-radius: 25px; border: none; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead { background-color: #f8f9fa; }
        .table thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; padding: 18px 15px; border: none; }
        .table tbody td { padding: 15px; border-bottom: 1px solid #f1f2f4; vertical-align: middle; }
        
        .item-img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 12px; border: 2px solid #eee; }
        .status-badge { font-size: 10px; font-weight: 800; padding: 5px 12px; border-radius: 50px; text-transform: uppercase; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people-fill"></i> <span>Manage Users</span></a>
            <a href="manage_lost_found.php" class="nav-link active"><i class="bi bi-search"></i> <span>Lost & Found</span></a>
            <a href="manage_academic.php" class="nav-link"><i class="bi bi-mortarboard-fill"></i> <span>Academic Hub</span></a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post"></i> <span>Content Moderation</span></a>
            <a href="manage_marketplace.php" class="nav-link"><i class="bi bi-shop"></i> <span>Marketplace</span></a>
            <a href="suggestions.php" class="nav-link"><i class="bi bi-lightbulb-fill"></i> <span>Suggestions</span></a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display"></i> <span>User View</span></a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-1">Lost & Found Moderation</h2>
                <p class="text-muted small">Search and manage campus lost & found reports.</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end gap-3">
                    <div class="stat-mini-card">
                        <div class="text-danger me-3 fs-4"><i class="bi bi-patch-question-fill"></i></div>
                        <div><h5 class="mb-0 fw-bold"><?php echo $lost_count; ?></h5><small class="text-muted">Lost</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="text-success me-3 fs-4"><i class="bi bi-bag-check-fill"></i></div>
                        <div><h5 class="mb-0 fw-bold"><?php echo $found_count; ?></h5><small class="text-muted">Found</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Search Bar -->
        <div class="search-container">
            <form method="GET" action="manage_lost_found.php" class="position-relative">
                <i class="bi bi-search position-absolute" style="left: 20px; top: 12px; color: #adb5bd;"></i>
                <div class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control search-input" placeholder="Search by item name, location, category or publisher..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 small">Report successfully removed from the system.</div>
        <?php endif; ?>

        <!-- Items Table -->
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Item Details</th>
                            <th>Status</th>
                            <th>Found At</th>
                            <th>Posted By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($all_items) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($all_items)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="../<?php echo $row['item_image']; ?>" class="item-img-preview me-3 shadow-sm border">
                                            <div>
                                                <span class="fw-bold text-dark d-block small"><?php echo $row['item_name']; ?></span>
                                                <small class="text-muted"><?php echo $row['category']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'; ?>">
                                            <?php echo $row['item_status']; ?>
                                        </span>
                                        <?php if($row['is_resolved']): ?>
                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill ms-1" style="font-size: 9px;">SOLVED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark"><?php echo $row['location']; ?></div>
                                        <small class="text-muted" style="font-size: 10px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?php echo $row['full_name']; ?></div>
                                        <small class="text-muted" style="font-size: 10px;"><?php echo $row['dept']; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete_item=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-danger rounded-circle px-2" onclick="return confirm('Permanently remove this report?')" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No reports found matching your search.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>