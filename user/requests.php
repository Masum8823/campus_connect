<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// ইউজারের নিজের তথ্য আনা (সাইডবারের জন্য)
$user_info_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'");
$user_res = mysqli_fetch_assoc($user_info_query);
$my_pic = ($user_res['profile_pic'] != 'default.png') ? "../" . $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

// যারা রিকোয়েস্ট পাঠিয়েছে তাদের তথ্য আনা
$query = "SELECT connections.id as conn_id, users.* FROM connections 
          JOIN users ON connections.sender_id = users.id 
          WHERE connections.receiver_id = '$current_user_id' AND connections.status = 'pending'";
$requests = mysqli_query($conn, $query);
$total_pending = mysqli_num_rows($requests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Requests | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; --bg-light: #f0f2f5; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }

        /* Sidebar Styling */
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; overflow-y: auto; z-index: 1000; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 12px; margin-bottom: 5px; transition: 0.2s; border: none; }
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); transform: translateX(5px); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.3rem; margin-right: 12px; }

        /* Main Content Styling */
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        .req-card { border-radius: 20px; border: none; background: white; transition: all 0.3s ease; box-shadow: var(--card-shadow); }
        .req-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        .user-avatar { width: 65px; height: 65px; object-fit: cover; border-radius: 18px; border: 2px solid #f8f9fa; }
        .btn-action { border-radius: 10px; font-weight: 700; font-size: 13px; padding: 10px; }
        
        .nav-profile-img { width: 35px; height: 35px; object-fit: cover; border: 2px solid white; cursor: pointer; }
        
        @media (max-width: 992px) {
            .sidebar { width: 85px; }
            .sidebar span, .sidebar h6, .sidebar p, .sidebar hr { display: none; }
            .main-content { margin-left: 85px; }
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle nav-profile-img" data-bs-toggle="dropdown">
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                        <div class="p-3 border-bottom"><h6 class="fw-bold mb-0 small"><?php echo $_SESSION['user_name']; ?></h6><small><?php echo $_SESSION['dept']; ?></small></div>
                        <li><a class="dropdown-item mt-2" href="profile.php">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <div class="sidebar d-none d-md-block">
        <div class="text-center mb-4">
            <a href="profile.php">
                <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;">
            </a>
            <h6 class="fw-bold mb-0 text-dark"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i> <span>Campus Feed</span></a>
            <a href="../notice/view_notice_list.php" class="nav-link"><i class="bi bi-megaphone text-warning"></i> <span>Notices</span></a>
            <a href="../lost_found/index.php" class="nav-link"><i class="bi bi-search text-info"></i> <span>Lost & Found</span></a>
            <a href="../academic/index.php" class="nav-link"><i class="bi bi-mortarboard text-success"></i> <span>Academic Hub</span></a>
            <a href="requests.php" class="nav-link active"><i class="bi bi-person-plus-fill text-danger"></i> <span>Requests</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people text-primary"></i> <span>Network</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h3 class="fw-bold text-dark mb-0">Connection Requests</h3>
                    <p class="text-muted small">Manage people who want to connect with you.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-clock-history me-1"></i> <?php echo $total_pending; ?> Pending Requests
                    </span>
                </div>
            </div>

            <div class="row">
                <?php if($total_pending > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($requests)): ?>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card req-card p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                    <img src="<?php echo $img; ?>" class="user-avatar shadow-sm me-3">
                                    
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-1 fw-bold text-dark text-truncate"><?php echo $row['full_name']; ?></h6>
                                        <small class="text-muted d-block text-truncate"><i class="bi bi-building"></i> <?php echo $row['dept']; ?></small>
                                        <small class="badge bg-light text-primary border-0 p-0" style="font-size: 10px;"><?php echo strtoupper($row['role']); ?></small>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-7">
                                        <a href="accept_request.php?id=<?php echo $row['conn_id']; ?>" class="btn btn-primary w-100 btn-action shadow-sm">
                                            ACCEPT
                                        </a>
                                    </div>
                                    <div class="col-5">
                                        <a href="toggle_connect.php?id=<?php echo $row['id']; ?>" class="btn btn-light border w-100 btn-action">
                                            IGNORE
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="profile.php?id=<?php echo $row['id']; ?>" class="text-decoration-none small fw-bold">View Profile →</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border">
                        <div class="mb-3">
                            <i class="bi bi-person-plus display-1 text-muted opacity-25"></i>
                        </div>
                        <h4 class="mt-3 text-muted fw-bold">All caught up!</h4>
                        <p class="text-muted small">No pending connection requests at the moment.</p>
                        <a href="dashboard.php" class="btn btn-primary rounded-pill px-4 fw-bold mt-2">Back to Feed</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>