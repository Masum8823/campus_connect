<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT connections.id as conn_id, users.* FROM connections 
          JOIN users ON connections.sender_id = users.id 
          WHERE connections.receiver_id = '$user_id' AND connections.status = 'pending'";
$requests = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connection Requests - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; }
        .req-card { border-radius: 15px; border: none; }
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
        <h4 class="fw-bold mb-4">Pending Connection Requests</h4>
        <div class="row">
            <?php if(mysqli_num_rows($requests) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($requests)): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card req-card shadow-sm p-3">
                            <div class="d-flex align-items-center mb-3">
                                <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                <img src="<?php echo $img; ?>" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h6>
                                    <small class="text-muted"><?php echo $row['dept']; ?></small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="accept_request.php?id=<?php echo $row['conn_id']; ?>" class="btn btn-primary btn-sm flex-grow-1 fw-bold">Accept</a>
                                <a href="toggle_connect.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm flex-grow-1">Ignore</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted"></i>
                    <p class="text-muted mt-3">No pending requests at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>