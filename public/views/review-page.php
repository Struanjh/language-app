<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/language-app/config.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    logOutUser();
    header("Location: /language-app/public/views/login.php");
}

//Send a GET REQ TO LOAD PAGE...
if(!isset($_GET['content_id'])) {
    header("Location: /language-app/public/views/review.php");
} else {
    //Fetch the data and load the game......
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD); 
    $gameData = getExistingWords($conn, $_GET['content_id'], $_GET['content_type']);
}
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
    <script src="../scripts/review-page.js" type="module" defer></script>
    <title>Review Page</title>
    <script>
    const gameData = <?php echo(json_encode($gameData))?>;
</script>
</head>
<body>
    <?php require_once '../includes/header.php';?>
    <div class="game-container">
        <h2>Review the words</h2>
        <div id="target-word"></div>
        <div id="answer-container">
            <input type="text" id="user-answer">
        </div>
        <div id="buttons-container">
            <button id="submit-answer">Submit</button>
        </div>
        <div id="problem-words">
        </div>
        <button id="hint">Get a hint</button>
    </div>
</body>
</html>