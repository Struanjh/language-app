<?php

use function PHPSTORM_META\type;

require_once __DIR__ . '/../../config.php';

session_start();

if(isset($_GET['logout'])) {
    logOutUser();
}

//No authentication code, redirect to login page
if(!isset($_GET['code']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    die();
}

// //Authorization code received from google OAuth flow, and no session exists
// if(isset($_GET['code']) && !isset($_SESSION['user_id'])) {
//     handleOAuthLogin($client);
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/header.css">
    <script src="../scripts/header.js" type="module" defer></script>
    <script src="../scripts/functions.js" type="module" defer></script>
    <title>Document</title>
</head>
<body>
    <?php 
        require_once '../includes/header.php'; 
        
    ?>
    <div id="user-welcome-message">
        <h4>Welcome <?=$this->firstName?> <?=$this->lastName?></h4>
        <p>You joined Language Up on <?=date('l jS F Y', $this->joinDate);?></p>
        <p>You've been a member for <?=intval((time() - $this->joinDate) / 60 / 60 / 24);?> days</p>
        <p>You've learned <?=$this->learnedWords?> words from <?=$this->youTubeVideos?> YouTube videos and <?=$this->newsArticles?> News Articles</p>
        <p>To review the words you've already learned, go to the <a href="">Review Content</a> section</p>
        <p>To add new words go to the <a href="">Study with YouTube</a> or <a href="">Study with News Articles</a> sections</p>
    </div>
</body>
</html>