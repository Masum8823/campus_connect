<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all notices
$notices_query = mysqli_query($conn, "SELECT notices.*, users.full_name FROM notices JOIN users ON notices.user_id = users.id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Notices - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; }
        .notice-card { border-radius: 15px; border: none; transition: 0.3s; }
        .notice-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Feed</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Official Notices</h3>
            <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                <a href="add_notice.php" class="btn btn-primary fw-bold">+ Post New Notice</a>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($notices_query) > 0): ?>
                <?php while($notice = mysqli_fetch_assoc($notices_query)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card notice-card shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-primary fw-bold"><i class="bi bi-person-fill"></i> <?php echo $notice['full_name']; ?></small>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></small>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo $notice['title']; ?></h5>
                                <p class="text-muted small"><?php echo nl2br(substr($notice['description'], 0, 200)); ?>...</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <a href="view_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-outline-primary px-4 rounded-pill">Read More</a>
                                    
                                    <?php if(($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin') && $notice['user_id'] == $_SESSION['user_id']): ?>
                                        <div>
                                            <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light text-secondary"><i class="bi bi-pencil"></i></a>
                                            <a href="delete_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Delete this notice?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-megaphone display-1 text-muted"></i>
                    <p class="mt-3 text-muted">No official notices found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>