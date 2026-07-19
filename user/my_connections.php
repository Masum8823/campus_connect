<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$query = "SELECT users.* FROM connections 
          JOIN users ON (connections.sender_id = users.id OR connections.receiver_id = users.id)
          WHERE (connections.sender_id = '$current_user_id' OR connections.receiver_id = '$current_user_id') 
          AND connections.status = 'accepted' 
          AND users.id != '$current_user_id'";

$network = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Network - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; }
        .network-card { border-radius: 15px; border: none; transition: 0.3s; }
        .network-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">CampusConnect</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Feed</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">My Campus Network</h4>
            <span class="text-muted"><?php echo mysqli_num_rows($network); ?> Connections</span>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($network) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($network)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card network-card shadow-sm p-3">
                            <div class="d-flex align-items-center">
                                <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                <img src="<?php echo $img; ?>" class="rounded-circle me-3 border" width="65" height="65" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h6>
                                    <small class="text-muted d-block"><?php echo strtoupper($row['role']); ?> | <?php echo $row['dept']; ?></small>
                                    <a href="profile.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary mt-2 py-0" style="font-size: 11px;">View Profile</a>
                                </div>
                                <div class="dropdown">
                                    <i class="bi bi-three-dots-vertical" role="button" data-bs-toggle="dropdown"></i>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li><a class="dropdown-item text-danger small" href="toggle_connect.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Remove connection?')">Remove Connection</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <p class="text-muted mt-3">You haven't connected with anyone yet.</p>
                    <a href="dashboard.php" class="btn btn-primary btn-sm">Browse Campus Feed</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>