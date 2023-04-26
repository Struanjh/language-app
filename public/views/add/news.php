<?php 
    //WANT TO AVOID THIS..
    // require_once  '../../../config.php';

    //Flawed because it relies on 
    require_once $_SERVER["DOCUMENT_ROOT"] . '/language-app/config.php';

    session_start();

    if(!isset($_SESSION['user_id'])) {
        logOutUser();
        header("Location: /language-app/public/views/login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../scripts/news.js" defer type="module"></script>
    <script src="../../scripts/translation.js" defer type="module"></script>
    <script src="../../scripts/header.js" type="module" defer></script>
    <script src="../../scripts/functions.js" type="module" defer></script>
    <script src="https://kit.fontawesome.com/4bdea9f4d2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/translate-tool.css">
    <link rel="stylesheet" href="../../styles/news.css">
    <title>Add News Content</title>
</head>
<body>
    <?php require_once '../../includes/header.php' ?>
    <div class="focus-article grid">
        <?php require_once '../../includes/translation_tool.php' ?>
    </div>
    <div class="news-container">
        <!--Basic Form Structure to collect user input -->
        <form class="flex" id="news-article-form">
            <h3 id="news-instructions-heading">
            Use the form fields below to find news articles to study written in the language you are learning
            </h3>
            <div class="form-field flex">
                <label for="search-terms">Search Terms</label>
                <input type="text" id="search-terms">
            </div>
            <div class="form-field flex">
                <label for="search-topic">News Topic</label>
                <select name="search-topic" id="search-topic">
                    <option value="" selected disabled>--Select--</option>
                    <option value="news">News</option>
                    <option value="sport">Sport</option>
                    <option value="tech">Tech</option>
                    <option value="world">World</option>
                    <option value="finance">Finance</option>
                    <option value="politics">Politics</option>
                    <option value="business">Business</option>
                    <option value="economics">Entertainment</option>
                </select>
            </div>
            <div class="form-field flex">
                <label for="search-lang">Search Language</label>
                    <select name="search-lang" id="search-lang">
                        <option value="" selected disabled>--Select--</option>
                        <option value="en">English</option>
                        <option value="ja">Japanese</option>
                        <option value="ko">Korean</option>
                        <option value="cn">Chinese</option>
                        <option value="es">Spanish</option>
                        <option value="de">German</option>
                        <option value="fr">French</option>
                    </select>
            </div>
            <button type="button" id="news-search-submit">Search</button>
            <h4 class="errMsg"></h4>
        </form>
        <!--Results from news API appended to this div using JS on front-end-->
        <div id="search-results" class="grid"></div>
    </div>
</body>
</html>