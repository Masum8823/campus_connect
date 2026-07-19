<?php
include '../config.php';
session_start();

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

    $sql = "UPDATE users SET full_name='$name', bio='$bio', phone='$phone', batch='$batch', skills='$skills', linkedin_url='$linkedin' $update_img_sql WHERE id='$user_id'";
    
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_name'] = $name; // সেশন নাম আপডেট করা
        echo "<script>alert('Profile Updated!'); window.location='profile.php';</script>";
    }
}

// বর্তমান ডাটা আনা
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container card p-4 shadow-sm mx-auto" style="max-width: 700px; border-radius: 15px;">
        <h4 class="fw-bold text-primary mb-4">Edit My Profile</h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Bio (Short Description)</label>
                <textarea name="bio" class="form-control" rows="2"><?php echo $user['bio']; ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Batch / Semester</label>
                    <input type="text" name="batch" class="form-control" value="<?php echo $user['batch']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Skills (comma separated)</label>
                <input type="text" name="skills" class="form-control" value="<?php echo $user['skills']; ?>" placeholder="PHP, Java, Photography">
            </div>
            <div class="mb-4">
                <label class="small fw-bold">LinkedIn URL</label>
                <input type="url" name="linkedin_url" class="form-control" value="<?php echo $user['linkedin_url']; ?>">
            </div>
            <button name="update_profile" class="btn btn-primary px-5">Save Changes</button>
            <a href="profile.php" class="btn btn-link">Cancel</a>
        </form>
    </div>
</body>
</html>