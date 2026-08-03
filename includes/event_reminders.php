<?php
// PHPMailer ব্যবহার করার জন্য প্রয়োজনীয় ফাইল
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// পাথগুলো খেয়াল করো, includes ফোল্ডার থেকে এক ধাপ পেছনে গিয়ে PHPMailer খুঁজতে হবে
require __DIR__ . '/../auth/PHPMailer/Exception.php';
require __DIR__ . '/../auth/PHPMailer/PHPMailer.php';
require __DIR__ . '/../auth/PHPMailer/SMTP.php';

// বর্তমান তারিখ থেকে ১ দিন পরের তারিখ বের করা
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$uid = $_SESSION['user_id'];

// চেক করা: এই ইউজার আগামীকাল হবে এমন কোনো ইভেন্টে "Going" দিয়েছে কি না
$query = "SELECT e.title, e.event_date, e.event_time, e.location, u.email, u.full_name 
          FROM event_participations ep 
          JOIN events e ON ep.event_id = e.id 
          JOIN users u ON ep.user_id = u.id 
          WHERE ep.user_id = '$uid' AND ep.status = 'going' AND e.event_date = '$tomorrow'";

$remind_res = mysqli_query($conn, $query);

if(mysqli_num_rows($remind_res) > 0) {
    while($data = mysqli_fetch_assoc($remind_res)) {
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'masum688823@gmail.com'; // তোমার জিমেইল
            $mail->Password   = 'qpcm gmol tydu rqed';   // তোমার অ্যাপ পাসওয়ার্ড
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('masum688823@gmail.com', 'CampusConnect Notifications');
            $mail->addAddress($data['email'], $data['full_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Reminder: Upcoming Event Tomorrow!';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #0d6efd;'>Event Reminder 🔔</h2>
                    <p>Hello <strong>{$data['full_name']}</strong>,</p>
                    <p>This is a reminder that the event <strong>'{$data['title']}'</strong> is happening tomorrow.</p>
                    <p>📍 <strong>Location:</strong> {$data['location']}<br>
                       ⏰ <strong>Time:</strong> " . date('h:i A', strtotime($data['event_time'])) . "</p>
                    <p>We look forward to seeing you there!</p>
                    <br>
                    <p>Best Regards,<br>CampusConnect Team</p>
                </div>";

            $mail->send();
            
            // একই ইমেইল যেন বারবার না যায়, তার জন্য একটি ছোট লজিক (Optional) রাখা যেতে পারে। 
            // আপাতত একাডেমিক ডেমোর জন্য এটিই যথেষ্ট।
            
        } catch (Exception $e) {
            // ইমেইল না গেলে সাইলেন্টলি এরর হ্যান্ডেল করবে
        }
    }
}
?>