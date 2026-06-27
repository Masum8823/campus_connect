<?php
include 'config.php';
session_start();

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM notices WHERE id='$id'");
$notice = mysqli_fetch_assoc($query);

if(!$notice){
    echo "Notice not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notice Details - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">CampusConnect</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4 shadow border-0">
                    <h2 class="text-primary"><?php echo $notice['title']; ?></h2>
                    <small class="text-muted">Published on: <?php echo date('F d, Y', strtotime($notice['created_at'])); ?></small>
                    <hr>
                    <p style="font-size: 18px; line-height: 1.6; white-space: pre-line;">
                        <?php echo $notice['description']; ?>
                    </p>
                    <hr>
                    <a href="dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>