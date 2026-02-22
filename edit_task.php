<?php 
include 'config.php'; 
$user_id = 1; 
$id = isset($_GET['id']) ? $_GET['id'] : 0;  

if($id == 0){
    echo "Task ID lama helin!";
    exit;
}

// Fetch task
$mysql = $conn->prepare("SELECT * FROM tasks WHERE id=:id AND user_id=:user_id");
$mysql->bindParam(':id', $id);
$mysql->bindParam(':user_id', $user_id);
$mysql->execute();
$task = $mysql->fetch(PDO::FETCH_ASSOC);

if(!$task){
    echo "Task ma jiro ama ma lihid ogolaansho aad ku aragto.";
    exit;
}

if(isset($_POST['update'])){
    $subject = $_POST['subject'];
    $topic = $_POST['topic'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $mysql = $conn->prepare("UPDATE tasks SET subject=:subject, topic=:topic, due_date=:due_date, status=:status WHERE id=:id AND user_id=:user_id");
    $mysql->bindParam(':subject', $subject);
    $mysql->bindParam(':topic', $topic);
    $mysql->bindParam(':due_date', $due_date);
    $mysql->bindParam(':status', $status);
    $mysql->bindParam(':id', $id);
    $mysql->bindParam(':user_id', $user_id);
    $mysql->execute();

    header("Location: dhashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .card{
            border-radius:15px;
            box-shadow:0 10px 25px rgba(0,0,0,0.2);
        }
        .form-control:focus{
            box-shadow:none;
            border-color:#4e73df;
        }
        .btn-primary{
            background:#4e73df;
            border:none;
        }
        .btn-primary:hover{
            background:#2e59d9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="text-center mb-4">✏️ Edit Task</h3>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" 
                               value="<?= htmlspecialchars($task['subject']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <input type="text" name="topic" class="form-control" 
                               value="<?= htmlspecialchars($task['topic']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" 
                               value="<?= $task['due_date'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?= $task['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="done" <?= $task['status']=='done'?'selected':'' ?>>Done</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button name="update" class="btn btn-primary">Update Task</button>
                        <a href="dhashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
