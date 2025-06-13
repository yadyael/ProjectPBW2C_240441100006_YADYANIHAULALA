<?php
session_start();
require '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../dashboard.php");
            exit;
        }
    }
    $error = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Setetes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0ede0;
        }
        .form-label {
            color: #970c10;
            font-weight: 600;
        }
        .form-input {
            background-color: #fff;
            color: #433f30;
            border: 1px solid #433f30;
        }
        .form-input:focus {
            outline: none;
            border-color: #970c10;
        }
        .btn-login {
            background-color: #970c10;
        }
        .btn-login:hover {
            background-color: #7e0a0d;
        }
        .signup-link a {
            color: #970c10;
            text-decoration: underline;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-[#918e80] p-10 rounded-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-red-800">Welcome back!</h2>
            <p class="text-sm text-red-700">Mari lanjutkan kebaikan hari ini juga.</p>
        </div>
        <?php if (isset($error)) echo "<div class='bg-red-100 text-red-700 px-4 py-2 rounded mb-4'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="form-label block mb-1">Username</label>
                <input type="text" name="username" id="username" class="form-input w-full py-2 px-3 rounded" required>
            </div>
            <div class="mb-6">
                <label for="password" class="form-label block mb-1">Password</label>
                <input type="password" name="password" id="password" class="form-input w-full py-2 px-3 rounded" required>
            </div>
            <button type="submit" class="btn-login w-full py-2 text-white font-semibold rounded">Log in</button>
            <div class="text-center mt-4 text-sm signup-link text-black">
                Don’t have an account? <a href="register.php">Sign up</a>
            </div>
        </form>
    </div>
</body>
</html>
