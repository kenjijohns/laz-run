<?php
session_start();

if (isset($_SESSION['user_authenticated'])) {
    header('Location: index.php');
    exit();
}

$pageTitle = "Login Page";
require '../db.php';
include './header.php';
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<div class="login-page">

    <div class="logocontainer">
        <img src="../assets/images/PG.png" alt="ADALogo" class="ADALogo">
    </div>

    <div class="login">

        <div class="title">
            <h1>Quiz Dashboard</h1>
        </div>

        <div class="login-form">
            <form action="functions.php" method="post" autocomplete="off">
                <div class="container">

                    <input type="text" placeholder="Username" name="username" id="username" required class="border-none"><br>

                    <div class="password-container">
                        <input type="password" id="password" placeholder="Password" name="password" required>
                        <button type="button" id="togglePassword">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>


                    <button type="submit" class="loginpage-button" name="login">Login</button>
                </div>
            </form>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const passwordInput = document.getElementById('password');
                const togglePasswordButton = document.getElementById('togglePassword'); // Update the id here
                const eyeIcon = document.getElementById('eye-icon');

                togglePasswordButton.addEventListener('click', () => {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;

                    eyeIcon.classList.toggle('fa-eye-slash');
                });
            });
        </script>


    </div>

</div>