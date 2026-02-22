<?php
session_start();
include 'config.php';

$error = "";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $mysql= $conn->prepare("SELECT * FROM users WHERE username=:username");
    $mysql->bindParam(':username', $username);
    $mysql->execute();
    $user = $mysql->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password , $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dhashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Ramadan Planner</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    min-height:100vh;
}

.login-card{
    border:none;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,0.3);
}

.login-header{
    font-weight:600;
}

.btn-custom{
    background:linear-gradient(45deg,#11998e,#38ef7d);
    border:none;
    color:white;
    border-radius:10px;
}

.btn-custom:hover{
    opacity:0.9;
}

.register-link{
    text-decoration:none;
    font-weight:500;
}
</style>

</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
<div class="row justify-content-center">
<div class="col-md-5">

<div class="card login-card p-4">

<div class="text-center mb-4">
    <h3 class="login-header">🌙 Ramadan Planner</h3>
    <p class="text-muted">Login to your account</p>
</div>

<?php if(!empty($error)){ ?>
<div class="alert alert-danger text-center">
    <?= $error ?>
</div>
<?php } ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Username</label>
<div class="input-group">
<span class="input-group-text"><i class="bi bi-person"></i></span>
<input type="text" name="username" class="form-control" required>
</div>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<div class="input-group">
<span class="input-group-text"><i class="bi bi-lock"></i></span>
<input type="password" name="password" class="form-control" required>
</div>
</div>

<div class="d-grid mb-3">
<button name="login" class="btn btn-custom">
<i class="bi bi-box-arrow-in-right"></i> Login
</button>
</div>

<div class="text-center">
<a href="register.php" class="register-link">
Don't have an account? Register
</a>
</div>

</form>

</div>
</div>
</div>
</div>

</body>
</html>
