<?php
include '../config.php';
// সেশন অলরেডি config.php-তে চেক করা আছে

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// ১. স্ট্যাটাস আপডেট করার লজিক
if(isset($_GET['update_status']) && isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $new_status = mysqli_real_escape_string($conn, $_GET['update_status']);
    
    mysqli_query($conn, "UPDATE suggestions SET status='$new_status' WHERE id='$id'");
    header("Location: suggestions.php?msg=status_updated");
    exit();
}

// ২. সাজেশন ডিলিট করার লজিক
if(isset($_GET['delete'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM suggestions WHERE id='$id'");
    header("Location: suggestions.php?msg=deleted");
    exit();
}

// --- ৩. সার্চ এবং স্ট্যাটাস ফিল্টার লজিক ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';

$where_sql = "WHERE 1=1";

if(!empty($search)){
    // বিষয়, সাজেশন টেক্সট অথবা ইউজারের নাম দিয়ে সার্চ (নাম শুধু তখনই সার্চ হবে যখন anonymous না)
    $where_sql .= " AND (suggestions.subject LIKE '%$search%' 
                    OR suggestions.suggestion_text LIKE '%$search%' 
                    OR (users.full_name LIKE '%$search%' AND suggestions.is_anonymous = 0))";
}

if(!empty($filter)){
    $where_sql .= " AND suggestions.status = '$filter'";
}

// ৪. স্ট্যাটাস অনুযায়ী কাউন্ট আনা (সবসময় টোটাল দেখাবে)
$new_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='new'"))['total'];
$reviewed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='reviewed'"))['total'];
$done_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='implemented'"))['total'];

// ৫. সব সাজেশন তুলে আনা (সার্চ ফিল্টারসহ)
$query = "SELECT suggestions.*, users.full_name, users.dept, users.university_id 
          FROM suggestions 
          JOIN users ON suggestions.user_id = users.id 
          $where_sql
          ORDER BY created_at DESC";
$res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggestion Moderation | Admin Hub</title>
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
        .stat-mini-card { border-radius: 20px; border: none; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); padding: 15px 20px; display: flex; align-items: center; }
        .search-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #eee; margin-bottom: 25px; }
        .premium-input { border-radius: 50px; background: #f8f9fa; border: 1px solid #eee; padding: 10px 20px 10px 45px; font-size: 14px; }
        .search-icon { position: absolute; left: 15px; top: 12px; color: #adb5bd; }

        /* Suggestion Card Style */
        .suggestion-card { border-radius: 25px; border: none; transition: all 0.3s ease; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 6px solid #dee2e6; }
        .suggestion-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        
        .status-new { border-top-color: #0d6efd; }
        .status-reviewed { border-top-color: #ffc107; }
        .status-implemented { border-top-color: #198754; }

        .anon-badge { background: #6c757d; color: white; font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 50px; }
        .dropdown-menu { border-radius: 15px; border: none; box-shadow: 0 5px 25px rgba(0,0,0,0.1); padding: 8px; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people-fill"></i> <span>Manage Users</span></a>
            <a href="manage_lost_found.php" class="nav-link"><i class="bi bi-search"></i> <span>Lost & Found</span></a>
            <a href="manage_academic.php" class="nav-link"><i class="bi bi-mortarboard-fill"></i> <span>Academic Hub</span></a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post"></i> <span>Content Moderation</span></a>
            <a href="manage_marketplace.php" class="nav-link"><i class="bi bi-shop"></i> <span>Marketplace</span></a>
            <a href="suggestions.php" class="nav-link active"><i class="bi bi-lightbulb-fill"></i> <span>Suggestions</span></a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display"></i> <span>User View</span></a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="row align-items-center mb-4">
            <div class="col-md-5">
                <h2 class="fw-bold text-dark mb-1">User Feedback</h2>
                <p class="text-muted small">Search and manage user-submitted suggestions.</p>
            </div>
            <div class="col-md-7">
                <div class="d-flex justify-content-end gap-2">
                    <div class="stat-mini-card">
                        <div class="text-primary me-2"><i class="bi bi-plus-circle-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold small"><?php echo $new_count; ?></h6><small class="text-muted" style="font-size:10px;">New</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="text-warning me-2"><i class="bi bi-eye-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold small"><?php echo $reviewed_count; ?></h6><small class="text-muted" style="font-size:10px;">Reviewed</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="text-success me-2"><i class="bi bi-check-circle-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold small"><?php echo $done_count; ?></h6><small class="text-muted" style="font-size:10px;">Implemented</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Search & Filter Bar -->
        <div class="search-container">
            <form method="GET" action="suggestions.php">
                <div class="row g-2">
                    <div class="col-md-7 position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control premium-input" placeholder="Search by subject, text or user name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="filter" class="form-select rounded-pill shadow-sm py-2 px-3 border-0 bg-light" style="font-size: 14px;" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="new" <?php if($filter == 'new') echo 'selected'; ?>>New Feedback</option>
                            <option value="reviewed" <?php if($filter == 'reviewed') echo 'selected'; ?>>Reviewed</option>
                            <option value="implemented" <?php if($filter == 'implemented') echo 'selected'; ?>>Implemented</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 small">Operation successful.</div>
        <?php endif; ?>

        <!-- Suggestions Grid -->
        <div class="row">
            <?php if(mysqli_num_rows($res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res)): 
                    $status_class = "status-" . $row['status'];
                    $badge_class = ($row['status'] == 'new') ? 'bg-primary' : (($row['status'] == 'reviewed') ? 'bg-warning text-dark' : 'bg-success');
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card suggestion-card h-100 p-4 <?php echo $status_class; ?>">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge <?php echo $badge_class; ?> rounded-pill small text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">
                                    <?php echo $row['status']; ?>
                                </span>
                                <div class="dropdown">
                                    <i class="bi bi-three-dots-vertical text-muted p-1" role="button" data-bs-toggle="dropdown"></i>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item small" href="?update_status=reviewed&id=<?php echo $row['id']; ?>">Mark as Reviewed</a></li>
                                        <li><a class="dropdown-item small" href="?update_status=implemented&id=<?php echo $row['id']; ?>">Mark as Implemented</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item small text-danger" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete forever?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-3"><?php echo $row['subject']; ?></h5>
                            <p class="text-secondary small mb-4 flex-grow-1" style="line-height: 1.6;">
                                <?php echo nl2br($row['suggestion_text']); ?>
                            </p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div class="small">
                                    <?php if($row['is_anonymous']): ?>
                                        <span class="anon-badge"><i class="bi bi-eye-slash-fill me-1"></i> Anonymous</span>
                                    <?php else: ?>
                                        <strong class="text-dark"><?php echo $row['full_name']; ?></strong>
                                        <div class="text-muted" style="font-size: 10px;"><?php echo $row['dept']; ?> | ID: <?php echo $row['university_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted" style="font-size: 10px;">
                                    <i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-chat-right-heart display-1 text-muted opacity-25"></i>
                    <h5 class="mt-3 text-muted fw-bold">No matching suggestions found.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>