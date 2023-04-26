<?php
require_once __DIR__ . '/../../config.php';

session_start();

if(isset($_POST['logout'])) {
  echo json_encode(logOutUser());
} else if(isset($_POST['newUser'])) {
  unset($_POST['newUser']);
  echo json_encode(validateNewUser());
} else if (isset($_POST['logIn'])) {
  echo json_encode(authenticateUser());
} else if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
} else
//Show Login/Register Forms
{
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../scripts/login.js" defer type="module"></script>
    <title>Login</title>
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body>
  <div class="page-container">
    <div class="form-btn-container">
      <a href="#" id="show-login-click" class="btn">Sign In</a>
      <a href="#" id="show-register-click" class="btn">Sign Up</a>
    </div>
  </div>
  <div class="login-form form-container">
    <form id="login-form">
        <span class="close">&times;</span>
        <h2>Login</h2>
        <input type="email" placeholder="email" name="login-email">
        <input type="password" placeholder="enter password" id="login-password">
        <button type="button" id="login-submit" class="btn">Sign in</button>
        <?php
        echo "<a href='" . $client->createAuthUrl() . "'>
                <img id='google-login' src='../assets/google/1x/btn_google_signin_dark_normal_web.png' alt='Sign in with google'>
              </a> ";
        ?>
        <div class="error status-msg"></div>
    </form>
  </div>
  <div class="register-form form-container">
    <form id="register-form">
        <span class="close">&times;</span>
        <h2>Register</h2>
        <input type="text" name="firstname "placeholder="firstname" id="firstname">
        <div class="error" id="firstname-error"></div>
        <input type="text" name="lastname" placeholder="lastname" id="lastname">
        <div class="error" id="lastname-error"></div>
        <input type="email" name="email" placeholder="email" id="email">
        <div class="error" id="email-error"></div>
        <input type="password" placeholder="enter password" id="password">
        <div class="error" id="password-error"></div>
        <input type="password" placeholder="confirm password" id="confirm-password">
        <div class="error" id="confirm-password-error"></div>
        <button type="button" id="register-submit" class="btn">Sign up</button>
        <div class="error status-msg"><div>
    </form>
  </div>
</body>

<?php } ?>