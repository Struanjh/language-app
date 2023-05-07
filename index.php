<?php

session_start();
use function PHPSTORM_META\type;

include 'config.php';
include './CONTROLLER/contr.user.login.php';
include './CONTROLLER/contr.user.register.php';
include './CONTROLLER/contr.user.oauth.php';


//Returns all the raw data after the HTTP-headers of the request, regardless of content-type
$content = trim(file_get_contents("php://input"));
$contentType = trim($_SERVER["CONTENT_TYPE"] ?? '');



if (isset($_GET['code'])) {
    $user = new OauthLogIn($client);
    die();
}

switch ($_GET['action'] ?? '') {
    case 'newUser':
        $user = new RegisterUser();
        $user->validateNewUser();
        break;
    case 'login':
        $user = new LoginUser();
        $user->authenticateUser();
        break;
    case 'logout':
        $user = new User();
        $user->logOutUser();
        break;
    default:
        $user = new User();
        $user->isUserLoggedIn($client);
        break;
  }