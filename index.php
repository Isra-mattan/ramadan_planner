<?php
include 'config.php';

$message = "";

if (isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Hubi username inuu jiro
    $check = $conn->prepare("SELECT * FROM users WHERE username=:username");
    $check->bindParam(':username', $username);
    $check->execute();

    if($check->rowCount() > 0){
        $message = "<div class='alert alert-danger'>Username already exists!</div>";
    }else {
        $mysql = $conn->prepare("INSERT INTO users(username, password) VALUES(:username, :password)");
        $mysql->bindParam(':username', $username);
        $mysql->bindParam(':password', $password);
        $mysql->execute();

        $message = "<div class='alert alert-success'>
                        Registration successful! 
                        <a href='login.php' class='alert-link'>Login Now</a>
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card{
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
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
        <div class="col-md-5">
            <div class="card p-4">

                <h3 class="text-center mb-4">
                    <i class="bi bi-person-plus-fill"></i> Create Account
                </h3>

                <?= $message; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button name="register" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Register
                        </button>
                    </div>

                </form>

                <div class="text-center mt-3">
                    Already have an account? 
                    <a href="login.php" class="text-decoration-none">Login here</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
