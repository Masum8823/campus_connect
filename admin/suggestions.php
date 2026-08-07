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

// ৩. স্ট্যাটাস অনুযায়ী কাউন্ট আনা
$new_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='new'"))['total'];
$reviewed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='reviewed'"))['total'];
$done_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suggestions WHERE status='implemented'"))['total'];

// সব সাজেশন ডাটাবেস থেকে তুলে আনা
$query = "SELECT suggestions.*, users.full_name, users.dept, users.university_id 
          FROM suggestions 
          JOIN users ON suggestions.user_id = users.id 
          ORDER BY created_at DESC";
$res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Suggestions | Admin Hub</title>
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

        /* Stat Mini Cards */
        .stat-mini-card { border-radius: 15px; border: none; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); padding: 15px; display: flex; align-items: center; }

        /* Suggestion Card Style */
        .suggestion-card { border-radius: 25px; border: none; transition: all 0.3s ease; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border-top: 6px solid #dee2e6; }
        .suggestion-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        
        /* Status Colors */
        .status-new { border-top-color: #0d6efd; }
        .status-reviewed { border-top-color: #ffc107; }
        .status-implemented { border-top-color: #198754; }

        .anon-badge { background: #6c757d; color: white; font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 50px; }
        .dropdown-menu { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 8px; }
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
            <a href="suggestions.php" class="nav-link active"><i class="bi bi-lightbulb-fill text-warning"></i> <span>Suggestions</span></a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display"></i> <span>User View</span></a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="row align-items-center mb-5">
            <div class="col-md-5">
                <h2 class="fw-bold text-dark mb-1">User Feedback</h2>
                <p class="text-muted small">Improve CampusConnect based on user ideas.</p>
            </div>
            <div class="col-md-7">
                <div class="d-flex justify-content-end gap-2">
                    <div class="stat-mini-card">
                        <div class="text-primary me-2"><i class="bi bi-plus-circle-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold"><?php echo $new_count; ?></h6><small class="text-muted">New</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="text-warning me-2"><i class="bi bi-eye-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold"><?php echo $reviewed_count; ?></h6><small class="text-muted">Reviewed</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="text-success me-2"><i class="bi bi-check-circle-fill"></i></div>
                        <div><h6 class="mb-0 fw-bold"><?php echo $done_count; ?></h6><small class="text-muted">Implemented</small></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 small">Action performed successfully.</div>
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
                                        <strong class="text-dark d-block"><?php echo $row['full_name']; ?></strong>
                                        <small class="text-muted"><?php echo $row['dept']; ?> | ID: <?php echo $row['university_id']; ?></small>
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
                    <h5 class="mt-3 text-muted fw-bold">No feedback collected yet.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>