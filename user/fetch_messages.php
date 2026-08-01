<?php
include '../config.php';

if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

    // ১. মেইন লজিক: এই কনভারসেশনে আমার কাছে আসা সব মেসেজ "Read" হিসেবে মার্ক করা
    mysqli_query($conn, "UPDATE private_messages SET is_read = 1 
                         WHERE conversation_id = '$conv_id' 
                         AND sender_id != '$my_id' 
                         AND is_read = 0");

    // ২. সব মেসেজ তুলে আনা (রিপ্লাই তথ্যসহ)
    $query = mysqli_query($conn, "SELECT m.*, r.message_text as replied_text, r.message_type as replied_type 
                                   FROM private_messages m 
                                   LEFT JOIN private_messages r ON m.reply_to = r.id 
                                   WHERE m.conversation_id='$conv_id' 
                                   ORDER BY m.created_at ASC");

    while($msg = mysqli_fetch_assoc($query)){
        $is_my_msg = ($msg['sender_id'] == $my_id);
        $class = $is_my_msg ? 'sent' : 'received';
        $time = date('h:i A', strtotime($msg['created_at']));
        $edited_label = ($msg['is_edited'] == 1) ? ' <small style="font-size:9px; opacity:0.6;">(edited)</small>' : '';

        echo '<div class="message-wrapper d-flex flex-column ' . ($is_my_msg ? 'align-items-end' : 'align-items-start') . '">';
        echo '  <div class="message ' . $class . ' shadow-sm">';
        
        // --- রিপ্লাই প্রিভিউ ---
        if($msg['reply_to']){
            echo '<div class="reply-preview-in-chat mb-1">';
            if($msg['replied_type'] == 'image') echo '<i class="bi bi-image"></i> Photo';
            else echo htmlspecialchars(substr($msg['replied_text'], 0, 40)) . '...';
            echo '</div>';
        }

        // --- ইমেজ/ফাইল/টেক্সট রেন্ডারিং ---
        if($msg['message_type'] == 'image'){
            echo '<a href="../' . $msg['file_path'] . '" target="_blank"><img src="../' . $msg['file_path'] . '" class="chat-img mb-2"></a>';
        } elseif($msg['message_type'] == 'file'){
            echo '<a href="../' . $msg['file_path'] . '" class="file-attachment mb-2 text-decoration-none" download><i class="bi bi-file-earmark-arrow-down fs-4"></i> <span class="small">' . basename($msg['file_path']) . '</span></a>';
        }

        if(!empty($msg['message_text'])){
            echo '<div>' . htmlspecialchars($msg['message_text']) . $edited_label . '</div>';
        }

        // এডিট/ডিলিট/রিপ্লাই বাটন
        echo '  <div class="msg-actions">';
        echo '      <i class="bi bi-reply-fill me-2" onclick="setupReply(' . $msg['id'] . ', \'' . htmlspecialchars(substr($msg['message_text'], 0, 25)) . '\')"></i>';
        if($is_my_msg){
            echo '      <i class="bi bi-pencil-square me-2" onclick="editMessage(' . $msg['id'] . ', \'' . htmlspecialchars($msg['message_text']) . '\')"></i>';
            echo '      <i class="bi bi-trash" onclick="deleteMessage(' . $msg['id'] . ')"></i>';
        }
        echo '  </div>';
        
        echo '  </div>';
        
        // --- ৩. "Seen" স্ট্যাটাস এবং সময় দেখানো ---
        echo '  <div class="d-flex align-items-center mt-1">';
        echo '      <small class="msg-time text-muted me-2" style="font-size:10px;">' . $time . '</small>';
        // যদি মেসেজটি আমার পাঠানো হয় এবং সেটি পড়া হয়
        if($is_my_msg && $msg['is_read'] == 1){
            echo '  <small class="text-primary fw-bold" style="font-size:9px;"><i class="bi bi-check2-all"></i> Seen</small>';
        }
        echo '  </div>';
        
        echo '</div>';
    }
}
?>