<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$search = $_GET['search'] ?? '';
$sql = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept FROM alumni_stories 
        JOIN users ON alumni_stories.user_id = users.id WHERE 1=1";

if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (alumni_stories.current_job_title LIKE '%$safe_search%' 
                OR alumni_stories.company_name LIKE '%$safe_search%' 
                OR users.full_name LIKE '%$safe_search%')";
}

$stories = mysqli_query($conn, $sql . " ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success Stories | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .journey-card { border-radius: 25px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 25px; background: white; transition: 0.3s; }
        .journey-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .alumni-img { width: 65px; height: 65px; object-fit: cover; border-radius: 50%; border: 3px solid #f8f9fa; }
        .action-icon { color: #adb5bd; transition: 0.2s; font-size: 18px; }
        .action-icon:hover { color: var(--primary-color); }
        .delete-icon:hover { color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">← Success Stories</a>
            <div class="ms-auto">
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <a href="share_journey.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 text-dark shadow-sm">Share My Journey</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search by name, job, or company..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary rounded-pill px-4 ms-2 fw-bold shadow-sm" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <?php if(mysqli_num_rows($stories) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($stories)): 
                        $sid = $row['id'];
                        $is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$sid' AND user_id='$current_user_id'")) > 0;
                    ?>
                        <div class="card journey-card">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <a href="../user/profile.php?id=<?php echo $row['user_id']; ?>" class="text-decoration-none d-flex align-items-center">
                                        <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                        <img src="<?php echo $img; ?>" class="alumni-img me-3 shadow-sm">
                                        <div>
                                            <h5 class="mb-0 fw-bold text-dark"><?php echo $row['full_name']; ?></h5>
                                            <p class="text-primary mb-0 small fw-bold"><?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?></p>
                                            <small class="text-muted">
                                                <?php echo $row['dept']; ?> Graduate 
                                                <!-- Edited Label -->
                                                <?php if($row['is_edited'] == 1): ?> • <span class="text-primary italic" style="font-size: 10px;">Edited</span><?php endif; ?>
                                            </small>
                                        </div>
                                    </a>

                                    <!-- Edit/Delete Actions (Only for owner or admin) -->
                                    <?php if($row['user_id'] == $current_user_id || $user_role == 'admin'): ?>
                                        <div class="d-flex gap-2">
                                            <?php if($row['user_id'] == $current_user_id): ?>
                                                <a href="edit_journey.php?id=<?php echo $sid; ?>" class="action-icon" title="Edit Story"><i class="bi bi-pencil-square"></i></a>
                                            <?php endif; ?>
                                            <a href="delete_journey.php?id=<?php echo $sid; ?>" class="action-icon delete-icon" title="Delete Story" onclick="return confirm('Delete this success story permanently?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 15px;">
                                    <?php echo nl2br(substr($row['journey_story'], 0, 300)); ?>...
                                </p>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="toggle_inspire.php?id=<?php echo $sid; ?>" class="btn btn-sm <?php echo $is_inspired ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-3 fw-bold">
                                        <i class="bi <?php echo $is_inspired ? 'bi-heart-fill' : 'bi-heart'; ?> me-1"></i> Inspired
                                    </a>
                                    <a href="view_journey.php?id=<?php echo $sid; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Read Full Roadmap →</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <i class="bi bi-journal-text display-1 text-muted opacity-25"></i>
                        <p class="text-muted mt-3">No stories found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>