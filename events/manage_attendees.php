<?php
include '../config.php';
// config.php-তে সেশন অলরেডি স্টার্ট করা আছে

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$event_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// ১. আগে চেক করি এই ইভেন্টটি কি লগইন করা ইউজারের (Teacher/Admin)?
$event_q = mysqli_query($conn, "SELECT title, organizer_id FROM events WHERE id='$event_id'");
$event = mysqli_fetch_assoc($event_q);

if(!$event || ($event['organizer_id'] != $current_user_id && $_SESSION['role'] != 'admin')){
    echo "<div style='text-align:center; margin-top:50px;'><h2>Access Denied!</h2><p>You don't have permission to view this list.</p></div>";
    exit();
}

// ২. অংশগ্রহণকারীদের ডেটা তুলে আনা
$query = "SELECT ep.*, u.full_name, u.university_id, u.dept, u.role, u.profile_pic 
          FROM event_participations ep 
          JOIN users u ON ep.user_id = u.id 
          WHERE ep.event_id = '$event_id' 
          ORDER BY ep.status ASC, u.full_name ASC";
$attendees = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendees | <?php echo $event['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --card-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 90px; }
        .table-card { border-radius: 25px; border: none; box-shadow: var(--card-shadow); background: white; overflow: hidden; }
        .user-img-sm { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; }
        .badge-going { background: #198754; color: white; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 700; }
        .badge-interested { background: #0dcaf0; color: #000; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 700; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="view_event.php?id=<?php echo $event_id; ?>">
                <i class="bi bi-arrow-left me-2"></i> Back to Event Details
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="mb-4">
                    <h6 class="text-primary fw-bold text-uppercase mb-1" style="font-size: 12px; letter-spacing: 1px;">Participation List</h6>
                    <h3 class="fw-extrabold text-dark"><?php echo $event['title']; ?></h3>
                </div>

                <div class="card table-card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr style="font-size: 13px; color: #666;">
                                    <th>Participant</th>
                                    <th>University ID</th>
                                    <th>Department</th>
                                    <th>Response</th>
                                    <th>Date Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($attendees) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($attendees)): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                                    <img src="<?php echo $img; ?>" class="user-img-sm me-3 border">
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 14px;"><?php echo $row['full_name']; ?></div>
                                                        <small class="text-muted text-uppercase" style="font-size: 10px;"><?php echo $row['role']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="text-muted small"><?php echo $row['university_id']; ?></span></td>
                                            <td><span class="text-muted small"><?php echo $row['dept']; ?></span></td>
                                            <td>
                                                <span class="badge <?php echo $row['status'] == 'going' ? 'badge-going' : 'badge-interested'; ?>">
                                                    <i class="bi <?php echo $row['status'] == 'going' ? 'bi-check-circle-fill' : 'bi-info-circle'; ?> me-1"></i>
                                                    <?php echo strtoupper($row['status']); ?>
                                                </span>
                                            </td>
                                            <td><small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($row['registered_at'])); ?></small></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people display-1 opacity-25"></i>
                                            <h5 class="mt-3">No responses yet.</h5>
                                            <p class="small">Share the event to get participants!</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Print Button -->
                    <?php if(mysqli_num_rows($attendees) > 0): ?>
                        <div class="text-end mt-4 pt-3 border-top">
                            <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold rounded-pill px-4">
                                <i class="bi bi-printer me-2"></i> Print List
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>