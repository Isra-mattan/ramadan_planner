<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

   //FETCH TASKS

$sql_tasks = $conn->prepare("SELECT * FROM tasks WHERE user_id=:user_id ORDER BY due_date ASC");
$sql_tasks->bindParam(':user_id', $user_id);
$sql_tasks->execute();
$tasks = $sql_tasks->fetchAll(PDO::FETCH_ASSOC);

  // DAILY DUAS

$today_day = date('j');
if($today_day > 30){ $today_day = 30; }

$sql_duas = $conn->prepare("SELECT * FROM daily_duas WHERE day = :day LIMIT 5");
$sql_duas->bindParam(':day', $today_day);
$sql_duas->execute();
$duas = $sql_duas->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['mark_read'], $_POST['dua_id'], $_POST['day'])){
    $mysql = $conn->prepare("INSERT INTO dua_progress (user_id, dua_id, day, date_read) 
                             VALUES (:user_id, :dua_id, :day, :date_read)");
    $mysql->bindParam(':user_id', $user_id);
    $mysql->bindParam(':dua_id', $_POST['dua_id']);
    $mysql->bindParam(':day', $_POST['day']);
    $date_read = date('Y-m-d');
    $mysql->bindParam(':date_read', $date_read);
    $mysql->execute();

    $_SESSION['success'] = "Daily Dua marked as read!";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Read Duas 
$read_stmt = $conn->prepare("SELECT dua_id FROM dua_progress WHERE user_id=:user_id AND day=:day");
$read_stmt->bindParam(':user_id', $user_id);
$read_stmt->bindParam(':day', $today_day);
$read_stmt->execute();
$read_duas = $read_stmt->fetchAll(PDO::FETCH_COLUMN);

// Dua Progress 
$sql_dua_progress = $conn->prepare("SELECT DISTINCT day FROM dua_progress WHERE user_id=:user_id");
$sql_dua_progress->bindParam(':user_id', $user_id);
$sql_dua_progress->execute();
$dua_days_read = $sql_dua_progress->fetchAll(PDO::FETCH_COLUMN);

$total_days = 30;
$days_read_count = count($dua_days_read);
$dua_progress_percent = ($days_read_count / $total_days) * 100;

// Streak 
$streak = 0;
$mysql = $conn->prepare("SELECT DISTINCT date_read FROM dua_progress 
                        WHERE user_id=:user_id ORDER BY date_read DESC");
$mysql->bindParam(':user_id', $user_id);
$mysql->execute();
$dates = $mysql->fetchAll(PDO::FETCH_COLUMN);

$today = date('Y-m-d');
foreach($dates as $date){
    if($date == $today){
        $streak++;
        $today = date('Y-m-d', strtotime($today.' -1 day'));
    } else { break; }
}
// MARK QURAN READING
if(isset($_POST['mark_quran']) && isset($_SESSION['user_id'])){

    $ayah_range = trim($_POST['ayah_range']);
    $today = date("Y-m-d");

    // Check if already read today
    $check = $conn->prepare("SELECT * FROM quran_progress 
                             WHERE user_id=:user_id AND date_read=:today");
    $check->bindParam(':user_id', $_SESSION['user_id']);
    $check->bindParam(':today', $today);
    $check->execute();

    if($check->rowCount() == 0){

        $insert = $conn->prepare("INSERT INTO quran_progress 
                                  (user_id, ayah_range, date_read) 
                                  VALUES (:user_id, :ayah_range, :today)");

        $insert->bindParam(':user_id', $_SESSION['user_id']);
        $insert->bindParam(':ayah_range', $ayah_range);
        $insert->bindParam(':today', $today);
        $insert->execute();

        $success = "Reading saved successfully!";
    } else {
        $error = "You already recorded reading for today.";
    }
}
$quran = $conn->prepare("SELECT * FROM quran_progress 
                         WHERE user_id=:user_id 
                         ORDER BY date_read DESC");
$quran->bindParam(':user_id', $_SESSION['user_id']);
$quran->execute();
$quran_progress = $quran->fetchAll(PDO::FETCH_ASSOC);

$days_read = count($quran_progress);
$progress_percent = min(($days_read / 30) * 100, 100);

?>

<!DOCTYPE html>
<html>


<head>
<title>Ramadan Planner Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/style.css">

</head>



<body>
<div class="container py-4">

<h2 class="mb-4 main-title">🌙 Ramadan Planner Dashboard</h2>

<?php if(isset($_SESSION['success'])){ ?>
<div class="alert alert-success alert-dismissible fade show">
<?= $_SESSION['success']; unset($_SESSION['success']); ?>
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } ?>

<!-- STATS -->
<div class="row mb-4 g-3">
<div class="col-md-3"><div class="stat-box stat-green">
<h5>Dua Progress</h5><h3><?= round($dua_progress_percent) ?>%</h3>
</div></div>

<div class="col-md-3"><div class="stat-box stat-blue">
<h5>Streak 🔥</h5><h3><?= $streak ?> Days</h3>
</div></div>

<div class="col-md-3"><div class="stat-box stat-orange">
<h5>Quran Progress</h5><h3><?= round($progress_percent) ?>%</h3>
</div></div>

<div class="col-md-3"><div class="stat-box stat-dark">
<h5>Total Tasks</h5><h3><?= count($tasks) ?></h3>
</div></div>
</div>

<!-- TASKS -->
<div class="card mb-4">
<div class="card-header bg-primary text-white">📚 Study Tasks</div>
<div class="card-body">

<a href="add_task.php" class="btn btn-success mb-3">
<i class="bi bi-plus-circle"></i> Add Task
</a>

<table class="table table-hover align-middle">
<thead class="table-dark">
<tr>
<th>Subject</th>
<th>Topic</th>
<th>Due</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php if(!empty($tasks)){ foreach($tasks as $task){ ?>
<tr>
<td><?= htmlspecialchars($task['subject']) ?></td>
<td><?= htmlspecialchars($task['topic']) ?></td>
<td><?= $task['due_date'] ?></td>
<td><span class="badge bg-info"><?= $task['status'] ?></span></td>
<td>
<a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">
<i class="bi bi-pencil"></i>
</a>
<a href="delete_task.php?id=<?= $task['id'] ?>" 
onclick="return confirm('Delete?')" 
class="btn btn-sm btn-danger">
<i class="bi bi-trash"></i>
</a>
</td>
</tr>
<?php }} else { ?>
<tr><td colspan="5" class="text-center">No tasks yet.</td></tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

<!-- DAILY DUAS -->
<div class="card mb-4">
<div class="card-header bg-success text-white">
    🌙 Today's Duas (<?= $today_day ?>/30)
</div>
<div class="card-body">

<?php if(!empty($duas)): ?>

    <?php foreach($duas as $dua): ?>
        <div class="border rounded p-3 mb-3 bg-light">

            <p class="fs-5"><?= htmlspecialchars($dua['dua_text']) ?></p>

            <?php if(in_array($dua['id'], $read_duas)): ?>
                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i> Read
                </span>
            <?php else: ?>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="dua_id" value="<?= $dua['id'] ?>">
                    <input type="hidden" name="day" value="<?= $today_day ?>">
                    <button name="mark_read" class="btn btn-success btn-sm">
                        <i class="bi bi-check2-square"></i> Mark as Read
                    </button>
                </form>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

<?php else: ?>
    <p>No duas found for today.</p>
<?php endif; ?>

<hr>

<h6>Dua Progress</h6>

<p>
Days Completed: <?= $days_read_count ?>/<?= $total_days ?> 
| Streak: <?= $streak ?> 🔥
</p>

<div class="progress mb-3">
    <div class="progress-bar bg-success" style="width:<?= $dua_progress_percent ?>%"></div>
</div>

<!-- Calendar Small View -->
<div>
<?php for($i=1;$i<=$total_days;$i++){ ?>
<span class="badge badge-day <?= in_array($i,$dua_days_read)?'bg-success':'bg-secondary' ?>">
<?= $i ?>
</span>
<?php } ?>
</div>

</div>
</div>

<!-- RAMADAN CALENDAR -->
<div class="card mb-4">
<div class="card-header bg-warning text-dark">
    <i class="bi bi-calendar-week"></i> Ramadan Duas Calendar
</div>
<div class="card-body">

<div class="row g-2">

<?php
for($d=1;$d<=30;$d++){

    $mysql = $conn->prepare("SELECT COUNT(*) FROM dua_progress 
                            WHERE user_id=:user_id AND day=:day");
    $mysql->bindParam(':user_id', $user_id);
    $mysql->bindParam(':day', $d);
    $mysql->execute();
    $count_read = (int)$mysql->fetchColumn();

    if($count_read == 5){
        $bg = 'bg-success text-white';
    } elseif($count_read > 0){
        $bg = 'bg-warning text-dark';
    } else {
        $bg = 'bg-secondary text-white';
    }
?>
    <div class="col-4 col-md-2 text-center">
        <div class="p-2 rounded <?= $bg ?>" 
             style="min-height:60px; cursor:pointer;"
             data-bs-toggle="modal" 
             data-bs-target="#duasModal" 
             data-day="<?= $d ?>">
            <strong>Day <?= $d ?></strong><br>
            <small><?= $count_read ?>/5 Duas</small>
        </div>
    </div>
<?php } ?>

</div>
</div>
</div>


<div class="card shadow mb-4 border-0">
<div class="card-header bg-dark text-white d-flex justify-content-between">
    <span>📖 Quran Progress</span>
    <span><?= $days_read ?>/30 Days</span>
</div>

<div class="card-body">

<?php if(isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if(isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="progress mb-4" style="height:20px;">
    <div class="progress-bar bg-dark" 
         style="width:<?= $progress_percent ?>%">
    </div>
</div>

<form method="POST" class="row g-2 mb-4">
    <div class="col-md-8">
        <input type="text" name="ayah_range" 
        class="form-control" 
        placeholder="Example: 1-20" required>
    </div>
    <div class="col-md-4">
        <button name="mark_quran" 
        class="btn btn-dark w-100">
        Save Reading
        </button>
    </div>
</form>

<hr>

<h6 class="mb-3">📜 Reading History</h6>

<?php if($days_read > 0): ?>

<div style="max-height:250px; overflow-y:auto;">
<ul class="list-group">

<?php foreach($quran_progress as $q): ?>
<li class="list-group-item d-flex justify-content-between align-items-center">

<div>
<strong><?= htmlspecialchars($q['ayah_range']) ?></strong>
<br>
<small class="text-muted">
<?= date("F d, Y", strtotime($q['date_read'])) ?>
</small>
</div>

<a href="delete_quran.php?id=<?= $q['id'] ?>" 
class="btn btn-sm btn-outline-danger"
onclick="return confirm('Delete record?')">
Delete
</a>

</li>
<?php endforeach; ?>

</ul>
</div>

<?php else: ?>
<p class="text-muted">No reading recorded yet.</p>
<?php endif; ?>

<?php if($days_read >= 30): ?>
<div class="alert alert-success mt-4">
🎉 Congratulations! You completed 30 days Quran reading!
</div>
<?php endif; ?>

</div>
</div>


</div>
<!-- DUAS MODAL -->
<div class="modal fade" id="duasModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content rounded-4 shadow">

<div class="modal-header bg-success text-white">
<h5 class="modal-title">
Duas for Day <span id="modalDay"></span>
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="modalBody">
Loading...
</div>

</div>
</div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
var duasModal = document.getElementById('duasModal');

duasModal.addEventListener('show.bs.modal', function (event) {

    var button = event.relatedTarget;
    var day = button.getAttribute('data-day');

    document.getElementById('modalDay').textContent = day;

    var modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = 'Loading...';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_duas.php?day=' + day, true);

    xhr.onload = function(){
        if(this.status == 200){
            modalBody.innerHTML = this.responseText;
        } else {
            modalBody.innerHTML = 'Failed to load duas.';
        }
    };

    xhr.send();
});
</script>

</body>
</html>
