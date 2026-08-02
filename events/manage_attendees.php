<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$event_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// ইভেন্ট চেক এবং অর্গানাইজার ভেরিফিকেশন
$event_q = mysqli_query($conn, "SELECT title, organizer_id FROM events WHERE id='$event_id'");
$event = mysqli_fetch_assoc($event_q);

if(!$event || ($event['organizer_id'] != $current_user_id && $_SESSION['role'] != 'admin')){
    echo "Access Denied!"; exit();
}

// অংশগ্রহণকারীদের ডাটা তুলে আনা
$query = "SELECT ep.*, u.full_name, u.university_id, u.dept, u.role 
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
    <title>Manage Attendees | <?php echo $event['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .table-card { border-radius: 20px; border: none; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        .badge-going { background: #198754; color: white; }
        .badge-interested { background: #0dcaf0; color: #000; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="view_event.php?id=<?php echo $event_id; ?>">← Back to Event</a>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <h5 class="text-muted mb-1">Attendee List for:</h5>
            <h3 class="fw-bold text-dark"><?php echo $event['title']; ?></h3>
        </div>

        <div class="card table-card bg-white p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>ID / Dept</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($attendees) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($attendees)): ?>
                                <tr>
                                    <td><strong><?php echo $row['full_name']; ?></strong></td>
                                    <td>
                                        <small class="d-block text-dark"><?php echo $row['university_id']; ?></small>
                                        <small class="text-muted"><?php echo $row['dept']; ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?php echo strtoupper($row['role']); ?></span></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $row['status'] == 'going' ? 'badge-going' : 'badge-interested'; ?>">
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><small class="text-muted"><?php echo date('M d, Y', strtotime($row['registered_at'])); ?></small></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-4 d-block mb-3"></i>
                                    No one has responded to this event yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-end mt-3">
                <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold">
                    <i class="bi bi-printer"></i> Print Attendee List
                </button>
            </div>
        </div>
    </div>
</body>
</html>