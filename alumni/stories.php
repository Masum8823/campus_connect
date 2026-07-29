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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Stories | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-light: #f8f9fa;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding-top: 80px; 
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(13, 110, 253, 0.9) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Search Section */
        .search-container {
            background: white;
            border-radius: 50px;
            padding: 8px 8px 8px 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            max-width: 700px;
            margin: 0 auto 50px;
            border: 1px solid #eee;
        }
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 15px;
            font-weight: 500;
        }
        .btn-search {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Journey Card Premium Design */
        .journey-card {
            border-radius: 30px;
            border: none;
            background: white;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: var(--card-shadow);
            margin-bottom: 35px;
            position: relative;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .journey-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .alumni-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 22px;
            border: 4px solid #f8f9fa;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .badge-alumni {
            background: #e7f3ff;
            color: #0d6efd;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
        }

        .job-info {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .story-text {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            position: relative;
        }

        /* Action Icons */
        .action-btn {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f8f9fa;
            color: #666;
            transition: 0.2s;
            text-decoration: none;
        }
        .action-btn:hover { background: #eee; color: #000; }
        .delete-btn:hover { background: #fee2e2; color: #dc3545; }

        .btn-inspired {
            border-radius: 50px;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 25px;
            border: 2px solid #fee2e2;
            color: #dc3545;
            transition: 0.3s;
        }
        .btn-inspired.active {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        .btn-inspired:hover { transform: scale(1.05); }

        .btn-roadmap {
            background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }
        .btn-roadmap:hover {
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
            transform: scale(1.03);
            color: white;
        }

        .quote-icon {
            position: absolute;
            top: 30px;
            right: 40px;
            font-size: 50px;
            color: #f0f2f5;
            z-index: 0;
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="bi bi-arrow-left-short me-1"></i> Success Stories
            </a>
            <div class="ms-auto">
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <a href="share_journey.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Share My Journey
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Modern Search Bar -->
        <div class="search-container">
            <i class="bi bi-search text-muted me-2"></i>
            <form method="GET" action="" class="w-100 d-flex">
                <input type="text" name="search" class="search-input" placeholder="Search by Alumni name, company or role..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary btn-search shadow-sm" type="submit">SEARCH</button>
            </form>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
                <?php if(mysqli_num_rows($stories) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($stories)): 
                        $sid = $row['id'];
                        $count_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alumni_inspired WHERE story_id='$sid'"));
                        $is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$sid' AND user_id='$current_user_id'")) > 0;
                    ?>
                        <div class="card journey-card">
                            <i class="bi bi-quote quote-icon"></i>
                            <div class="card-body p-4 p-md-5 position-relative">
                                
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <!-- Alumni Profile -->
                                    <a href="../user/profile.php?id=<?php echo $row['user_id']; ?>" class="text-decoration-none d-flex align-items-center">
                                        <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                        <img src="<?php echo $img; ?>" class="alumni-img me-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="mb-0 fw-bold text-dark"><?php echo $row['full_name']; ?></h5>
                                                <span class="badge-alumni">ALUMNI</span>
                                            </div>
                                            <div class="job-info">
                                                <i class="bi bi-briefcase"></i> <?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?>
                                            </div>
                                            <small class="text-muted"><?php echo $row['dept']; ?> Graduate <?php if($row['is_edited']): ?> • <span class="text-primary italic">Edited</span><?php endif; ?></small>
                                        </div>
                                    </a>

                                    <!-- Edit/Delete -->
                                    <?php if($row['user_id'] == $current_user_id || $user_role == 'admin'): ?>
                                        <div class="d-flex gap-2">
                                            <?php if($row['user_id'] == $current_user_id): ?>
                                                <a href="edit_journey.php?id=<?php echo $sid; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <?php endif; ?>
                                            <a href="delete_journey.php?id=<?php echo $sid; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Permanently delete this story?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Story Summary -->
                                <p class="story-text mb-4">
                                    <?php echo nl2br(substr($row['journey_story'], 0, 320)); ?>...
                                </p>

                                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                    <!-- Inspired Button -->
                                    <a href="toggle_inspire.php?id=<?php echo $sid; ?>" class="btn btn-inspired <?php echo $is_inspired ? 'active' : ''; ?>">
                                        <i class="bi <?php echo $is_inspired ? 'bi-heart-fill' : 'bi-heart'; ?> me-2"></i>Inspired (<?php echo $count_res['total']; ?>)
                                    </a>
                                    
                                    <!-- Details Button -->
                                    <a href="view_journey.php?id=<?php echo $sid; ?>" class="btn btn-roadmap">
                                        Full Roadmap <i class="bi bi-arrow-right-short ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-5 shadow-sm">
                        <i class="bi bi-journal-text display-1 text-muted opacity-25"></i>
                        <h4 class="mt-3 text-muted fw-bold">No stories found.</h4>
                        <p class="text-muted">Be the first alumni to share your roadmap!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>