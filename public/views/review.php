<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/language-app/config.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    logOutUser();
    header("Location: /language-app/public/views/login.php");
}
if(isset($_POST['deleteContent'])) {
   die(
    json_encode(deleteContent($_POST['contentId'], $_POST['contentType']))
   );
}
$conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
$userNewsArticles = getUserNewsArticles($conn);
$userYouTubeVideos = getUserYouTubeVideos($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/review.css">
    <script src="../scripts/header.js" type="module" defer></script>
    <script src="../scripts/functions.js" type="module" defer></script>
    <script src="../scripts/review.js" type="module" defer></script>
    <title>Review Content</title>
</head>
<body>
    <?php require_once '../includes/header.php'; ?> 
    <button id="newsArticles" type="button" class="collapsible">Your News Articles</button>
    <ul  class="content" data-content-type="news">
    <?php foreach ($userNewsArticles as $article) {?>
        <li content-id="<?=$article['id']?>"><?=$article['title']?><button class="delete-content">DELETE</button></li>
    <?php } ?>
    </ul>
    <button id="youTubeVideos" type="button" class="collapsible">Your YouTube Videos</button>
    <ul class="content" data-content-type="youtube">
    <?php foreach ($userYouTubeVideos as $video) {?>
        <li content-id="<?=$video['id']?>"><?=$video['title']?><button class="delete-content">DELETE</button></li>
    <?php } ?>
    </ul>
</body>
</html>