<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$event_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// বর্তমান ডাটা আনা এবং চেক করা যে সে মালিক কি না
$query = mysqli_query($conn, "SELECT * FROM events WHERE id='$event_id'");
$event = mysqli_fetch_assoc($query);

if(!$event || ($event['organizer_id'] != $current_user_id && $_SESSION['role'] != 'admin')){
    echo "Access Denied!"; exit();
}

if(isset($_POST['update_event'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cat = $_POST['category'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $seats = $_POST['seat_limit'];

    $image_sql = "";
    // নতুন ব্যানার আপলোড করলে
    if(!empty($_FILES['banner']['name'])){
        $img_name = time() . "_" . $_FILES['banner']['name'];
        $target = "../uploads/events/" . $img_name;
        if(move_uploaded_file($_FILES['banner']['tmp_name'], $target)){
            $db_path = "uploads/events/" . $img_name;
            $image_sql = ", banner_image='$db_path'";
        }
    }

    $update = "UPDATE events SET title='$title', category='$cat', description='$desc', event_date='$date', event_time='$time', location='$loc', seat_limit='$seats' $image_sql WHERE id='$event_id'";
    
    if(mysqli_query($conn, $update)){
        echo "<script>alert('Event Updated!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 50px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card edit-card p-4 p-md-5">
                    <h2 class="fw-bold text-primary mb-4">Edit Event Details</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3"><label class="fw-bold small">Title</label><input type="text" name="title" class="form-control" value="<?php echo $event['title']; ?>" required></div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small">Category</label>
                                <select name="category" class="form-select">
                                    <option value="Seminar" <?php if($event['category'] == 'Seminar') echo 'selected'; ?>>Seminar</option>
                                    <option value="Workshop" <?php if($event['category'] == 'Workshop') echo 'selected'; ?>>Workshop</option>
                                    <option value="Fest" <?php if($event['category'] == 'Fest') echo 'selected'; ?>>Fest</option>
                                    <option value="Sports" <?php if($event['category'] == 'Sports') echo 'selected'; ?>>Sports</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3"><label class="fw-bold small">Seat Limit</label><input type="number" name="seat_limit" class="form-control" value="<?php echo $event['seat_limit']; ?>"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="fw-bold small">Date</label><input type="date" name="event_date" class="form-control" value="<?php echo $event['event_date']; ?>" required></div>
                            <div class="col-md-6 mb-3"><label class="fw-bold small">Time</label><input type="time" name="event_time" class="form-control" value="<?php echo $event['event_time']; ?>" required></div>
                        </div>

                        <div class="mb-3"><label class="fw-bold small">Venue</label><input type="text" name="location" class="form-control" value="<?php echo $event['location']; ?>" required></div>
                        <div class="mb-3"><label class="fw-bold small">Description</label><textarea name="description" class="form-control" rows="4" required><?php echo $event['description']; ?></textarea></div>
                        
                        <div class="mb-4">
                            <label class="fw-bold small">Update Banner (Optional)</label>
                            <input type="file" name="banner" class="form-control">
                        </div>

                        <div class="d-grid gap-2">
                            <button name="update_event" class="btn btn-primary btn-lg fw-bold rounded-pill">SAVE CHANGES</button>
                            <a href="index.php" class="btn btn-link text-muted">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>