<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT lost_found.*, users.full_name, users.dept FROM lost_found JOIN users ON lost_found.user_id = users.id WHERE lost_found.id='$id'");
$item = mysqli_fetch_assoc($query);

if(!$item){
    echo "Item not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Details - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .full-img { max-height: 500px; width: 100%; object-fit: contain; background: #000; border-radius: 15px; }
        .details-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">← Back to Lost & Found</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="details-container">
                    <div class="row">
                        <!-- Left Side: Image -->
                        <div class="col-md-6 mb-4">
                            <?php if($item['item_image'] != 'no_image.png' && !empty($item['item_image'])): ?>
                                <img src="../<?php echo $item['item_image']; ?>" class="full-img" alt="Item Image">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px; border-radius: 15px;">
                                    <i class="bi bi-image text-muted display-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Side: Text Info -->
                        <div class="col-md-6">
                            <span class="badge <?php echo $item['item_status'] == 'lost' ? 'bg-danger' : 'bg-success'; ?> mb-2">
                                <?php echo strtoupper($item['item_status']); ?>
                            </span>
                            
                            <?php if($item['is_resolved'] == 1): ?>
                                <span class="badge bg-primary mb-2 ms-2">RESOLVED</span>
                            <?php endif; ?>

                            <h2 class="fw-bold mb-3"><?php echo $item['item_name']; ?></h2>
                            <p class="text-muted small mb-4"><i class="bi bi-clock"></i> Posted on: <?php echo date('F d, Y', strtotime($item['created_at'])); ?></p>
                            
                            <h6 class="fw-bold">Description:</h6>
                            <p class="text-secondary mb-4" style="font-size: 17px; line-height: 1.6;">
                                <?php echo nl2br($item['description']); ?>
                            </p>

                            <div class="p-3 bg-light rounded border mb-4">
                                <h6 class="fw-bold text-primary mb-3">Contact Information</h6>
                                <p class="mb-2"><strong><i class="bi bi-person"></i> Posted By:</strong> <?php echo $item['full_name']; ?> (<?php echo $item['dept']; ?>)</p>
                                <p class="mb-0"><strong><i class="bi bi-telephone-outbound"></i> Reach at:</strong> <?php echo $item['contact_info']; ?></p>
                            </div>

                            <a href="index.php" class="btn btn-outline-secondary w-100">Back to Feed</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>