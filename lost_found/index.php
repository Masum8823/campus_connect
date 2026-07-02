<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all lost and found items
$query = "SELECT lost_found.*, users.full_name FROM lost_found 
          JOIN users ON lost_found.user_id = users.id 
          ORDER BY created_at DESC";
$items = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lost & Found - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect</a>
            <a href="post_item.php" class="btn btn-light btn-sm fw-bold">+ Post New Item</a>
        </div>
    </nav>

    <div class="container">
        <h3 class="mb-4">Lost & Found Feed</h3>
        <div class="row">
            <?php while($row = mysqli_fetch_assoc($items)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <?php if($row['item_image'] != 'no_image.png'): ?>
                            <img src="../<?php echo $row['item_image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?> mb-2">
                                <?php echo strtoupper($row['item_status']); ?>
                            </span>
                            <h5 class="card-title"><?php echo $row['item_name']; ?></h5>
                            <p class="card-text small text-muted"><?php echo $row['description']; ?></p>
                            <hr>
                            <p class="small mb-1"><strong>Posted by:</strong> <?php echo $row['full_name']; ?></p>
                            <p class="small mb-1"><strong>Contact:</strong> <?php echo $row['contact_info']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>