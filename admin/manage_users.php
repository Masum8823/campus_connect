<?php
include '../config.php';
// সেশন অলরেডি config.php-তে চেক করা আছে

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// ১. রোল পরিবর্তন করার লজিক
if(isset($_POST['update_role'])){
    $u_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
    if(mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE id='$u_id'")){
        header("Location: manage_users.php?msg=role_updated");
        exit();
    }
}

// ২. ম্যানুয়াল ভেরিফিকেশন লজিক
if(isset($_GET['verify'])){
    $u_id = mysqli_real_escape_string($conn, $_GET['verify']);
    mysqli_query($conn, "UPDATE users SET is_verified=1 WHERE id='$u_id'");
    header("Location: manage_users.php?msg=verified");
    exit();
}

// ৩. ইউজার ডিলিট লজিক
if(isset($_GET['delete_user'])){
    $u_id = mysqli_real_escape_string($conn, $_GET['delete_user']);
    
    $u_q = mysqli_query($conn, "SELECT profile_pic FROM users WHERE id='$u_id'");
    $u_d = mysqli_fetch_assoc($u_q);
    if($u_d['profile_pic'] && $u_d['profile_pic'] != 'default.png' && file_exists("../".$u_d['profile_pic'])){
        unlink("../".$u_d['profile_pic']);
    }
    
    mysqli_query($conn, "DELETE FROM users WHERE id='$u_id'");
    header("Location: manage_users.php?msg=deleted");
    exit();
}

// --- ৪. সার্চ এবং রোল ফিল্টার লজিক ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$role_filter = isset($_GET['role_filter']) ? mysqli_real_escape_string($conn, $_GET['role_filter']) : '';

$where_sql = "WHERE role != 'admin'"; // ডিফল্টভাবে অ্যাডমিনদের দেখাবে না

if(!empty($search)){
    $where_sql .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%' OR university_id LIKE '%$search%')";
}

if(!empty($role_filter)){
    $where_sql .= " AND role = '$role_filter'";
}

// সব ইউজার তুলে আনা
$users = mysqli_query($conn, "SELECT * FROM users $where_sql ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin Hub</title>
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

        /* Search & Filter Container */
        .search-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #eee; margin-bottom: 25px; }
        .premium-input { border-radius: 50px; background: #f8f9fa; border: 1px solid #eee; padding: 10px 20px 10px 45px; font-size: 14px; }
        .search-icon { position: absolute; left: 15px; top: 12px; color: #adb5bd; }

        /* Table Styling */
        .table-card { border-radius: 25px; border: none; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead { background-color: #f8f9fa; }
        .table thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; padding: 18px 15px; border: none; }
        .table tbody td { padding: 15px; border-bottom: 1px solid #f1f2f4; vertical-align: middle; }
        
        .user-avatar-sm { width: 40px; height: 40px; object-fit: cover; border-radius: 12px; border: 2px solid #eee; }
        .role-select { border-radius: 10px; font-size: 13px; border: 1px solid #eee; background: #f8f9fa; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
            <a href="manage_users.php" class="nav-link active"><i class="bi bi-people-fill"></i> <span>Manage Users</span></a>
            <a href="manage_lost_found.php" class="nav-link"><i class="bi bi-search"></i> <span>Lost & Found</span></a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">User Management</h2>
                <p class="text-muted small">Search, filter, and manage campus member accounts.</p>
            </div>
            <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 small">
                <i class="bi bi-people-fill text-primary me-1"></i> <?php echo mysqli_num_rows($users); ?> Members Shown
            </span>
        </div>

        <!-- Premium Search & Filter Bar -->
        <div class="search-container">
            <form method="GET" action="manage_users.php">
                <div class="row g-2">
                    <div class="col-md-7 position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control premium-input" placeholder="Search by name, email or university ID..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="role_filter" class="form-select rounded-pill shadow-sm py-2 px-3 border-0 bg-light" style="font-size: 14px;" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            <option value="student" <?php if($role_filter == 'student') echo 'selected'; ?>>Student</option>
                            <option value="teacher" <?php if($role_filter == 'teacher') echo 'selected'; ?>>Teacher</option>
                            <option value="alumni" <?php if($role_filter == 'alumni') echo 'selected'; ?>>Alumni</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 small">Action completed successfully.</div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">User Profile</th>
                            <th>Department</th>
                            <th>Email Status</th>
                            <th>Change Role</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($users) > 0): ?>
                            <?php while($u = mysqli_fetch_assoc($users)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <?php $img = ($u['profile_pic'] != 'default.png') ? "../" . $u['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($u['full_name']); ?>
                                            <img src="<?php echo $img; ?>" class="user-avatar-sm me-3 shadow-sm border">
                                            <div>
                                                <div class="fw-bold text-dark small"><?php echo $u['full_name']; ?></div>
                                                <small class="text-muted" style="font-size: 10px;">ID: <?php echo $u['university_id']; ?> | <?php echo $u['email']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border small fw-normal"><?php echo $u['dept']; ?></span>
                                    </td>
                                    <td>
                                        <?php if($u['is_verified']): ?>
                                            <span class="text-success small fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                                        <?php else: ?>
                                            <a href="?verify=<?php echo $u['id']; ?>" class="btn btn-warning btn-sm fw-bold rounded-pill shadow-sm" style="font-size: 9px; padding: 2px 10px;">Verify Now</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <select name="new_role" class="form-select form-select-sm role-select shadow-sm" style="width: 105px;">
                                                <option value="student" <?php if($u['role'] == 'student') echo 'selected'; ?>>Student</option>
                                                <option value="teacher" <?php if($u['role'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                                                <option value="alumni" <?php if($u['role'] == 'alumni') echo 'selected'; ?>>Alumni</option>
                                            </select>
                                            <button name="update_role" class="btn btn-sm btn-dark rounded-circle px-2" title="Confirm Update"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <a href="?delete_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-light border text-danger rounded-circle px-2 shadow-sm" onclick="return confirm('Delete account permanently?')" title="Delete Account">
                                            <i class="bi bi-person-x-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No campus members found matching your search.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>