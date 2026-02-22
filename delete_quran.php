<?php
session_start();
include 'config.php';

if(isset($_GET['id']) && isset($_SESSION['user_id'])){

    $stmt = $conn->prepare("DELETE FROM quran_progress 
                            WHERE id=:id AND user_id=:user_id");

    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}

header("Location: dhashboard.php");
exit;
?>
