<?php
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'connection.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password']) {
        $_SESSION['email'] = $email;
        header("Location: admin_panel.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }

    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | KM/STR/AL-Marjan Muslim Ladies College</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">KM/STR/AL-Marjan Muslim Ladies College</h1>
            <nav class="space-x-6 text-white font-medium">
                <a href="#" class="hover:underline">Home</a>
                <a href="aboutus.php" class="hover:underline">About Us</a>
                <a href="index.php" class="hover:underline">Login</a>
                <a href="register.php" class="hover:underline">Register</a>
            </nav>
        </div>
    </header>

    <!-- Login Form -->
    <main class="flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Login</h2>
            <?php if ($error): ?>
                <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Email</label>
                    <input type="email" name="email" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold">Password</label>
                    <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white text-center p-4">
        &copy; <?= date("Y") ?> KM/STR/AL-Marjan Muslim Ladies College. All rights reserved.
    </footer>
</body>
</html>
