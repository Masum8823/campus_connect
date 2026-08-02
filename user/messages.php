<?php
include '../config.php';
// সেশন অলরেডি config.php তে স্টার্ট করা আছে

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

// ইউজারের নিজের তথ্য আনা (সাইডবারের জন্য)
$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'"));
$my_pic = ($user_info['profile_pic'] != 'default.png') ? "../" . $user_info['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

// কুয়েরি: কনভারসেশন লিস্ট আনা
$query = "SELECT c.id as conv_id, u.id as other_user_id, u.full_name, u.profile_pic, u.dept, u.last_activity,
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; --bg-light: #f0f2f5; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; overflow-y: auto; z-index: 1000; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 12px; margin-bottom: 5px; transition: 0.2s; border: none; text-decoration: none;}
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        
        /* Chat Card Styles */
        .chat-item-wrapper { position: relative; margin-bottom: 12px; }
        .chat-item { border-radius: 20px; border: none; transition: 0.3s; background: white; display: block; color: inherit; box-shadow: 0 2px 10px rgba(0,0,0,0.03); text-decoration: none; padding-right: 60px !important; }
        .chat-item:hover { transform: translateX(5px); box-shadow: 0 5px 20px rgba(0,0,0,0.08); background: #fff; }
        
        .user-avatar { width: 55px; height: 55px; object-fit: cover; border-radius: 18px; border: 2px solid #f8f9fa; }
        .online-badge { position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; background-color: #198754; border: 2px solid white; border-radius: 50%; }
        .last-msg-preview { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80%; color: #6c757d; font-size: 13.5px; }

        /* Delete Button - Fixed Positioning */
        .delete-conv-btn { 
            position: absolute; 
            right: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            z-index: 10; 
            color: #adb5bd; 
            padding: 8px; 
            border-radius: 50%; 
            transition: 0.2s;
            background: transparent;
            border: none;
        }
        .delete-conv-btn:hover { color: #dc3545; background: #fff1f0; }

        @media (max-width: 992px) { .sidebar { width: 85px; } .sidebar span, .sidebar h6, .sidebar p, .sidebar hr { display: none; } .main-content { margin-left: 85px; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php"><i class="bi bi-connectdevelop"></i> CampusConnect</a>
            <div class="ms-auto">
                <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-2 border-white" width="35" height="35">
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block shadow-sm">
        <div class="text-center mb-4">
            <a href="profile.php"><img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;"></a>
            <h6 class="fw-bold mb-0 text-dark"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i> <span>Feed</span></a>
            <a href="messages.php" class="nav-link active"><i class="bi bi-chat-square-text-fill"></i> <span>Messages</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people"></i> <span>Network</span></a>
            <a href="../alumni/index.php" class="nav-link"><i class="bi bi-award"></i> <span>Alumni Hub</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container" style="max-width: 750px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark mb-0">Messages</h3>
                <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 small">
                    <?php echo mysqli_num_rows($conversations); ?> active chats
                </span>
            </div>
            
            <?php if(mysqli_num_rows($conversations) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($conversations)): 
                    $is_online = (time() - strtotime($row['last_activity'])) < 120;
                ?>
                    <div class="chat-item-wrapper">
                        <!-- Main Chat Card -->
                        <a href="chat.php?user_id=<?php echo $row['other_user_id']; ?>" class="card chat-item shadow-sm p-3">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                    <img src="<?php echo $img; ?>" class="user-avatar shadow-sm">
                                    <?php if($is_online): ?>
                                        <span class="online-badge" title="Online"></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-dark"><?php echo $row['full_name']; ?></h6>
                                        <small class="text-muted" style="font-size: 11px;">
                                            <?php echo $row['last_time'] ? getTimeAgo($row['last_time']) : ''; ?>
                                        </small>
                                    </div>
                                    <p class="last-msg-preview mb-0 mt-1">
                                        <?php echo $row['last_msg'] ? htmlspecialchars($row['last_msg']) : '<span class="text-primary">Start a conversation...</span>'; ?>
                                    </p>
                                </div>
                            </div>
                        </a>

                        <!-- Delete Button - Independent Link -->
                        <a href="delete_conversation.php?conv_id=<?php echo $row['conv_id']; ?>" 
                           class="delete-conv-btn" 
                           onclick="return confirm('Are you sure you want to delete this entire chat history?')"
                           title="Delete Chat">
                            <i class="bi bi-trash3-fill fs-5"></i>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                    <i class="bi bi-chat-dots display-1 text-muted opacity-25"></i>
                    <h4 class="mt-3 text-muted fw-bold">Your inbox is empty</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>