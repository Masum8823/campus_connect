<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

$query = "SELECT message_requests.id as req_id, users.* FROM message_requests 
          JOIN users ON message_requests.sender_id = users.id 
          WHERE message_requests.receiver_id = '$current_user_id' AND message_requests.status = 'pending'";
$requests = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Requests | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .req-card { border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .user-img { width: 70px; height: 70px; object-fit: cover; border-radius: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">CampusConnect</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4">Back to Feed</a>
        </div>
    </nav>

    <div class="container">
        <h3 class="fw-bold mb-4">Message Requests</h3>
        <div class="row">
            <?php if(mysqli_num_rows($requests) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($requests)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card req-card p-4 text-center">
                            <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                            <img src="<?php echo $img; ?>" class="user-img mb-3 mx-auto shadow-sm">
                            <h5 class="fw-bold mb-1"><?php echo $row['full_name']; ?></h5>
                            <p class="text-muted small mb-4"><?php echo $row['dept']; ?> Department</p>
                            
                            <div class="d-grid gap-2">
                                <!-- Accept বাটন: এটি handle_msg_request.php তে পাঠাবে -->
                                <a href="handle_msg_request.php?action=accept&req_id=<?php echo $row['req_id']; ?>&sender_id=<?php echo $row['id']; ?>" class="btn btn-primary rounded-pill fw-bold">Accept Request</a>
                                <a href="handle_msg_request.php?action=decline&req_id=<?php echo $row['req_id']; ?>" class="btn btn-light rounded-pill small">Ignore</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-chat-dots display-1 text-muted opacity-25"></i>
                    <h4 class="mt-3 text-muted fw-bold">No message requests.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>