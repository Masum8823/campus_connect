<?php
include '../config.php';
// সেশন অলরেডি config.php-তে আছে

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
    <title>User Suggestions | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 40px; }
        .suggestion-card { border-radius: 20px; border: none; transition: 0.3s; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 5px solid #ddd; }
        
        /* স্ট্যাটাস অনুযায়ী বর্ডার কালার */
        .status-new { border-top-color: #0d6efd; }
        .status-reviewed { border-top-color: #ffc107; }
        .status-implemented { border-top-color: #198754; }

        .nav-link { color: #ccc; padding: 12px; border-radius: 10px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #0d6efd; color: white; }
        .btn-status { font-size: 10px; font-weight: 700; border-radius: 50px; padding: 2px 10px; }
    </style>
</head>
<body>

    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <a href="suggestions.php" class="nav-link active"><i class="bi bi-lightbulb-fill me-2 text-warning"></i> Suggestions</a>
            <hr>
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">User Suggestions</h2>
            <span class="badge bg-dark px-3 py-2 rounded-pill"><?php echo mysqli_num_rows($res); ?> Feedbacks</span>
        </div>

        <div class="row">
            <?php while($row = mysqli_fetch_assoc($res)): 
                // স্ট্যাটাস অনুযায়ী ক্লাস নির্ধারণ
                $status_class = "status-" . $row['status'];
                $badge_class = ($row['status'] == 'new') ? 'bg-primary' : (($row['status'] == 'reviewed') ? 'bg-warning text-dark' : 'bg-success');
            ?>
                <div class="col-md-6 mb-4">
                    <div class="card suggestion-card h-100 p-4 <?php echo $status_class; ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge <?php echo $badge_class; ?> rounded-pill mb-2 small text-uppercase" style="font-size: 9px; letter-spacing: 1px;">
                                <?php echo $row['status']; ?>
                            </span>
                            <div class="dropdown">
                                <i class="bi bi-three-dots-vertical text-muted" role="button" data-bs-toggle="dropdown"></i>
                                <ul class="dropdown-menu shadow border-0">
                                    <li><a class="dropdown-item small" href="?update_status=reviewed&id=<?php echo $row['id']; ?>">Mark as Reviewed</a></li>
                                    <li><a class="dropdown-item small" href="?update_status=implemented&id=<?php echo $row['id']; ?>">Mark as Implemented</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item small text-danger" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete Forever</a></li>
                                </ul>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-3"><?php echo $row['subject']; ?></h5>
                        <p class="text-secondary small mb-4" style="line-height: 1.6;"><?php echo nl2br($row['suggestion_text']); ?></p>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <div class="small">
                                <?php if($row['is_anonymous']): ?>
                                    <span class="text-muted italic small"><i class="bi bi-eye-slash"></i> Anonymous User</span>
                                <?php else: ?>
                                    <strong class="text-dark"><?php echo $row['full_name']; ?></strong>
                                    <div class="text-muted" style="font-size: 10px;"><?php echo $row['dept']; ?> | <?php echo $row['university_id']; ?></div>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted" style="font-size: 10px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>