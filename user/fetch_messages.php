<?php
include '../config.php';
session_start();

if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

    $query = mysqli_query($conn, "SELECT * FROM private_messages WHERE conversation_id='$conv_id' ORDER BY created_at ASC");

    while($msg = mysqli_fetch_assoc($query)){
        $is_my_msg = ($msg['sender_id'] == $my_id);
        $class = $is_my_msg ? 'sent' : 'received';
        $time = date('h:i A', strtotime($msg['created_at']));

        echo '<div class="message-wrapper d-flex flex-column ' . ($is_my_msg ? 'align-items-end' : 'align-items-start') . '">';
        echo '  <div class="message ' . $class . ' shadow-sm">';
        
        // ১. যদি ইমেজ হয়
        if($msg['message_type'] == 'image'){
            echo '<a href="../' . $msg['file_path'] . '" target="_blank"><img src="../' . $msg['file_path'] . '" class="chat-img mb-2"></a>';
        } 
        // ২. যদি অন্য ফাইল (PDF, ZIP) হয়
        elseif($msg['message_type'] == 'file'){
            echo '<a href="../' . $msg['file_path'] . '" class="file-attachment mb-2" download>';
            echo '  <i class="bi bi-file-earmark-arrow-down fs-4"></i>';
            echo '  <span class="small">' . basename($msg['file_path']) . '</span>';
            echo '</a>';
        }

        // ৩. টেক্সট মেসেজ (যদি থাকে)
        if(!empty($msg['message_text'])){
            echo '<div>' . htmlspecialchars($msg['message_text']) . '</div>';
        }

        echo '  </div>';
        echo '  <small class="msg-time text-muted">' . $time . '</small>';
        echo '</div>';
    }
}
?>