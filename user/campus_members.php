<?php
include '../config.php';
// config.php তে সেশন স্টার্ট করা আছে

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

// সার্চ এবং ফিল্টার প্যারামিটার ধরা
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

// কুয়েরি তৈরি: নিজেকে বাদ দিয়ে বাকি সব ইউজারকে আনা
$sql = "SELECT * FROM users WHERE id != '$current_user_id' AND is_verified = 1";

if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (full_name LIKE '%$safe_search%' OR university_id LIKE '%$safe_search%' OR dept LIKE '%$safe_search%')";
}

if($role_filter){
    $safe_role = mysqli_real_escape_string($conn, $role_filter);
    $sql .= " AND role = '$safe_role'";
}

$sql .= " ORDER BY full_name ASC";
$members = mysqli_query($conn, $sql);

// সাইডবারের জন্য নিজের তথ্য
$user_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'"));
$my_pic = ($user_res['profile_pic'] != 'default.png') ? "../" . $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Members | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; --bg-light: #f0f2f5; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; overflow-y: auto; z-index: 1000; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 12px; margin-bottom: 5px; transition: 0.2s; border: none; text-decoration: none;}
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.3rem; margin-right: 12px; }
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        
        /* Member Card Styles */
        .member-card { border-radius: 20px; border: none; transition: 0.3s; background: white; box-shadow: var(--card-shadow); text-align: center; padding: 25px; height: 100%; }
        .member-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .member-avatar { width: 85px; height: 85px; object-fit: cover; border-radius: 50%; border: 3px solid #f0f2f5; margin-bottom: 15px; }
        .role-badge { font-size: 10px; text-transform: uppercase; font-weight: 800; padding: 4px 12px; border-radius: 50px; }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">CampusConnect</a>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block shadow-sm">
        <div class="text-center mb-4">
            <a href="profile.php"><img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;"></a>
            <h6 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i> <span>Feed</span></a>
            <a href="campus_members.php" class="nav-link active"><i class="bi bi-person-lines-fill"></i> <span>Campus Members</span></a>
            <a href="messages.php" class="nav-link"><i class="bi bi-chat-square-text"></i> <span>Messages</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people"></i> <span>Network</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Campus Directory</h2>
                <p class="text-muted">Find and connect with students, teachers, and alumni.</p>
            </div>

            <!-- Search & Filter Bar -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-10">
                    <form method="GET" class="row g-2">
                        <div class="col-md-7">
                            <input type="text" name="search" class="form-control search-box" placeholder="Search by name, ID or department..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select search-box py-2">
                                <option value="">All Roles</option>
                                <option value="student" <?php if($role_filter == 'student') echo 'selected'; ?>>Students</option>
                                <option value="teacher" <?php if($role_filter == 'teacher') echo 'selected'; ?>>Teachers</option>
                                <option value="alumni" <?php if($role_filter == 'alumni') echo 'selected'; ?>>Alumni</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Members Grid -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if(mysqli_num_rows($members) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($members)): ?>
                        <div class="col">
                            <div class="card member-card">
                                <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                <img src="<?php echo $img; ?>" class="member-avatar shadow-sm mx-auto">
                                
                                <h6 class="fw-bold text-dark mb-1 text-truncate"><?php echo $row['full_name']; ?></h6>
                                <div class="mb-2">
                                    <span class="badge bg-primary-subtle text-primary role-badge">
                                        <?php echo $row['role']; ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-3"><i class="bi bi-building"></i> <?php echo $row['dept']; ?></p>
                                
                                <div class="d-grid">
                                    <a href="profile.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search display-1 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No members found matching your search.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>