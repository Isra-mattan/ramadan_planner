
<?php
include 'config.php';
$user_id = 1; 
$id = $_GET['id'];

$mysql = $conn->prepare(
  "DELETE FROM tasks WHERE id=:id AND user_id=:user_id");
$mysql->bindParam(':id', $id);
$mysql->bindParam(':user_id', $user_id);
$mysql->execute();

header("Location: dhashboard.php");
exit;
?>
