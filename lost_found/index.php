<?php
include '../config.php';
session_start();

// Redirect to login if user is not authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all lost and found items with the name of the person who posted
$query = "SELECT lost_found.*, users.full_name FROM lost_found 
          JOIN users ON lost_found.user_id = users.id 
          ORDER BY created_at DESC";
$items = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found - CampusConnect</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { z-index: 1000; }
        .item-card { transition: transform 0.2s; border: none; border-radius: 15px; }
        .item-card:hover { transform: translateY(-5px); }
        .card-img-top { border-top-left-radius: 15px; border-top-right-radius: 15px; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Dashboard Home Button -->
                <a href="../user/dashboard.php" class="btn btn-outline-light btn-sm fw-bold me-2">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                
                <!-- Post Item Button -->
                <a href="post_item.php" class="btn btn-light btn-sm fw-bold text-primary shadow-sm">
                    <i class="bi bi-plus-circle"></i> Post New Item
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Lost & Found Feed</h3>
            <span class="badge bg-secondary rounded-pill"><?php echo mysqli_num_rows($items); ?> Items Posted</span>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($items) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($items)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 item-card">
                            <!-- Show image if it exists, otherwise show a placeholder icon -->
                            <?php if($row['item_image'] != 'no_image.png' && !empty($row['item_image'])): ?>
                                <img src="../<?php echo $row['item_image']; ?>" class="card-img-top" style="height: 220px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 220px; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                    <i class="bi bi-image text-muted display-1"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?> text-uppercase">
                                        <i class="bi <?php echo $row['item_status'] == 'lost' ? 'bi-exclamation-triangle' : 'bi-check-circle'; ?>"></i>
                                        <?php echo $row['item_status']; ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 11px;">
                                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                    </small>
                                </div>

                                <h5 class="card-title fw-bold text-dark"><?php echo $row['item_name']; ?></h5>
                                <p class="card-text text-secondary small" style="min-height: 40px;">
                                    <?php echo nl2br($row['description']); ?>
                                </p>
                                
                                <hr class="text-muted">
                                
                                <div class="small">
                                    <p class="mb-1 text-dark"><i class="bi bi-person-circle text-primary"></i> <strong>Posted by:</strong> <?php echo $row['full_name']; ?></p>
                                    <p class="mb-0 text-dark"><i class="bi bi-telephone-outbound text-success"></i> <strong>Contact:</strong> <?php echo $row['contact_info']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State if no items found -->
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No items posted yet.</h4>
                    <p class="text-muted">If you lost or found something, feel free to post it!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>