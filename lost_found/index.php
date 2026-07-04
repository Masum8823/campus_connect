<?php
include '../config.php';
session_start();

// Redirect to login if user is not authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all lost and found items with owner details
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .item-card { transition: transform 0.2s; border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .item-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .resolved-overlay { background: rgba(25, 135, 84, 0.9); color: white; font-weight: bold; position: absolute; width: 100%; top: 0; z-index: 2; }
        .item-img-container { position: relative; height: 200px; overflow: hidden; }
        .item-img-container img { height: 100%; width: 100%; object-fit: cover; }
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
                <a href="../user/dashboard.php" class="btn btn-outline-light btn-sm fw-bold me-2"><i class="bi bi-house-door"></i> Dashboard</a>
                <a href="post_item.php" class="btn btn-light btn-sm fw-bold text-primary shadow-sm"><i class="bi bi-plus-circle"></i> Post New Item</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Lost & Found Feed</h3>
            <span class="badge bg-secondary rounded-pill"><?php echo mysqli_num_rows($items); ?> Total Items</span>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($items) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($items)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 item-card">
                            
                            <!-- Resolved Status Banner -->
                            <?php if($row['is_resolved'] == 1): ?>
                                <div class="resolved-overlay text-center py-1 small">
                                    <i class="bi bi-check-circle-fill"></i> THIS CASE IS RESOLVED
                                </div>
                            <?php endif; ?>

                            <!-- Clickable Image Container -->
                            <div class="item-img-container">
                                <a href="view_item.php?id=<?php echo $row['id']; ?>">
                                    <?php if($row['item_image'] != 'no_image.png' && !empty($row['item_image'])): ?>
                                        <img src="../<?php echo $row['item_image']; ?>" alt="Item Image" style="<?php echo ($row['is_resolved'] == 1) ? 'filter: grayscale(100%); opacity: 0.6;' : ''; ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="bi bi-image text-muted display-1"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?> text-uppercase">
                                        <i class="bi <?php echo $row['item_status'] == 'lost' ? 'bi-exclamation-triangle' : 'bi-check-circle'; ?>"></i>
                                        <?php echo $row['item_status']; ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                </div>

                                <h5 class="card-title fw-bold <?php echo ($row['is_resolved'] == 1) ? 'text-muted text-decoration-line-through' : 'text-dark'; ?>">
                                    <?php echo $row['item_name']; ?>
                                </h5>
                                
                                <!-- Short Description -->
                                <p class="card-text text-secondary small flex-grow-1">
                                    <?php echo nl2br(substr($row['description'], 0, 80)); ?>...
                                </p>
                                
                                <!-- NEW: View Details Button -->
                                <a href="view_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary w-100 fw-bold mb-3 shadow-sm">
                                    View Full Details
                                </a>
                                
                                <hr class="text-muted my-2">
                                
                                <div class="small mb-3">
                                    <p class="mb-1 text-dark"><i class="bi bi-person text-primary"></i> <strong>By:</strong> <?php echo $row['full_name']; ?></p>
                                    <p class="mb-0 text-dark"><i class="bi bi-telephone text-success"></i> <strong>Contact:</strong> <?php echo $row['contact_info']; ?></p>
                                </div>

                                <!-- Action Buttons: Only for Owner -->
                                <?php if($row['user_id'] == $_SESSION['user_id']): ?>
                                    <div class="d-flex gap-2 border-top pt-2">
                                        <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary flex-grow-1" style="font-size: 11px;">
                                            <i class="bi bi-pencil-square"></i> Edit / Status
                                        </a>
                                        <a href="delete_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')" style="font-size: 11px;">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No items found.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>