<?php
include '../config.php';
session_start();

if(isset($_POST['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_POST['conv_id'];
    $sender_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // ১. রিপ্লাই আইডি ধরা (যদি থাকে)
    $reply_to = (isset($_POST['reply_to']) && !empty($_POST['reply_to'])) ? $_POST['reply_to'] : 'NULL';
    
    $file_path = NULL;
    $msg_type = 'text';

    // ২. ফাইল আপলোড হ্যান্ডলিং
    if(isset($_FILES['chat_file']) && $_FILES['chat_file']['error'] == 0){
        $filename = time() . "_" . $_FILES['chat_file']['name'];
        $target = "../uploads/chat/" . $filename;
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(move_uploaded_file($_FILES['chat_file']['tmp_name'], $target)){
            $file_path = "uploads/chat/" . $filename;
            
            // ফাইল টাইপ নির্ধারণ (ছবি হলে 'image', অন্য ফাইল হলে 'file')
            $image_exts = ['jpg', 'jpeg', 'png', 'gif'];
            $msg_type = in_array($file_ext, $image_exts) ? 'image' : 'file';
        }
    }

    // ৩. ডাটাবেসে ইনসার্ট (reply_to কলামসহ)
    if(!empty($message) || $file_path){
        $query = "INSERT INTO private_messages (conversation_id, sender_id, message_text, message_type, file_path, reply_to) 
                  VALUES ('$conv_id', '$sender_id', '$message', '$msg_type', " . ($file_path ? "'$file_path'" : "NULL") . ", $reply_to)";
        
        if(mysqli_query($conn, $query)){
            echo "Message sent";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>