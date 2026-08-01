<?php
include '../config.php';
session_start();

if(isset($_POST['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_POST['conv_id'];
    $sender_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $file_path = NULL;
    $msg_type = 'text';

    // ১. ফাইল আপলোড হ্যান্ডলিং
    if(isset($_FILES['chat_file']) && $_FILES['chat_file']['error'] == 0){
        $filename = time() . "_" . $_FILES['chat_file']['name'];
        $target = "../uploads/chat/" . $filename;
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(move_uploaded_file($_FILES['chat_file']['tmp_name'], $target)){
            $file_path = "uploads/chat/" . $filename;
            // টাইপ নির্ধারণ
            $image_exts = ['jpg', 'jpeg', 'png', 'gif'];
            $msg_type = in_array($file_ext, $image_exts) ? 'image' : 'file';
        }
    }

    // ২. ডাটাবেসে ইনসার্ট
    if(!empty($message) || $file_path){
        $query = "INSERT INTO private_messages (conversation_id, sender_id, message_text, message_type, file_path) 
                  VALUES ('$conv_id', '$sender_id', '$message', '$msg_type', " . ($file_path ? "'$file_path'" : "NULL") . ")";
        mysqli_query($conn, $query);
    }
}
?>