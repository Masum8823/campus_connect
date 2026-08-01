<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['user_id'])){
    header("Location: dashboard.php"); exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

// অন্য ইউজারের তথ্য এবং লাস্ট অ্যাক্টিভিটি আনা
$other_user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$other_user_id'");
$other_user = mysqli_fetch_assoc($other_user_query);

if(!$other_user){ echo "User not found!"; exit(); }

// --- ১. অনলাইন স্ট্যাটাস লজিক ---
$last_active = $other_user['last_activity'];
$is_online = (time() - strtotime($last_active)) < 120; // ২ মিনিট (১২০ সেকেন্ড) থ্রেশহোল্ড

// কনভারসেশন আইডি বের করা
$conv_query = mysqli_query($conn, "SELECT id FROM conversations WHERE (user1_id='$current_user_id' AND user2_id='$other_user_id') OR (user1_id='$other_user_id' AND user2_id='$current_user_id')");
$conv = mysqli_fetch_assoc($conv_query);
$conversation_id = $conv['id'];

// ব্লক চেক
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .chat-container { max-width: 600px; margin: 20px auto; background: white; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; height: 85vh; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        /* Updated Chat Header with Status */
        .chat-header { padding: 12px 20px; background: #0d6efd; color: white; display: flex; align-items: center; }
        .status-dot { font-size: 8px; vertical-align: middle; margin-right: 4px; }
        
        .chat-box { flex-grow: 1; padding: 20px; overflow-y: auto; background: #f9f9f9; display: flex; flex-direction: column; }
        .message-wrapper { margin-bottom: 15px; position: relative; }
        .message { max-width: 80%; padding: 10px 15px; border-radius: 18px; font-size: 14px; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .sent { align-self: flex-end; background: #0d6efd; color: white; border-bottom-right-radius: 2px; }
        .received { align-self: flex-start; background: white; color: #050505; border-bottom-left-radius: 2px; border: 1px solid #eee; }
        
        .reply-preview-in-chat { background: rgba(0,0,0,0.05); padding: 5px 10px; border-radius: 10px; font-size: 12px; border-left: 3px solid #0d6efd; margin-bottom: 8px; color: #555; }
        .sent .reply-preview-in-chat { background: rgba(255,255,255,0.2); color: #fff; border-left-color: white; }

        .chat-img { max-width: 100%; border-radius: 12px; cursor: pointer; }
        .file-attachment { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; background: rgba(0,0,0,0.05); padding: 8px 12px; border-radius: 12px; }
        
        .chat-footer { padding: 0; background: white; border-top: 1px solid #eee; }
        .msg-input-container { padding: 15px; display: flex; align-items: center; }
        .msg-input { border-radius: 25px; border: none; padding: 10px 20px; background: #f0f2f5; flex-grow: 1; }

        .msg-actions { display: none; position: absolute; top: -25px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 10px; padding: 2px 8px; z-index: 10; gap: 10px; }
        .message-wrapper:hover .msg-actions { display: flex; }
        .sent .msg-actions { right: 0; }
        .received .msg-actions { left: 0; }
        .msg-actions i { cursor: pointer; font-size: 14px; color: #666; transition: 0.2s; }
        .msg-actions i:hover { color: #0d6efd; }

        .msg-time { font-size: 10px; margin-top: 4px; opacity: 0.7; }
        #reply_container { transition: 0.3s; }
    </style>
</head>
<body>

    <div class="container">
        <div class="chat-container">
            <!-- Header with Active Status -->
            <div class="chat-header">
                <a href="messages.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <?php $img = ($other_user['profile_pic'] != 'default.png') ? "../" . $other_user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($other_user['full_name']); ?>
                <img src="<?php echo $img; ?>" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold"><?php echo $other_user['full_name']; ?></h6>
                    <!-- ২. স্ট্যাটাস ডিসপ্লে -->
                    <small style="font-size: 10px;">
                        <?php if($is_online): ?>
                            <span class="text-white"><i class="bi bi-circle-fill text-success status-dot"></i> Active Now</span>
                        <?php else: ?>
                            <span class="text-light opacity-75">Last seen <?php echo getTimeAgo($last_active); ?></span>
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <div class="chat-box" id="chatBox"></div>

            <div class="chat-footer">
                <?php if($is_chat_blocked): ?>
                    <div class="alert alert-secondary mb-0 py-3 text-center small fw-bold"><i class="bi bi-slash-circle"></i> Chat Blocked</div>
                <?php else: ?>
                    
                    <div id="reply_container" class="p-2 border-top bg-light d-none" style="border-radius: 15px 15px 0 0;">
                        <div class="d-flex justify-content-between align-items-center px-2">
                            <div class="small text-muted border-start border-primary border-4 ps-2 overflow-hidden" style="max-height: 40px;">
                                Replying to: <span id="reply_text_preview" class="fw-bold"></span>
                            </div>
                            <button type="button" class="btn-close" style="font-size: 10px;" onclick="cancelReply()"></button>
                        </div>
                    </div>

                    <div class="msg-input-container">
                        <form id="chatForm" class="d-flex align-items-center w-100" enctype="multipart/form-data">
                            <input type="hidden" id="conv_id" value="<?php echo $conversation_id; ?>">
                            <input type="hidden" id="reply_to_id" value="">
                            
                            <input type="file" id="msg_file" style="display:none" accept="image/*,.pdf,.docx,.zip">
                            <button type="button" class="btn btn-light text-primary rounded-circle me-2" onclick="document.getElementById('msg_file').click()">
                                <i class="bi bi-paperclip fs-5"></i>
                            </button>

                            <input type="text" id="message_text" class="form-control msg-input me-2" placeholder="Type a message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary rounded-circle shadow-sm"><i class="bi bi-send-fill"></i></button>
                        </form>
                    </div>
                    <div id="file_preview" class="small text-muted pb-2 ps-5 d-none"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const fileInput = document.getElementById('msg_file');
        const filePreview = document.getElementById('file_preview');
        const replyContainer = document.getElementById('reply_container');
        let currentReplyId = null;

        fileInput.onchange = () => {
            if(fileInput.files.length > 0) {
                filePreview.innerHTML = `<i class="bi bi-file-earmark-check"></i> ${fileInput.files[0].name}`;
                filePreview.classList.remove('d-none');
            }
        };

        function setupReply(id, text) {
            currentReplyId = id;
            document.getElementById('reply_to_id').value = id;
            document.getElementById('reply_text_preview').innerText = text;
            replyContainer.classList.remove('d-none');
            document.getElementById('message_text').focus();
        }

        function cancelReply() {
            currentReplyId = null;
            document.getElementById('reply_to_id').value = "";
            replyContainer.classList.add('none');
            replyContainer.classList.add('d-none');
        }

        chatForm.onsubmit = (e) => {
            e.preventDefault();
            const text = document.getElementById('message_text').value;
            const convId = document.getElementById('conv_id').value;
            
            if(text.trim() == "" && fileInput.files.length == 0) return;

            const formData = new FormData();
            formData.append('conv_id', convId);
            formData.append('message', text);
            if(currentReplyId) formData.append('reply_to', currentReplyId);
            if(fileInput.files.length > 0) formData.append('chat_file', fileInput.files[0]);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                chatForm.reset();
                cancelReply();
                filePreview.classList.add('d-none');
                loadMessages(true);
            });
        };

        function loadMessages(forceScroll = false) {
            const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 50;
            fetch(`fetch_messages.php?conv_id=<?php echo $conversation_id; ?>`)
                .then(res => res.text())
                .then(data => {
                    chatBox.innerHTML = data;
                    if (isAtBottom || forceScroll) chatBox.scrollTop = chatBox.scrollHeight;
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