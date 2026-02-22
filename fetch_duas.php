<?php
session_start();
include 'config.php';
$user_id = 1;

$day = (int)$_GET['day'];
$stmt = $conn->prepare("SELECT * FROM daily_duas WHERE day=:day LIMIT 5");
$stmt->bindParam(':day', $day);
$stmt->execute();
$duas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Which duas already read
$read_stmt = $conn->prepare("SELECT dua_id FROM dua_progress WHERE user_id=:user_id AND day=:day");
$read_stmt->bindParam(':user_id', $user_id);
$read_stmt->bindParam(':day', $day);
$read_stmt->execute();
$read_duas = $read_stmt->fetchAll(PDO::FETCH_COLUMN);

foreach($duas as $dua){
    echo '<div class="border p-2 mb-2 rounded">';
    echo '<p>'.$dua['dua_text'].'</p>';
    if(in_array($dua['id'], $read_duas)){
        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Read</span>';
    } else {
        echo '<form method="POST" action="mark_dua_read.php" class="d-inline">';
        echo '<input type="hidden" name="dua_id" value="'.$dua['id'].'">';
        echo '<input type="hidden" name="day" value="'.$day.'">';
        echo '<button type="submit" name="mark_read" class="btn btn-success btn-sm">Mark as Read</button>';
        echo '</form>';
    }
    echo '</div>';
}
?>