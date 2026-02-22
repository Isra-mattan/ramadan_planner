<?php
include 'config.php';
$user_id = 1; 

if (isset($_POST['add'])){
    $subject = $_POST['subject'];
    $topic = $_POST['topic'];
    $due_date = $_POST['due_date'];

    $mysql = $conn->prepare("INSERT INTO tasks
    (user_id, subject, topic, due_date) 
    VALUES(:user_id, :subject, :topic, :due_date)");

    $mysql->bindParam(':user_id', $user_id);
    $mysql->bindParam(':subject', $subject);
    $mysql->bindParam(':topic', $topic);
    $mysql->bindParam(':due_date', $due_date);

    $mysql->execute();

    header("Location: dhashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Task</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
        }

        .card{
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .btn-custom{
            background-color: #28a745;
            color: white;
            border-radius: 8px;
        }

        .btn-custom:hover{
            background-color: #218838;
        }

        .back-link{
            text-decoration: none;
            color: #fff;
        }

        .back-link:hover{
            text-decoration: underline;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card p-4">
                <h2 class="text-center mb-4">➕ Add New Task</h2>

                <form method="post">

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <input type="text" name="topic" class="form-control" placeholder="Enter topic" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button name="add" class="btn btn-custom">
                            Add Task
                        </button>
                    </div>

                </form>

                <div class="text-center mt-3">
                    <a href="dhashboard.php" class="back-link">
                        ⬅ Back to Dashboard
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>
