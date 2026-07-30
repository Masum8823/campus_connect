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
        $edited_label = ($msg['is_edited'] == 1) ? ' <small style="font-size:9px; opacity:0.7;">(edited)</small>' : '';
        
        $time = date('h:i A', strtotime($msg['created_at']));
        
        echo '<div class="message-wrapper d-flex flex-column ' . ($is_my_msg ? 'align-items-end' : 'align-items-start') . '">';
        
        echo '  <div class="message ' . $class . '" data-id="' . $msg['id'] . '">';
        echo        htmlspecialchars($msg['message_text']) . $edited_label;
        
        if($is_my_msg){
            echo '  <div class="msg-actions">';
            echo '      <i class="bi bi-pencil-square me-1" onclick="editMessage(' . $msg['id'] . ', \'' . htmlspecialchars($msg['message_text']) . '\')"></i>';
            echo '      <i class="bi bi-trash" onclick="deleteMessage(' . $msg['id'] . ')"></i>';
            echo '  </div>';
        }
        echo '  </div>';
        
        echo '  <small class="msg-time text-muted">' . $time . '</small>';
        
        echo '</div>';
    }
}
?>