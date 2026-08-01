<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$search = $_GET['search'] ?? '';

$sql = "SELECT id, full_name, dept, profile_pic FROM users WHERE role='alumni'";

if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (full_name LIKE '%$safe_search%' OR dept LIKE '%$safe_search%')";
}

$sql .= " ORDER BY full_name ASC";
$directory = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Directory | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        
        .dir-card { 
            border-radius: 20px; 
            border: none; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            background: white; 
            text-align: center; 
            padding: 25px; 
            transition: 0.3s; 
            height: 100%;
        }
        .dir-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        .search-box { 
            border-radius: 50px; 
            background: white; 
            border: 1px solid #eee; 
            padding: 12px 25px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }

        .alumni-img { 
            width: 90px; 
            height: 90px; 
            object-fit: cover; 
            border-radius: 50%; 
            border: 4px solid #f8f9fa; 
            margin-bottom: 15px;
            transition: 0.3s;
        }
        .dir-card:hover .alumni-img { border-color: var(--primary-color); }
        
        .profile-link { text-decoration: none; color: inherit; }
        .profile-link:hover .alumni-name { color: var(--primary-color); text-decoration: underline; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="bi bi-people-fill me-2"></i> Alumni Directory
            </a>
            <a href="index.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4">← Back to Hub</a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Search Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-7 text-center">
                <h3 class="fw-bold mb-3">Find Our Alumni</h3>
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search by name or department..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm fw-bold" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Directory Grid -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 pb-5">
            <?php if(mysqli_num_rows($directory) > 0): ?>
                <?php while($dir = mysqli_fetch_assoc($directory)): ?>
                    <div class="col">
                        <div class="card dir-card">
                            <!-- Clickable Profile Link (Image & Name) -->
                            <a href="../user/profile.php?id=<?php echo $dir['id']; ?>" class="profile-link">
                                <?php $img = ($dir['profile_pic'] != 'default.png') ? "../" . $dir['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($dir['full_name'])."&background=random"; ?>
                                <img src="<?php echo $img; ?>" class="alumni-img shadow-sm">
                                <h6 class="fw-bold text-dark mb-1 alumni-name"><?php echo $dir['full_name']; ?></h6>
                            </a>
                            <small class="text-muted d-block mb-3"><?php echo $dir['dept']; ?> Graduate</small>
                            <a href="../user/profile.php?id=<?php echo $dir['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">View Profile</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">No alumni found with that name or department.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>