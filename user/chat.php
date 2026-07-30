<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['user_id'])){
    header("Location: dashboard.php"); exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

$other_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$other_user_id'"));

$conv_query = mysqli_query($conn, "SELECT id FROM conversations WHERE (user1_id='$current_user_id' AND user2_id='$other_user_id') OR (user1_id='$other_user_id' AND user2_id='$current_user_id')");
$conv = mysqli_fetch_assoc($conv_query);
$conversation_id = $conv['id'];

$block_check = mysqli_query($conn, "SELECT * FROM message_blocks WHERE (blocker_id='$current_user_id' AND blocked_id='$other_user_id') OR (blocker_id='$other_user_id' AND blocked_id='$current_user_id')");
$is_chat_blocked = mysqli_num_rows($block_check) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?php echo $other_user['full_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .chat-container { max-width: 600px; margin: 20px auto; background: white; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; height: 85vh; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .chat-header { padding: 15px 20px; background: #0d6efd; color: white; display: flex; align-items: center; }
        .chat-box { flex-grow: 1; padding: 20px; overflow-y: auto; background: #f9f9f9; display: flex; flex-direction: column; }
        .message { max-width: 75%; padding: 10px 15px; border-radius: 18px; margin-bottom: 10px; font-size: 14px; position: relative; }
        .sent { align-self: flex-end; background: #0d6efd; color: white; border-bottom-right-radius: 2px; }
        .received { align-self: flex-start; background: #e4e6eb; color: #050505; border-bottom-left-radius: 2px; }
        .chat-footer { padding: 15px; background: white; border-top: 1px solid #eee; }
        .msg-input { border-radius: 25px; border: 1px solid #ddd; padding: 10px 20px; background: #f0f2f5; }
        .message { position: relative; }
        .msg-actions { 
            display: none; 
            position: absolute; 
            top: -20px; 
            right: 0; 
            background: rgba(0,0,0,0.6); 
            color: white; 
            padding: 2px 8px; 
            border-radius: 10px; 
            font-size: 12px; 
            cursor: pointer; 
        }
        .msg-time { font-size: 10px; margin-bottom: 10px; padding: 0 8px; opacity: 0.8; }
        .sent + .msg-time { text-align: right; }
        .message:hover .msg-actions { display: block; }
        .sent .msg-actions { right: 0; }
        .received .msg-actions { left: 0; }
    </style>
</head>
<body>

    <div class="container">
        <div class="chat-container">
            <!-- Header -->
            <div class="chat-header">
                <a href="messages.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <?php $img = ($other_user['profile_pic'] != 'default.png') ? "../" . $other_user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($other_user['full_name']); ?>
                <img src="<?php echo $img; ?>" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                <h6 class="mb-0 fw-bold"><?php echo $other_user['full_name']; ?></h6>
            </div>

            <!-- Messages Window -->
            <div class="chat-box" id="chatBox"></div>

            <!-- Footer Input -->
            <div class="chat-footer">
                <?php if($is_chat_blocked): ?>
                    <div class="alert alert-secondary mb-0 py-2 text-center small fw-bold">
                        <i class="bi bi-slash-circle me-1"></i> You cannot reply to this conversation.
                    </div>
                <?php else: ?>
                    <form id="chatForm" class="d-flex">
                        <input type="hidden" id="conv_id" value="<?php echo $conversation_id; ?>">
                        <input type="text" id="message_text" class="form-control msg-input me-2" placeholder="Type a message..." autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-circle"><i class="bi bi-send-fill"></i></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const convId = document.getElementById('conv_id') ? document.getElementById('conv_id').value : null;

        if(chatForm) {
            chatForm.onsubmit = (e) => {
                e.preventDefault();
                const text = document.getElementById('message_text').value;
                if(text.trim() == "") return;

                fetch('send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `conv_id=${convId}&message=${encodeURIComponent(text)}`
                }).then(() => {
                    document.getElementById('message_text').value = "";
                    loadMessages(true); 
                });
            };
        }

        function loadMessages(forceScroll = false) {
            const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 50;

            fetch(`fetch_messages.php?conv_id=<?php echo $conversation_id; ?>`)
                .then(res => res.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    if (isAtBottom || forceScroll) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });
        }

        function deleteMessage(msgId) {
            if(confirm('Delete this message?')){
                fetch(`delete_message.php?id=${msgId}`).then(() => loadMessages());
            }
        }

        function editMessage(msgId, oldText) {
            const newText = prompt("Edit your message:", oldText);
            if(newText != null && newText.trim() != ""){
                fetch('edit_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `msg_id=${msgId}&message=${encodeURIComponent(newText)}`
                }).then(() => loadMessages());
            }
        }

        setInterval(() => loadMessages(false), 2000);
        window.onload = () => loadMessages(true);
    </script>
</body>
</html>