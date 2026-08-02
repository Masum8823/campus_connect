<?php
include '../config.php';
// config.php-তে অলরেডি সেশন চেক এবং স্টার্ট করা আছে

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// তথ্য আপডেট করার লজিক
if(isset($_POST['update_profile'])){
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $batch = mysqli_real_escape_string($conn, $_POST['batch']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin_url']);
    $is_private = mysqli_real_escape_string($conn, $_POST['is_private']); // প্রাইভেসী ভ্যালু (0 বা 1)

    // ফাইল আপলোড লজিক (যদি ছবি চেঞ্জ করে)
    $update_img_sql = "";
    if(!empty($_FILES['profile_pic']['name'])){
        $file_name = time() . "_" . $_FILES['profile_pic']['name'];
        $target = "../uploads/" . $file_name;
        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)){
            $img_path = "uploads/" . $file_name;
            $update_img_sql = ", profile_pic='$img_path'";
        }
    }

    // পূর্ণাঙ্গ আপডেট কুয়েরি (is_private সহ)
    $sql = "UPDATE users SET 
            full_name='$name', 
            bio='$bio', 
            phone='$phone', 
            batch='$batch', 
            skills='$skills', 
            linkedin_url='$linkedin', 
            is_private='$is_private' 
            $update_img_sql 
            WHERE id='$user_id'";
    
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_name'] = $name; // সেশন নাম আপডেট করা
        echo "<script>alert('Profile Updated Successfully!'); window.location='profile.php';</script>";
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}

// বর্তমান ডাটা আনা ফর্মে দেখানোর জন্য
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: white; overflow: hidden; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; font-size: 14px; }
        .premium-input:focus { background: white; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <div class="card edit-card p-4 p-md-5">
                    <h3 class="fw-bold text-primary mb-4"><i class="bi bi-person-gear me-2"></i>Edit My Profile</h3>
                    <hr class="mb-4 opacity-50">
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control premium-input" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control premium-input" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="e.g. 017XXXXXXXX">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio (Short Description)</label>
                            <textarea name="bio" class="form-control premium-input" rows="2" placeholder="Tell us something about yourself..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch / Semester</label>
                                <input type="text" name="batch" class="form-control premium-input" value="<?php echo htmlspecialchars($user['batch']); ?>" placeholder="e.g. Fall 2023">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Update Profile Picture</label>
                                <input type="file" name="profile_pic" class="form-control premium-input" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Skills (comma separated)</label>
                            <input type="text" name="skills" class="form-control premium-input" value="<?php echo htmlspecialchars($user['skills']); ?>" placeholder="PHP, Java, Graphics, Photography">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">LinkedIn Profile URL</label>
                            <input type="url" name="linkedin_url" class="form-control premium-input" value="<?php echo htmlspecialchars($user['linkedin_url']); ?>" placeholder="https://linkedin.com/in/username">
                        </div>

                        <!-- Privacy Settings Section -->
                        <div class="mb-5 p-3 rounded-4 border bg-light">
                            <label class="form-label text-primary"><i class="bi bi-shield-lock-fill me-1"></i> Profile Visibility</label>
                            <select name="is_private" class="form-select premium-input">
                                <option value="0" <?php echo ($user['is_private'] == 0) ? 'selected' : ''; ?>>🌍 Public (Everyone can see info & posts)</option>
                                <option value="1" <?php echo ($user['is_private'] == 1) ? 'selected' : ''; ?>>🔒 Private (Only connected users can see)</option>
                            </select>
                            <div class="form-text mt-2">Setting your profile to private will hide your timeline and personal details from users you aren't connected with.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button name="update_profile" class="btn btn-primary rounded-pill px-5 fw-bold py-2 shadow">Save Changes</button>
                            <a href="profile.php" class="btn btn-light rounded-pill px-4 py-2 border">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>