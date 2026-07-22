<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: view_notice_list.php");
    exit();
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT notices.*, users.full_name, users.profile_pic, users.role 
                             FROM notices 
                             JOIN users ON notices.user_id = users.id 
                             WHERE notices.id='$id'");
$notice = mysqli_fetch_assoc($query);

if(!$notice){
    echo "Notice not found!";
    exit();
}

$publisher_pic = ($notice['profile_pic'] != 'default.png') ? "../" . $notice['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($notice['full_name'])."&background=random";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice: <?php echo $notice['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 100px; }
        .notice-container { max-width: 850px; margin: 0 auto; }
        .notice-paper { 
            background: white; 
            border-radius: 25px; 
            border: none; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.05); 
            overflow: hidden;
        }
        .notice-header-accent { 
            background: linear-gradient(135deg, #0d6efd 0%, #4b0082 100%); 
            height: 10px; 
        }
        .publisher-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .publisher-img { width: 45px; height: 45px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .notice-title { font-weight: 800; color: #1a1a1b; line-height: 1.3; margin-bottom: 15px; font-size: 2rem; }
        .notice-content { 
            font-size: 17px; 
            line-height: 1.7; 
            color: #444; 
            white-space: pre-line;
            margin-bottom: 30px;
        }
        .notice-image {
            width: 100%;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .link-card {
            background: #e7f1ff;
            border: 1px solid #b6d4fe;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .meta-tag { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; }
    </style>
</head>
<body>

    <!-- Fixed Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect</a>
            <a href="view_notice_list.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> All Notices
            </a>
        </div>
    </nav>

    <div class="container notice-container pb-5">
        <div class="card notice-paper">
            <div class="notice-header-accent"></div>
            <div class="card-body p-4 p-md-5">
                
                <!-- Category and Date -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                        <i class="bi bi-megaphone-fill me-1"></i> Official Notice
                    </span>
                    <div class="text-end">
                        <span class="meta-tag d-block">Published Date</span>
                        <span class="fw-bold text-dark small"><?php echo date('F d, Y', strtotime($notice['created_at'])); ?></span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="notice-title"><?php echo $notice['title']; ?></h1>
                
                <!-- Publisher Info -->
                <div class="publisher-box border">
                    <img src="<?php echo $publisher_pic; ?>" class="rounded-circle publisher-img me-3">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><?php echo $notice['full_name']; ?></h6>
                        <small class="text-muted text-uppercase" style="font-size: 10px;">
                            <?php echo $notice['role']; ?> | Audience: <?php echo ucfirst($notice['target_audience']); ?>
                        </small>
                    </div>
                </div>

                <!-- Attachment Image (If exists) -->
                <?php if(!empty($notice['image_path'])): ?>
                    <div class="text-center">
                        <img src="../<?php echo $notice['image_path']; ?>" class="notice-image" alt="Notice Attachment">
                    </div>
                <?php endif; ?>

                <!-- Main Text Content -->
                <div class="notice-content">
                    <?php echo $notice['description']; ?>
                </div>

                <!-- External Link Section (If exists) -->
                <?php if(!empty($notice['external_link'])): ?>
                    <div class="link-card mb-4">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-link-45deg"></i> Important Link / Resource</h6>
                        <p class="small text-muted mb-3">Click the button below to access additional materials related to this notice.</p>
                        <a href="<?php echo $notice['external_link']; ?>" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Open Resource
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Action Footer -->
                <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                    <a href="view_notice_list.php" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                        <i class="bi bi-chevron-left"></i> Back to Notice Board
                    </a>
                    
                    <?php if(($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin') && $notice['user_id'] == $_SESSION['user_id']): ?>
                        <div class="d-flex gap-2">
                            <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
                                <i class="bi bi-pencil-square me-1"></i> Edit Notice
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <p class="text-center text-muted mt-4 small">
            &copy; 2026 CampusConnect Platform - Official Communication.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>