<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

$sql = "SELECT notices.*, users.full_name, users.profile_pic FROM notices 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Notices | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-light: #f0f2f5;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding-top: 80px; 
        }

        /* Sidebar Glassmorphism Style */
        .sidebar-filter {
            border-radius: 20px;
            border: none;
            background: white;
            box-shadow: var(--card-shadow);
            padding: 25px;
        }

        .filter-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #adb5bd;
            margin-bottom: 20px;
            display: block;
        }

        /* Notice Card Premium Style */
        .notice-card {
            border-radius: 22px;
            border: none;
            background: white;
            transition: all 0.3s ease;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            overflow: hidden;
            border-left: 6px solid var(--primary-color);
        }

        .notice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }

        /* Date Calendar Icon Style */
        .date-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 12px;
            text-align: center;
            min-width: 80px;
            border: 1px solid #eee;
        }

        .date-day { font-size: 24px; font-weight: 800; color: #333; line-height: 1; }
        .date-month { font-size: 11px; font-weight: 700; color: var(--primary-color); text-transform: uppercase; }

        /* Search Input Style */
        .search-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        .premium-input {
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            padding: 12px 12px 12px 45px;
            font-size: 14px;
        }

        .premium-input:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: var(--primary-color);
        }

        .badge-custom {
            font-size: 10px;
            font-weight: 700;
            padding: 6px 15px;
            border-radius: 50px;
            text-transform: uppercase;
        }

        .publisher-img-sm {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

    <!-- Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">
                <i class="bi bi-megaphone-fill me-2"></i>Notices
            </a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4 shadow-sm">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h2 class="fw-extrabold text-dark mb-1">Campus Announcements</h2>
                <p class="text-muted">Stay informed with official updates from your university.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                    <a href="add_notice.php" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow">
                        <i class="bi bi-plus-lg me-1"></i> New Notice
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="sidebar-filter">
                    <span class="filter-title">Search & Filter</span>
                    <form method="GET">
                        <div class="search-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" class="form-control premium-input" placeholder="Search notices..." value="<?php echo $search; ?>">
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2 ps-1">Sort by Date</label>
                            <select name="sort" class="form-select border-0 bg-light rounded-3" style="padding: 10px;" onchange="this.form.submit()">
                                <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest First</option>
                                <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Notice Feed -->
            <div class="col-md-8 col-lg-9">
                <?php if(mysqli_num_rows($notices_query) > 0): ?>
                    <?php while($notice = mysqli_fetch_assoc($notices_query)): ?>
                        <div class="card notice-card">
                            <div class="card-body p-4">
                                <div class="d-flex gap-4">
                                    <!-- Date Box -->
                                    <div class="date-box d-none d-sm-block">
                                        <div class="date-day"><?php echo date('d', strtotime($notice['created_at'])); ?></div>
                                        <div class="date-month"><?php echo date('M', strtotime($notice['created_at'])); ?></div>
                                    </div>

                                    <div class="flex-grow-1">
                                        <!-- Card Top Info -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary-subtle text-primary badge-custom">
                                                <i class="bi bi-people-fill me-1"></i> Target: <?php echo $notice['target_audience']; ?>
                                            </span>
                                            <small class="text-muted small"><?php echo date('Y', strtotime($notice['created_at'])); ?></small>
                                        </div>

                                        <!-- Title & Content -->
                                        <h4 class="fw-bold text-dark mb-2"><?php echo $notice['title']; ?></h4>
                                        <p class="text-secondary mb-4" style="font-size: 15px; line-height: 1.6;">
                                            <?php echo nl2br(substr($notice['description'], 0, 180)); ?>...
                                        </p>

                                        <!-- Card Bottom Actions -->
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center">
                                                <?php $p_pic = ($notice['profile_pic'] != 'default.png') ? "../" . $notice['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($notice['full_name']); ?>
                                                <img src="<?php echo $p_pic; ?>" class="publisher-img-sm me-2 shadow-sm">
                                                <small class="text-dark fw-bold"><?php echo $notice['full_name']; ?></small>
                                            </div>
                                            
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="view_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">Read Full</a>
                                                
                                                <?php if(($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin') && $notice['user_id'] == $_SESSION['user_id']): ?>
                                                    <div class="btn-group">
                                                        <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light rounded-circle border mx-1 text-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <a href="delete_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-light rounded-circle border text-danger" title="Delete" onclick="return confirm('Delete this notice?')"><i class="bi bi-trash"></i></a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <i class="bi bi-megaphone display-1 text-muted opacity-25"></i>
                        <h4 class="mt-3 text-muted fw-bold">No announcements yet.</h4>
                        <p class="text-muted small">Check back later for official campus updates.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>