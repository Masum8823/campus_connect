<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// সার্চ এবং সর্টিং লজিক
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

// SQL কুয়েরি (সার্চসহ)
$sql = "SELECT notices.*, users.full_name FROM notices 
        JOIN users ON notices.user_id = users.id WHERE 1=1";

if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (title LIKE '%$safe_search%' OR description LIKE '%$safe_search%')";
}

$sql .= " ORDER BY created_at " . ($sort == 'asc' ? 'ASC' : 'DESC');
$notices_query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Notices - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .page-header { background: white; padding: 30px 0; margin-bottom: 30px; border-bottom: 1px solid #dee2e6; }
        .notice-card { border-radius: 20px; border: none; transition: all 0.3s ease; background: white; border-left: 5px solid transparent; }
        .notice-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .notice-card.priority-high { border-left-color: #dc3545; } /* লাল বর্ডার */
        .notice-card.priority-normal { border-left-color: #0d6efd; } /* নীল বর্ডার */
        
        .search-container { border-radius: 15px; background: white; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .search-input { border-radius: 50px; background: #f0f2f5; border: none; padding-left: 45px; }
        .search-icon { position: absolute; left: 20px; top: 10px; color: #aaa; }
        
        .date-badge { background: #f8f9fa; border-radius: 10px; padding: 10px; text-align: center; min-width: 70px; }
        .badge-audience { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; padding: 5px 12px; border-radius: 50px; }
    </style>
</head>
<body>

    <!-- Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php"><i class="bi bi-megaphone-fill me-2"></i>Notices</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3">Back to Feed</a>
        </div>
    </nav>

    <div class="container">
        <!-- Search & Header Section -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <h2 class="fw-bold text-dark mb-1">Notice Board</h2>
                <p class="text-muted">Stay updated with the latest official announcements from your department.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-3">
                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                    <a href="add_notice.php" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Post New Notice
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Left Sidebar Search -->
            <div class="col-md-4 mb-4">
                <div class="search-container shadow-sm">
                    <h6 class="fw-bold mb-3">Search & Filter</h6>
                    <form method="GET">
                        <div class="position-relative mb-3">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="search" class="form-control search-input" placeholder="Search notices..." value="<?php echo $search; ?>">
                        </div>
                        <select name="sort" class="form-select rounded-pill mb-3" onchange="this.form.submit()">
                            <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Apply Filter</button>
                    </form>
                </div>
            </div>

            <!-- Notice Feed -->
            <div class="col-md-8">
                <?php if(mysqli_num_rows($notices_query) > 0): ?>
                    <?php while($notice = mysqli_fetch_assoc($notices_query)): ?>
                        <div class="card notice-card shadow-sm mb-4 priority-normal">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <!-- Date Box -->
                                    <div class="date-badge border">
                                        <h4 class="mb-0 fw-bold"><?php echo date('d', strtotime($notice['created_at'])); ?></h4>
                                        <small class="text-muted text-uppercase"><?php echo date('M', strtotime($notice['created_at'])); ?></small>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <!-- Audience Badge -->
                                            <span class="badge bg-primary-subtle text-primary badge-audience">
                                                <i class="bi bi-people-fill me-1"></i> For: <?php echo $notice['target_audience']; ?>
                                            </span>
                                            <small class="text-muted"><?php echo date('Y', strtotime($notice['created_at'])); ?></small>
                                        </div>

                                        <h4 class="fw-bold text-dark mb-2"><?php echo $notice['title']; ?></h4>
                                        <p class="text-secondary small mb-3">
                                            <?php echo nl2br(substr($notice['description'], 0, 180)); ?>...
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-primary me-2"></i>
                                                <small class="text-dark fw-semibold"><?php echo $notice['full_name']; ?></small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="view_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Read Full Notice</a>
                                                
                                                <?php if(($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin') && $notice['user_id'] == $_SESSION['user_id']): ?>
                                                    <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light text-secondary rounded-circle"><i class="bi bi-pencil"></i></a>
                                                    <a href="delete_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi- megaphone display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">No notices found.</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>