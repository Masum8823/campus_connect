<?php
include '../config.php';
session_start();

if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

    // জয়েন কুয়েরি দিয়ে রিপ্লাই মেসেজের তথ্যসহ সব মেসেজ আনা
    $query = mysqli_query($conn, "SELECT m.*, r.message_text as replied_text, r.message_type as replied_type 
                                   FROM private_messages m 
                                   LEFT JOIN private_messages r ON m.reply_to = r.id 
                                   WHERE m.conversation_id='$conv_id' 
                                   ORDER BY m.created_at ASC");

    while($msg = mysqli_fetch_assoc($query)){
        $is_my_msg = ($msg['sender_id'] == $my_id);
        $class = $is_my_msg ? 'sent' : 'received';
        $time = date('h:i A', strtotime($msg['created_at']));

        echo '<div class="message-wrapper d-flex flex-column ' . ($is_my_msg ? 'align-items-end' : 'align-items-start') . '">';
        echo '  <div class="message ' . $class . ' shadow-sm">';
        
        // --- রিপ্লাই প্রিভিউ (যদি থাকে) ---
        if($msg['reply_to']){
            echo '<div class="reply-preview-in-chat mb-1">';
            if($msg['replied_type'] == 'image'){
                echo '<i class="bi bi-image"></i> Photo';
            } else {
                echo htmlspecialchars(substr($msg['replied_text'], 0, 50)) . (strlen($msg['replied_text']) > 50 ? '...' : '');
            }
            echo '</div>';
        }

        // বাকি মেসেজ রেন্ডারিং (Image/File/Text) আগের মতোই
        if($msg['message_type'] == 'image'){
            echo '<a href="../' . $msg['file_path'] . '" target="_blank"><img src="../' . $msg['file_path'] . '" class="chat-img mb-2"></a>';
        } elseif($msg['message_type'] == 'file'){
            echo '<a href="../' . $msg['file_path'] . '" class="file-attachment mb-2 text-decoration-none" download><i class="bi bi-file-earmark-arrow-down fs-4"></i> <span class="small">' . basename($msg['file_path']) . '</span></a>';
        }

        if(!empty($msg['message_text'])){
            echo '<div>' . htmlspecialchars($msg['message_text']) . '</div>';
        }

        // এডিট/ডিলিট এবং রিপ্লাই বাটন (আইকন)
        echo '  <div class="msg-actions">';
        echo '      <i class="bi bi-reply-fill me-2" onclick="setupReply(' . $msg['id'] . ', \'' . htmlspecialchars(substr($msg['message_text'], 0, 30)) . '\')"></i>';
        if($is_my_msg){
            echo '      <i class="bi bi-pencil-square me-2" onclick="editMessage(' . $msg['id'] . ', \'' . htmlspecialchars($msg['message_text']) . '\')"></i>';
            echo '      <i class="bi bi-trash" onclick="deleteMessage(' . $msg['id'] . ')"></i>';
        }
        echo '  </div>';
        
        echo '  </div>';
        echo '  <small class="msg-time text-muted">' . $time . '</small>';
        echo '</div>';
    }
}
?>