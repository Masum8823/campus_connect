<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'"));
$my_pic = ($user_info['profile_pic'] != 'default.png') ? "../" . $user_info['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";


$query = "SELECT c.id as conv_id, u.id as other_user_id, u.full_name, u.profile_pic, u.dept,
          (SELECT message_text FROM private_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_msg,
          (SELECT created_at FROM private_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_time
          FROM conversations c
          JOIN users u ON (u.id = c.user1_id OR u.id = c.user2_id)
          WHERE (c.user1_id = '$current_user_id' OR c.user2_id = '$current_user_id')
          AND u.id != '$current_user_id'
          ORDER BY last_time DESC";

$conversations = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; --bg-light: #f0f2f5; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 12px; margin-bottom: 5px; transition: 0.2s; border: none; text-decoration: none;}
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        
        /* Message Item Styles */
        .chat-item { border-radius: 15px; border: none; transition: 0.3s; background: white; margin-bottom: 10px; text-decoration: none; display: block; color: inherit; }
        .chat-item:hover { background-color: #fff; transform: translateX(5px); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .user-avatar { width: 55px; height: 55px; object-fit: cover; border-radius: 50%; border: 2px solid #f0f2f5; }
        .last-msg-preview { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; color: #666; font-size: 13px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">CampusConnect</a>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block">
        <div class="text-center mb-4">
            <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;">
            <h6 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h6>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i> <span>Feed</span></a>
            <a href="messages.php" class="nav-link active"><i class="bi bi-chat-square-text-fill"></i> <span>Messages</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people"></i> <span>Network</span></a>
        </nav>
    </div>

    <!-- Content -->
    <div class="main-content">
        <div class="container" style="max-width: 700px;">
            <h3 class="fw-bold mb-4">Messages</h3>
            
            <?php if(mysqli_num_rows($conversations) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($conversations)): ?>
                    <a href="chat.php?user_id=<?php echo $row['other_user_id']; ?>" class="card chat-item shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                            <img src="<?php echo $img; ?>" class="user-avatar me-3">
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h6>
                                    <small class="text-muted" style="font-size: 11px;">
                                        <?php echo $row['last_time'] ? date('M d', strtotime($row['last_time'])) : ''; ?>
                                    </small>
                                </div>
                                <p class="last-msg-preview mb-0 mt-1">
                                    <?php echo $row['last_msg'] ? htmlspecialchars($row['last_msg']) : 'Start a conversation...'; ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                    <i class="bi bi-chat-dots display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">Your inbox is empty.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>