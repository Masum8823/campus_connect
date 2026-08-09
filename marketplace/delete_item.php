<?php
include '../config.php';

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $id = $_GET['id'];
    $u_id = $_SESSION['user_id'];
    
    // ছবি ডিলিট করা
    $img_q = mysqli_query($conn, "SELECT item_image FROM marketplace_items WHERE id='$id' AND seller_id='$u_id'");
    $img_data = mysqli_fetch_assoc($img_q);
    if($img_data['item_image'] != 'uploads/marketplace/no_product.png' && file_exists("../".$img_data['item_image'])){
        unlink("../".$img_data['item_image']);
    }

    mysqli_query($conn, "DELETE FROM marketplace_items WHERE id='$id' AND seller_id='$u_id'");
}
header("Location: index.php");
exit();