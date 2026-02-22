<?php
session_start();
include 'config.php';
$user_id = 1;

if(isset($_POST['mark_read'], $_POST['dua_id'], $_POST['day'])){
    $mysql = $conn->prepare(
        "INSERT IGNORE INTO dua_progress 
        (user_id, dua_id, day, date_read) 
        VALUES (:user_id, :dua_id, :day, :date_read)");

    $mysql->bindParam(':user_id', $user_id);
    $mysql->bindParam(':dua_id', $_POST['dua_id']);
    $mysql->bindParam(':day', $_POST['day']);
    $mysql->bindParam(':date_read', date('Y-m-d'));
    $mysql->execute();

    $_SESSION['success'] = " Daily Dua marked as read!";
    header("Location: dhashboard.php");
    exit;
}
?>