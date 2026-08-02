<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];



// ইভেন্ট তথ্য এবং অর্গানাইজারের নাম আনা
$query = mysqli_query($conn, "SELECT events.*, users.full_name, users.profile_pic, users.dept 
                             FROM events 
                             JOIN users ON events.organizer_id = users.id 
                             WHERE events.id = '$id'");
$event = mysqli_fetch_assoc($query);

if(!$event){ echo "Event not found!"; exit(); }

// বর্তমান ইউজার কি অলরেডি রেজিস্টার করেছে?
$check_rsvp = mysqli_query($conn, "SELECT status FROM event_participations WHERE event_id='$id' AND user_id='$current_user_id'");
$my_rsvp = mysqli_fetch_assoc($check_rsvp);

// মোট কতজন "Going" দিয়েছে তা বের করা
$count_going = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM event_participations WHERE event_id='$id' AND status='going'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $event['title']; ?> | Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .detail-card { border-radius: 30px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; background: white; }
        .event-banner { width: 100%; height: 400px; object-fit: cover; }
        .info-pill { background: #f0f7ff; border-radius: 15px; padding: 15px; border: 1px solid #e0e9ff; text-align: center; height: 100%; }
        .rsvp-section { background: #fff; border-top: 1px solid #eee; padding: 20px; position: sticky; bottom: 0; z-index: 100; }
        .organizer-box { background: #f8f9fa; border-radius: 15px; padding: 15px; display: flex; align-items: center; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i> Back to Events</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card detail-card mb-4">
                    <img src="../<?php echo $event['banner_image']; ?>" class="event-banner">
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo $event['category']; ?></span>
                            <span class="text-muted"><i class="bi bi-calendar-event me-1"></i> Posted on <?php echo date('M d, Y', strtotime($event['created_at'])); ?></span>
                        </div>

                        <h1 class="fw-extrabold text-dark mb-4"><?php echo $event['title']; ?></h1>

                        <!-- Event Meta Info Grid -->
                        <div class="row g-3 mb-5">
                            <div class="col-md-3">
                                <div class="info-pill">
                                    <i class="bi bi-calendar-check text-primary fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold">Date</h6>
                                    <small class="text-muted"><?php echo date('F d, Y', strtotime($event['event_date'])); ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-pill">
                                    <i class="bi bi-clock text-warning fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold">Time</h6>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($event['event_time'])); ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-pill">
                                    <i class="bi bi-geo-alt text-danger fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold">Location</h6>
                                    <small class="text-muted"><?php echo $event['location']; ?></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-pill">
                                    <i class="bi bi-people text-success fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold">Capacity</h6>
                                    <small class="text-muted"><?php echo ($event['seat_limit'] > 0) ? $count_going." / ".$event['seat_limit'] . " joined" : "Unlimited"; ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- শুধুমাত্র অর্গানাইজার বা এডমিন এই বাটনটি দেখবে -->
                        <?php if($current_user_id == $event['organizer_id'] || $_SESSION['role'] == 'admin'): ?>
                            <div class="mb-4">
                                <a href="manage_attendees.php?id=<?php echo $id; ?>" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-people-fill"></i> Manage Attendees
                                </a>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-bold mb-3 border-bottom pb-2">About this Event</h5>
                        <p class="text-secondary mb-5" style="font-size: 17px; line-height: 1.8; white-space: pre-line;">
                            <?php echo $event['description']; ?>
                        </p>

                        <!-- Organizer Info -->
                        <div class="organizer-box border">
                            <?php $p_pic = ($event['profile_pic'] != 'default.png') ? "../" . $event['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($event['full_name']); ?>
                            <img src="<?php echo $p_pic; ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                            <div>
                                <small class="text-muted d-block small fw-bold text-uppercase">Organized By</small>
                                <h6 class="mb-0 fw-bold"><?php echo $event['full_name']; ?></h6>
                                <small class="text-muted"><?php echo $event['dept']; ?> Department</small>
                            </div>
                            <a href="../user/profile.php?id=<?php echo $event['organizer_id']; ?>" class="ms-auto btn btn-sm btn-outline-primary rounded-pill">View Profile</a>
                        </div>
                    </div>

                    <!-- RSVP Footer -->
                    <div class="rsvp-section d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="bi bi-check-circle-fill text-success"></i> <strong><?php echo $count_going; ?> people</strong> are going
                        </div>
                        <div class="d-flex gap-2">
                            <?php if($my_rsvp && $my_rsvp['status'] == 'going'): ?>
                                <a href="toggle_rsvp.php?id=<?php echo $id; ?>&status=remove" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg"></i> You're Going
                                </a>
                            <?php else: ?>
                                <a href="toggle_rsvp.php?id=<?php echo $id; ?>&status=going" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-plus-lg"></i> I'm Going
                                </a>
                                <a href="toggle_rsvp.php?id=<?php echo $id; ?>&status=interested" class="btn <?php echo ($my_rsvp && $my_rsvp['status'] == 'interested') ? 'btn-info text-white' : 'btn-outline-secondary'; ?> rounded-pill px-3 fw-bold">
                                    Interested
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>