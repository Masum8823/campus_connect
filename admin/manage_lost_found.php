<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_GET['delete_item'])){
    $id = $_GET['delete_item'];
    
    $img_query = mysqli_query($conn, "SELECT item_image FROM lost_found WHERE id='$id'");
    $img_data = mysqli_fetch_assoc($img_query);
    if($img_data['item_image'] && $img_data['item_image'] != 'uploads/items/no_image.png' && file_exists("../".$img_data['item_image'])){
        unlink("../".$img_data['item_image']);
    }

    mysqli_query($conn, "DELETE FROM lost_found WHERE id='$id'");
    header("Location: manage_lost_found.php?msg=item_deleted");
}

$all_items = mysqli_query($conn, "SELECT lost_found.*, users.full_name FROM lost_found JOIN users ON lost_found.user_id = users.id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Lost & Found | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        .item-table-card { background: white; border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .item-img-sm { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link text-white"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_content.php" class="nav-link text-white"><i class="bi bi-file-post me-2"></i> Feed Moderation</a>
            <a href="manage_lost_found.php" class="nav-link active bg-primary text-white shadow-sm"><i class="bi bi-search me-2"></i> Lost & Found</a>
            <hr>
            <a href="../user/dashboard.php" class="nav-link text-white"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 class="fw-bold mb-4">Lost & Found Moderation</h3>

        <div class="card item-table-card p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Item Details</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Posted By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($all_items)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../<?php echo $row['item_image']; ?>" class="item-img-sm me-3 border">
                                    <div>
                                        <div class="fw-bold"><?php echo $row['item_name']; ?></div>
                                        <small class="text-muted"><?php echo $row['category']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $row['item_status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo strtoupper($row['item_status']); ?>
                                </span>
                                <?php if($row['is_resolved']): ?>
                                    <span class="badge bg-dark">RESOLVED</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo $row['location']; ?></small></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td>
                                <a href="?delete_item=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this report?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>