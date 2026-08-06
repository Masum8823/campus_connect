<?php
include '../config.php';
// config.php তে সেশন স্টার্ট করা আছে

// সিকিউরিটি চেক: শুধু অ্যাডমিন ঢুকতে পারবে
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// সাজেশন ডিলিট করার লজিক
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
    <title>User Suggestions | Admin Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 40px; }
        .suggestion-card { border-radius: 20px; border: none; transition: 0.3s; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .suggestion-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .nav-link { color: #ccc; padding: 12px; border-radius: 10px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #0d6efd; color: white; }
        .anon-badge { background: #6c757d; color: white; font-size: 10px; padding: 3px 10px; border-radius: 50px; }
    </style>
</head>
<body>

    <!-- Sidebar (অন্যান্য অ্যাডমিন পেজের মতোই) -->
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
            <div>
                <h2 class="fw-bold text-dark mb-1">User Feedback Hub</h2>
                <p class="text-muted">Review what students and teachers think about the platform.</p>
            </div>
            <span class="badge bg-dark px-3 py-2 rounded-pill"><?php echo mysqli_num_rows($res); ?> Total Feedbacks</span>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 py-2 small">Suggestion removed from the list.</div>
        <?php endif; ?>

        <div class="row">
            <?php if(mysqli_num_rows($res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card suggestion-card h-100 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="fw-bold text-primary mb-0"><?php echo $row['subject']; ?></h5>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border-0 text-danger" onclick="return confirm('Permanently delete this suggestion?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                            <p class="text-dark small mb-4" style="line-height: 1.6; min-height: 60px;">
                                <?php echo nl2br($row['suggestion_text']); ?>
                            </p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <?php if($row['is_anonymous']): ?>
                                        <span class="anon-badge"><i class="bi bi-eye-slash-fill me-1"></i> Anonymous User</span>
                                    <?php else: ?>
                                        <div class="small">
                                            <span class="fw-bold text-dark d-block"><?php echo $row['full_name']; ?></span>
                                            <small class="text-muted"><?php echo $row['dept']; ?> | <?php echo $row['university_id']; ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-clock"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-smile display-1 text-muted opacity-25"></i>
                    <h4 class="mt-3 text-muted">No suggestions yet. You're doing a great job!</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>