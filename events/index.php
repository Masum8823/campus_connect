<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

// সব আপকামিং ইভেন্ট আনা
$query = "SELECT events.*, users.full_name FROM events 
          JOIN users ON events.organizer_id = users.id 
          ORDER BY event_date ASC";
$events = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .event-card { border-radius: 25px; border: none; overflow: hidden; transition: 0.3s; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .event-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .banner-img { height: 200px; object-fit: cover; width: 100%; }
        .date-badge { position: absolute; top: 15px; left: 15px; background: white; border-radius: 12px; padding: 5px 15px; text-align: center; font-weight: 800; color: #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">CampusConnect Events</a>
            <div class="d-flex">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3 me-2">Dashboard</a>
                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                    <a href="create_event.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">+ Create Event</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Discover Campus Events</h2>
            <p class="text-muted">Stay updated with seminars, fests, and workshops.</p>
        </div>

        <div class="row">
            <?php while($row = mysqli_fetch_assoc($events)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100">
                        <div class="position-relative">
                            <img src="../<?php echo $row['banner_image']; ?>" class="banner-img">
                            <div class="date-badge">
                                <?php echo date('d M', strtotime($row['event_date'])); ?>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <span class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3"><?php echo $row['category']; ?></span>
                            <h5 class="fw-bold text-dark mb-1"><?php echo $row['title']; ?></h5>
                            <p class="text-muted small mb-3"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $row['location']; ?></p>
                            
                            <p class="small text-secondary mb-4"><?php echo substr($row['description'], 0, 100); ?>...</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <small class="text-muted">By: <strong><?php echo $row['full_name']; ?></strong></small>
                                <a href="view_event.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>