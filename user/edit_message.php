<?php
include '../config.php';
// config.php তে সেশন স্টার্ট করা আছে, তাও সেফটির জন্য চেক করা ভালো

if(isset($_POST['msg_id']) && isset($_SESSION['user_id'])){
    $id = mysqli_real_escape_string($conn, $_POST['msg_id']);
    $u_id = $_SESSION['user_id'];
    $text = mysqli_real_escape_string($conn, $_POST['message']);

    // শুধুমাত্র নিজের মেসেজ এডিট করার পারমিশন
    $sql = "UPDATE private_messages SET message_text='$text', is_edited=1 
            WHERE id='$id' AND sender_id='$u_id'";
    
    if(mysqli_query($conn, $sql)){
        echo "success";
    } else {
        echo "error";
    }
}
?>