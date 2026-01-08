<?php
session_start();
include 'db.php';

// Check if user submitted the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Secure the input slightly
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        // Redirect based on Role
        if ($row['role'] == 'admin') {
            header("Location: admin.php");
        } elseif ($row['role'] == 'worker') {
            header("Location: worker.php");
        } else {
            header("Location: index.html"); // Citizens go to the App
        }
        exit();
    } else {
        $error = "❌ Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SwachhSnap</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #2ecc71, #1abc9c); /* Swachh Green Theme */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .app-logo {
            font-size: 50px;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .app-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 50px;
            padding: 12px 20px;
            background-color: #f7f9fc;
            border: 1px solid #e1e1e1;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2ecc71;
            background-color: white;
        }

        .input-group-text {
            border-radius: 50px 0 0 50px;
            background-color: #f7f9fc;
            border: 1px solid #e1e1e1;
            border-right: none;
            color: #bdc3c7;
        }
        
        /* Fix the input radius when joined with icon */
        .input-group .form-control {
            border-radius: 0 50px 50px 0;
            border-left: none;
        }

        .btn-login {
            background: #27ae60;
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #219150;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .footer-text {
            margin-top: 20px;
            font-size: 12px;
            color: #95a5a6;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="app-logo">
            <i class="fas fa-recycle"></i>
        </div>
        
        <h3 class="app-title">SwachhSnap</h3>
        <p class="text-muted small mb-4">Smart Waste Management System</p>

        <?php if(isset($error)) { ?>
            <div class="alert alert-danger p-2 small rounded-pill"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" autocomplete="off">
            
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Username" required autocomplete="new-password">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-login">LOGIN</button>

        </form>

        <div class="footer-text">
            For DIPEX 2026 Competition<br>
            Developed by <b>Jivan Jadhav</b>
        </div>
    </div>

</body>
</html>