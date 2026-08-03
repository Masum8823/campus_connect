<?php
include '../config.php';


if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

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
        echo '  <div class="d-flex align-items-center w-100 ' . ($is_my_msg ? 'justify-content-end' : 'justify-content-start') . '">';
        
        // যদি আমার মেসেজ হয়, তবে বাম পাশে থ্রি-ডট দেখাবে
        if($is_my_msg) {
            echo '<div class="dropdown msg-options-dropdown">';
            echo '  <i class="bi bi-three-dots-vertical text-muted px-2" data-bs-toggle="dropdown" style="cursor:pointer; font-size: 14px;"></i>';
            echo '  <ul class="dropdown-menu shadow-sm border-0 small">';
            echo '      <li><a class="dropdown-item py-1" href="javascript:void(0)" onclick="setupReply(' . $msg['id'] . ', \'' . htmlspecialchars(substr($msg['message_text'], 0, 25)) . '\')"><i class="bi bi-reply me-2"></i>Reply</a></li>';
            echo '      <li><a class="dropdown-item py-1" href="javascript:void(0)" onclick="editMessage(' . $msg['id'] . ', \'' . htmlspecialchars($msg['message_text']) . '\')"><i class="bi bi-pencil me-2"></i>Edit</a></li>';
            echo '      <li><a class="dropdown-item py-1 text-danger" href="javascript:void(0)" onclick="deleteMessage(' . $msg['id'] . ')"><i class="bi bi-trash me-2"></i>Delete</a></li>';
            echo '  </ul>';
            echo '</div>';
        }

        echo '    <div class="message ' . $class . ' shadow-sm">';
        
        // রিপ্লাই প্রিভিউ
        if($msg['reply_to']){
            echo '<div class="reply-preview-in-chat mb-1">';
            if($msg['replied_type'] == 'image') echo '<i class="bi bi-image"></i> Photo';
            else echo htmlspecialchars(substr($msg['replied_text'] ?? '', 0, 40)) . '...';
            echo '</div>';
        }

        // মিডিয়া রেন্ডারিং
        if($msg['message_type'] == 'image'){
            echo '<a href="../' . $msg['file_path'] . '" target="_blank"><img src="../' . $msg['file_path'] . '" class="chat-img mb-2"></a>';
        } elseif($msg['message_type'] == 'file'){
            echo '<a href="../' . $msg['file_path'] . '" class="file-attachment mb-2 text-decoration-none" download><i class="bi bi-file-earmark-arrow-down fs-4"></i> <span class="small">' . basename($msg['file_path']) . '</span></a>';
        }

        if(!empty($msg['message_text'])){
            echo '<div>' . htmlspecialchars($msg['message_text']) . $edited_label . '</div>';
        }

        echo '    </div>';

        // যদি অন্যের মেসেজ হয়, তবে ডান পাশে রিপ্লাই আইকন দেখাবে
        if(!$is_my_msg) {
            echo '<i class="bi bi-reply text-muted px-2 reply-btn-hover" style="cursor:pointer;" onclick="setupReply(' . $msg['id'] . ', \'' . htmlspecialchars(substr($msg['message_text'], 0, 25)) . '\')"></i>';
        }

        echo '  </div>';
        
        // সিন স্ট্যাটাস এবং সময়
        echo '  <div class="d-flex align-items-center mt-1">';
        echo '      <small class="msg-time text-muted me-2" style="font-size:10px;">' . $time . '</small>';
        if($is_my_msg && $msg['is_read'] == 1){
            echo '  <small class="text-primary fw-bold" style="font-size:9px;"><i class="bi bi-check2-all"></i> Seen</small>';
        }
        echo '  </div>';
        
        echo '</div>';
    }
}
?>