<?php
session_start();
require_once 'config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['username'] === $username) {
            $error = "Username already exists";
        }
        if ($user['email'] === $email) {
            $error = "Email already exists";
        }
    } else {
        $query = "INSERT INTO users (username, email, password, full_name, address, phone) 
                  VALUES('$username', '$email', '$password', '$full_name', '$address', '$phone')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header('location: login.php');
            exit();
        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            color: #dc3545;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-register {
            background-color: #dc3545;
            border: none;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .btn-register:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2><i class="fas fa-utensils"></i> Register</h2>
            <?php if(isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <textarea class="form-control" name="address" placeholder="Delivery Address" required></textarea>
                </div>
                <div class="form-group">
                    <input type="tel" class="form-control" name="phone" placeholder="Phone Number" required>
                </div>
                <button type="submit" class="btn btn-primary btn-register">Register</button>
            </form>
            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
