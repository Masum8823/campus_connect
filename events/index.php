<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// বর্তমান তারিখ (Today's Date)
$today = date('Y-m-d');

// ১. আপকামিং ইভেন্ট কুয়েরি: যেসব ইভেন্ট আজ অথবা ভবিষ্যতে হবে
$upcoming_q = "SELECT events.*, users.full_name FROM events 
               JOIN users ON events.organizer_id = users.id 
               WHERE event_date >= '$today' 
               ORDER BY event_date ASC";
$upcoming_events = mysqli_query($conn, $upcoming_q);

// ২. গত হয়ে যাওয়া ইভেন্ট কুয়েরি: যেসব ইভেন্ট গতকাল বা তার আগে শেষ হয়েছে
$past_q = "SELECT events.*, users.full_name FROM events 
           JOIN users ON events.organizer_id = users.id 
           WHERE event_date < '$today' 
           ORDER BY event_date DESC LIMIT 6";
$past_events = mysqli_query($conn, $past_q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }

        .event-card { border-radius: 25px; border: none; overflow: hidden; transition: 0.3s; background: white; box-shadow: var(--card-shadow); height: 100%; display: flex; flex-direction: column; }
        .event-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        
        .banner-container { position: relative; height: 200px; overflow: hidden; background: #eee; }
        .banner-img { width: 100%; height: 100%; object-fit: cover; }

        .date-badge { position: absolute; top: 15px; left: 15px; background: white; border-radius: 12px; padding: 8px 15px; text-align: center; font-weight: 800; color: #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.1); line-height: 1.2; z-index: 2; }
        .date-badge span { display: block; font-size: 11px; text-transform: uppercase; color: #666; }

        /* Past Events Style */
        .past-event-card { opacity: 0.75; }
        .past-event-card .banner-img { filter: grayscale(100%); }
        .section-divider { border-top: 2px dashed #ddd; margin: 60px 0 40px; position: relative; }
        .section-divider span { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--bg-light); padding: 0 20px; color: #999; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">CampusConnect Events</a>
            <div class="ms-auto">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4 me-2">Dashboard</a>
                <?php if($user_role == 'teacher' || $user_role == 'admin'): ?>
                    <a href="create_event.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 text-dark">+ Create Event</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="text-center mb-5 mt-4">
            <h2 class="fw-extrabold text-dark">Campus Events Hub</h2>
            <p class="text-muted">Stay updated with the latest university activities.</p>
        </div>

        <!-- --- ১. আপকামিং ইভেন্ট সেকশন --- -->
        <h4 class="fw-bold text-dark mb-4 px-2"><i class="bi bi-calendar-check text-primary me-2"></i> Upcoming Events</h4>
        <div class="row">
            <?php if(mysqli_num_rows($upcoming_events) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($upcoming_events)): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card event-card">
                            <div class="banner-container">
                                <div class="date-badge">
                                    <?php echo date('d', strtotime($row['event_date'])); ?>
                                    <span><?php echo date('M', strtotime($row['event_date'])); ?></span>
                                </div>
                                <img src="../<?php echo $row['banner_image']; ?>" class="banner-img">
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3 align-self-start" style="font-size: 10px;"><?php echo $row['category']; ?></span>
                                <h5 class="fw-bold text-dark mb-3"><?php echo $row['title']; ?></h5>
                                <p class="text-muted small mb-4"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $row['location']; ?></p>
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                    <small class="text-muted">By <strong><?php echo explode(' ', $row['full_name'])[0]; ?></strong></small>
                                    <a href="view_event.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4"><p class="text-muted">No upcoming events found.</p></div>
            <?php endif; ?>
        </div>

        <!-- --- ২. গত হয়ে যাওয়া ইভেন্ট সেকশন --- -->
        <?php if(mysqli_num_rows($past_events) > 0): ?>
            <div class="section-divider">
                <span>Completed Events</span>
            </div>

            <div class="row">
                <?php while($row = mysqli_fetch_assoc($past_events)): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card event-card past-event-card shadow-sm">
                            <div class="banner-container">
                                <img src="../<?php echo $row['banner_image']; ?>" class="banner-img">
                            </div>
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-1"><?php echo $row['title']; ?></h6>
                                <small class="text-muted d-block mb-3">Held on <?php echo date('M d, Y', strtotime($row['event_date'])); ?></small>
                                <a href="view_event.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm w-100 rounded-pill fw-bold">View Summary</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>