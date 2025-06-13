
<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $email, $password);
    $stmt->execute();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="../assets/blood.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Setetes</title>
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
        .btn-register {
            background-color: #970c10;
        }
        .btn-register:hover {
            background-color: #7e0a0d;
        }
        .login-link a {
            color: #970c10;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-[#918e80] p-10 rounded-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-red-800">Setetes</h2>
            <p class="text-sm text-red-700">Bersama Setetes, kita bantu lebih banyak nyawa.</p>
        </div>
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="form-label block mb-1">Username</label>
                <input type="text" name="username" id="username" class="form-input w-full py-2 px-3 rounded" required>
            </div>
            <div class="mb-4">
                <label for="email" class="form-label block mb-1">Email</label>
                <input type="email" name="email" id="email" class="form-input w-full py-2 px-3 rounded" required>
            </div>
            <div class="mb-6">
                <label for="password" class="form-label block mb-1">Password</label>
                <input type="password" name="password" id="password" class="form-input w-full py-2 px-3 rounded" required>
            </div>
            <button type="submit" class="btn-register w-full py-2 text-white font-semibold rounded">Register</button>
            <div class="text-center mt-4 text-sm login-link text-black">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </form>
    </div>
</body>
</html>
