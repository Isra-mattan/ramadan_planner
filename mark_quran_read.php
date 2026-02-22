<?php
session_start();
include 'config.php';
$user_id = 1;

if(isset($_POST['mark_read'], $_POST['ayah_range'])){
    $mysql = $conn->prepare(
        "INSERT INTO quran_progress
         (user_id, ayah_range, date_read)
          VALUES (:user_id, :ayah_range, :date_read)");
          
    $mysql->bindParam(':user_id', $user_id);
    $mysql->bindParam(':ayah_range', $_POST['ayah_range']);
    $mysql->bindParam(':date_read', date('Y-m-d'));
    $mysql->execute();

    $_SESSION['success'] = " Quran reading recorded!";
    header("Location: dashboard.php");
    exit;
}
?>