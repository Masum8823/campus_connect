<?php
include '../config.php';
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: ../auth/login.php"); exit(); }

$directory = mysqli_query($conn, "SELECT users.id, full_name, dept, profile_pic FROM users WHERE role='alumni' ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Directory | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .dir-card { border-radius: 20px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; text-align: center; padding: 20px; transition: 0.3s; }
        .dir-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">← Alumni Directory</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h4 class="fw-bold mb-4">Our Alumni Network</h4>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <?php while($dir = mysqli_fetch_assoc($directory)): ?>
                <div class="col">
                    <div class="card dir-card h-100">
                        <?php $img = ($dir['profile_pic'] != 'default.png') ? "../" . $dir['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($dir['full_name'])."&background=random"; ?>
                        <img src="<?php echo $img; ?>" class="rounded-circle mx-auto mb-2 shadow-sm" width="80" height="80" style="object-fit: cover;">
                        <h6 class="fw-bold text-dark mb-1"><?php echo $dir['full_name']; ?></h6>
                        <small class="text-muted d-block mb-3"><?php echo $dir['dept']; ?> Graduate</small>
                        <a href="../user/profile.php?id=<?php echo $dir['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Profile</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>