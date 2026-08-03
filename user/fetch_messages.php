<?php
include '../config.php';


if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

    // মেসেজ রিড মার্ক করা
    mysqli_query($conn, "UPDATE private_messages SET is_read = 1 WHERE conversation_id = '$conv_id' AND sender_id != '$my_id' AND is_read = 0");

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
        
        // --- আমার মেসেজের জন্য বাম পাশে থ্রি-ডট ---
        if($is_my_msg) {
            echo '<div class="custom-dropdown">';
            echo '  <i class="bi bi-three-dots-vertical drop-trigger px-2" onclick="toggleCustomMenu(event)"></i>';
            echo '  <div class="custom-menu shadow-sm">';
            echo '      <div onclick="setupReply(' . $msg['id'] . ', \'' . addslashes(htmlspecialchars(substr($msg['message_text'], 0, 20))) . '\')"><i class="bi bi-reply"></i> Reply</div>';
            echo ' <div onclick="editMessage(' . $msg['id'] . ', `' . htmlspecialchars($msg['message_text'], ENT_QUOTES) . '`)"><i class="bi bi-pencil"></i> Edit</div>';            echo '      <div class="text-danger" onclick="deleteMessage(' . $msg['id'] . ')"><i class="bi bi-trash"></i> Delete</div>';
            echo '  </div>';
            echo '</div>';
        }

        echo '    <div class="message ' . $class . ' shadow-sm">';
        if($msg['reply_to']){
            echo '<div class="reply-preview-in-chat mb-1">';
            echo ($msg['replied_type'] == 'image') ? 'Photo' : htmlspecialchars(substr($msg['replied_text'] ?? '', 0, 30)) . '...';
            echo '</div>';
        }

        if($msg['message_type'] == 'image') echo '<a href="../' . $msg['file_path'] . '" target="_blank"><img src="../' . $msg['file_path'] . '" class="chat-img mb-2"></a>';
        elseif($msg['message_type'] == 'file') echo '<a href="../' . $msg['file_path'] . '" class="file-attachment mb-2 text-decoration-none" download><i class="bi bi-file-earmark-arrow-down fs-4"></i> <span class="small">' . basename($msg['file_path']) . '</span></a>';

        if(!empty($msg['message_text'])) echo '<div>' . htmlspecialchars($msg['message_text']) . $edited_label . '</div>';
        echo '    </div>';

        // --- অন্যের মেসেজের জন্য ডান পাশে রিপ্লাই আইকন ---
        if(!$is_my_msg) {
            echo '<i class="bi bi-reply text-muted px-2 drop-trigger" onclick="setupReply(' . $msg['id'] . ', \'' . addslashes(htmlspecialchars(substr($msg['message_text'], 0, 20))) . '\')"></i>';
        }

        echo '  </div>';
        echo '  <div class="d-flex align-items-center mt-1">';
        echo '      <small class="msg-time me-2">' . $time . '</small>';
        if($is_my_msg && $msg['is_read'] == 1) echo ' <small class="text-primary fw-bold" style="font-size:9px;"><i class="bi bi-check2-all"></i> Seen</small>';
        echo '  </div>';
        echo '</div>';
    }
}
?>