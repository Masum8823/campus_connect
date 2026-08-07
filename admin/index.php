<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// ১. সিস্টেম পরিসংখ্যান
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM posts"))['total'];
$total_notices = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM notices"))['total'];
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lost_found"))['total'];

// ২. রোল অনুযায়ী মেম্বার সংখ্যা
$stu_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='student'"))['total'];
$tea_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='teacher'"))['total'];
$alu_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='alumni'"))['total'];

// ৩. লেটেস্ট ৫ জন ইউজার
$recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #1a1d20; padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #adb5bd; padding: 12px; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; border: none; text-align: left; }
        .nav-link:hover, .nav-link.active { background: #0d6efd; color: white; }
        
        .stat-card { border-radius: 20px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; overflow: hidden; }
        .card-icon { position: absolute; right: 20px; top: 20px; font-size: 2rem; opacity: 0.15; }
        
        .table-card { border-radius: 20px; border: none; background: white; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .role-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Panel</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link active"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Manage Users</a>
            <a href="manage_lost_found.php" class="nav-link"><i class="bi bi-search me-2"></i> Lost & Found</a>
            <a href="manage_academic.php" class="nav-link"><i class="bi bi-mortarboard-fill me-2"></i> Academic Resources</a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <a href="suggestions.php" class="nav-link"><i class="bi bi-lightbulb-fill me-2 text-warning"></i> Suggestions</a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display me-2"></i> User View</a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power me-2"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">System Overview</h2>
            <p class="text-muted small">CampusConnect analytics and recent activities.</p>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white p-4">
                    <i class="bi bi-people card-icon"></i>
                    <h2 class="fw-bold"><?php echo $total_users; ?></h2>
                    <p class="mb-0 small fw-bold text-uppercase opacity-75">Members</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white p-4">
                    <i class="bi bi-chat-dots card-icon"></i>
                    <h2 class="fw-bold"><?php echo $total_posts; ?></h2>
                    <p class="mb-0 small fw-bold text-uppercase opacity-75">Posts</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark p-4">
                    <i class="bi bi-megaphone card-icon"></i>
                    <h2 class="fw-bold"><?php echo $total_notices; ?></h2>
                    <p class="mb-0 small fw-bold text-uppercase opacity-75">Notices</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white p-4">
                    <i class="bi bi-search card-icon"></i>
                    <h2 class="fw-bold"><?php echo $total_items; ?></h2>
                    <p class="mb-0 small fw-bold text-uppercase opacity-75">L&F Items</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Role Breakdown -->
            <div class="col-md-4 mb-4">
                <div class="card table-card p-4 h-100">
                    <h5 class="fw-bold mb-4">Member Breakdown</h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold"><span class="role-indicator bg-primary"></span> Students</span>
                            <span class="small fw-bold"><?php echo $stu_count; ?></span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo ($total_users > 0) ? ($stu_count/$total_users)*100 : 0; ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold"><span class="role-indicator bg-success"></span> Teachers</span>
                            <span class="small fw-bold"><?php echo $tea_count; ?></span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo ($total_users > 0) ? ($tea_count/$total_users)*100 : 0; ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold"><span class="role-indicator bg-purple" style="background:#6f42c1;"></span> Alumni</span>
                            <span class="small fw-bold"><?php echo $alu_count; ?></span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar" style="width: <?php echo ($total_users > 0) ? ($alu_count/$total_users)*100 : 0; ?>%; background:#6f42c1;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Recent Registrations -->
            <div class="col-md-8 mb-4">
                <div class="card table-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Recent Registrations</h5>
                        <a href="manage_users.php" class="btn btn-sm btn-light border px-3 rounded-pill fw-bold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold small text-dark"><?php echo $user['full_name']; ?></div>
                                        <small class="text-muted" style="font-size: 10px;"><?php echo $user['email']; ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-primary border border-primary border-opacity-10 small" style="font-size: 9px;"><?php echo strtoupper($user['role']); ?></span></td>
                                    <td><small class="text-muted" style="font-size: 10px;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small></td>
                                    <td class="text-end">
                                        <?php if($user['is_verified']): ?>
                                            <i class="bi bi-patch-check-fill text-success" title="Email Verified"></i>
                                        <?php else: ?>
                                            <i class="bi bi-clock-history text-warning" title="Verification Pending"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>