<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$today = date('Y-m-d');

// ১. আপকামিং ইভেন্ট কুয়েরি (নতুনগুলো আগে)
$upcoming_q = "SELECT events.*, users.full_name FROM events 
               JOIN users ON events.organizer_id = users.id 
               WHERE event_date >= '$today' 
               ORDER BY event_date ASC";
$upcoming_events = mysqli_query($conn, $upcoming_q);

// ২. গত হয়ে যাওয়া ইভেন্ট কুয়েরি (লিমিট ৬টি)
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

        /* Navigation */
        .navbar { background: #0d6efd !important; }

        /* Event Card Styles */
        .event-card { border-radius: 25px; border: none; overflow: hidden; transition: all 0.3s ease; background: white; box-shadow: var(--card-shadow); height: 100%; display: flex; flex-direction: column; }
        .event-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        
        .banner-container { position: relative; height: 200px; overflow: hidden; background: #eee; }
        .banner-img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .event-card:hover .banner-img { transform: scale(1.08); }

        .date-badge { position: absolute; top: 15px; left: 15px; background: white; border-radius: 15px; padding: 8px 15px; text-align: center; font-weight: 800; color: #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.1); line-height: 1.2; z-index: 2; }
        .date-badge span { display: block; font-size: 11px; text-transform: uppercase; color: #666; }

        .cat-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 5px 12px; border-radius: 50px; background: #e7f1ff; color: #0d6efd; }

        /* Past Events Style */
        .past-event { opacity: 0.8; }
        .past-event .banner-img { filter: grayscale(100%); }
        .section-divider { border-top: 2px dashed #ddd; margin: 50px 0; position: relative; }
        .section-divider span { position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--bg-light); padding: 0 20px; color: #999; font-weight: 700; text-transform: uppercase; font-size: 12px; }

        .btn-view { border-radius: 50px; font-weight: 700; font-size: 13px; padding: 8px 20px; }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">
                <i class="bi bi-calendar-event-fill me-2"></i> Events Hub
            </a>
            <div class="ms-auto d-flex align-items-center">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4 me-2">Dashboard</a>
                <?php if($user_role == 'teacher' || $user_role == 'admin'): ?>
                    <a href="create_event.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 text-dark shadow-sm">+ Create Event</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <!-- Header -->
        <div class="text-center mb-5 mt-4">
            <h2 class="fw-extrabold text-dark">University Events</h2>
            <p class="text-muted">Stay connected with the latest campus activities and workshops.</p>
        </div>

        <!-- --- UPCOMING EVENTS SECTION --- -->
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-stars text-warning"></i> Upcoming Events</h4>
            <span class="badge bg-primary rounded-pill px-3"><?php echo mysqli_num_rows($upcoming_events); ?> Scheduled</span>
        </div>

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
                                <img src="../<?php echo $row['banner_image']; ?>" class="banner-img" alt="Event Banner">
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-2">
                                    <span class="cat-badge"><?php echo $row['category']; ?></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-3"><?php echo $row['title']; ?></h5>
                                
                                <div class="mb-4">
                                    <p class="text-muted small mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $row['location']; ?></p>
                                    <p class="text-muted small mb-0"><i class="bi bi-clock-fill text-primary"></i> <?php echo date('h:i A', strtotime($row['event_time'])); ?></p>
                                </div>

                                <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                    <small class="text-muted">By <strong><?php echo explode(' ', $row['full_name'])[0]; ?></strong></small>
                                    <a href="view_event.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-view shadow-sm">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="bi bi-calendar-x display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">No upcoming events at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- --- PAST EVENTS SECTION --- -->
        <?php if(mysqli_num_rows($past_events) > 0): ?>
            <div class="section-divider">
                <span>History</span>
            </div>

            <h4 class="fw-bold text-secondary mb-4 px-2">Past Events</h4>
            <div class="row">
                <?php while($row = mysqli_fetch_assoc($past_events)): ?>
                    <div class="col-lg-4 col-md-6 mb-4 past-event">
                        <div class="card event-card grayscale shadow-sm">
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