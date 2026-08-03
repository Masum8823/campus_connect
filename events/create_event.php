<?php
include '../config.php';
// Session is already started in config.php

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')){
    header("Location: index.php"); exit();
}

if(isset($_POST['publish_event'])){
    $org_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cat = $_POST['category'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $seats = $_POST['seat_limit'];

    $db_banner_path = "uploads/events/default_event.png";

    // Handle Banner Image Upload
    if(!empty($_FILES['banner']['name'])){
        $img_name = time() . "_" . $_FILES['banner']['name'];
        $target = "../uploads/events/" . $img_name;
        if (!file_exists('../uploads/events')) { mkdir('../uploads/events', 0777, true); }
        if(move_uploaded_file($_FILES['banner']['tmp_name'], $target)){
            $db_banner_path = "uploads/events/" . $img_name;
        }
    }

    // Insert Event into Database
    $query = "INSERT INTO events (organizer_id, title, category, description, event_date, event_time, location, banner_image, seat_limit) 
              VALUES ('$org_id', '$title', '$cat', '$desc', '$date', '$time', '$loc', '$db_banner_path', '$seats')";
    
    if(mysqli_query($conn, $query)){
        // --- NEW: NOTIFICATION LOGIC START ---
        $event_id = mysqli_insert_id($conn); // Get the ID of the newly created event
        $notif_msg = "📢 New Event: " . $title . " has been published! Check it out.";
        $notif_link = "../events/view_event.php?id=" . $event_id;

        // Fetch all users except Admins to send them notifications
        $all_users = mysqli_query($conn, "SELECT id FROM users WHERE role != 'admin'");
        
        while($user_row = mysqli_fetch_assoc($all_users)){
            $target_uid = $user_row['id'];
            
            // Do not send notification to the person who created the event
            if($target_uid != $org_id) {
                mysqli_query($conn, "INSERT INTO notifications (user_id, type, message, link) 
                                     VALUES ('$target_uid', 'event', '$notif_msg', '$notif_link')");
            }
        }
        // --- NOTIFICATION LOGIC END ---

        echo "<script>alert('Event Published & Notifications Sent to all members!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 50px; }
        .form-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        .premium-input:focus { background: white; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card p-4 p-md-5 bg-white">
                    <div class="text-center mb-5">
                        <i class="bi bi-calendar-plus text-primary display-4"></i>
                        <h2 class="fw-bold mt-2">Organize a Campus Event</h2>
                        <p class="text-muted">Broadcast your event to the entire campus instantly</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Event Title</label>
                            <input type="text" name="title" class="form-control premium-input" placeholder="e.g., Annual Tech Fest 2026" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Category</label>
                                <select name="category" class="form-select premium-input">
                                    <option value="Seminar">Seminar</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Fest">Fest</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Reunion">Reunion</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Seat Limit (0 for Unlimited)</label>
                                <input type="number" name="seat_limit" class="form-control premium-input" value="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Event Date</label>
                                <input type="date" name="event_date" class="form-control premium-input" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase">Time</label>
                                <input type="time" name="event_time" class="form-control premium-input" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Venue / Location</label>
                            <input type="text" name="location" class="form-control premium-input" placeholder="e.g., Auditorium" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Full Description</label>
                            <textarea name="description" class="form-control premium-input" rows="5" placeholder="Mention schedule, guests..." required></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold small text-uppercase">Event Banner / Poster</label>
                            <input type="file" name="banner" class="form-control premium-input" accept="image/*">
                        </div>

                        <div class="d-grid">
                            <button name="publish_event" class="btn btn-primary btn-lg fw-bold rounded-pill shadow py-3">
                                <i class="bi bi-megaphone-fill me-2"></i> PUBLISH & NOTIFY ALL
                            </button>
                            <a href="index.php" class="btn btn-link text-muted mt-2 text-decoration-none small">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>