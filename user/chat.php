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
        .message { max-width: 75%; padding: 10px 15px; border-radius: 18px; margin-bottom: 5px; font-size: 14px; position: relative; }
        .sent { align-self: flex-end; background: #0d6efd; color: white; border-bottom-right-radius: 2px; }
        .received { align-self: flex-start; background: #e4e6eb; color: #050505; border-bottom-left-radius: 2px; }
        .chat-img { max-width: 100%; border-radius: 10px; cursor: pointer; }
        .file-attachment { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 10px; }
        .received .file-attachment { background: rgba(0,0,0,0.05); }
        .chat-footer { padding: 15px; background: white; border-top: 1px solid #eee; }
        .msg-input { border-radius: 25px; border: none; padding: 10px 20px; background: #f0f2f5; }
        .msg-time { font-size: 10px; margin-bottom: 10px; opacity: 0.7; }
        .sent + .msg-time { text-align: right; }
    </style>
</head>
<body>

    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                <a href="messages.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h6 class="mb-0 fw-bold"><?php echo $other_user['full_name']; ?></h6>
            </div>

            <div class="chat-box" id="chatBox"></div>

            <div class="chat-footer">
                <?php if($is_chat_blocked): ?>
                    <div class="alert alert-secondary mb-0 py-2 text-center small fw-bold"><i class="bi bi-slash-circle"></i> Chat Blocked</div>
                <?php else: ?>
                    <form id="chatForm" class="d-flex align-items-center">
                        <input type="hidden" id="conv_id" value="<?php echo $conversation_id; ?>">
                        
                        <!-- File Input (Hidden) -->
                        <input type="file" id="msg_file" style="display:none" accept=".jpg,.jpeg,.png,.pdf,.docx,.zip">
                        <button type="button" class="btn btn-light text-primary rounded-circle me-2" onclick="document.getElementById('msg_file').click()">
                            <i class="bi bi-paperclip fs-5"></i>
                        </button>

                        <input type="text" id="message_text" class="form-control msg-input me-2" placeholder="Type a message..." autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-circle"><i class="bi bi-send-fill"></i></button>
                    </form>
                    <div id="file_preview" class="small text-muted mt-2 ps-5 d-none"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const fileInput = document.getElementById('msg_file');
        const filePreview = document.getElementById('file_preview');

        // ফাইল সিলেক্ট করলে প্রিভিউ দেখানো
        fileInput.onchange = () => {
            if(fileInput.files.length > 0) {
                filePreview.innerHTML = `<i class="bi bi-file-earmark-check"></i> ${fileInput.files[0].name} selected`;
                filePreview.classList.remove('d-none');
            }
        };

        chatForm.onsubmit = (e) => {
            e.preventDefault();
            const text = document.getElementById('message_text').value;
            const convId = document.getElementById('conv_id').value;
            
            if(text.trim() == "" && fileInput.files.length == 0) return;

            // FormData ব্যবহার করছি ফাইল পাঠানোর জন্য
            const formData = new FormData();
            formData.append('conv_id', convId);
            formData.append('message', text);
            if(fileInput.files.length > 0) {
                formData.append('chat_file', fileInput.files[0]);
            }

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                chatForm.reset();
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

        setInterval(() => loadMessages(false), 2000);
        window.onload = () => loadMessages(true);
    </script>
</body>
</html>