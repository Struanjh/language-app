<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./newsapi.js" defer></script>
    <title>News API Demo</title>
</head>
<body>
    <div class="loader-wrapper">
    </div>
    <!--Basic Form Structure to collect user input -->
    <form action="" id="news-article-form">
        <div class="form-field">
            <label for="search-terms">Search Terms</label>
            <input type="text" id="search-terms">
        </div>
        <div class="form-field">
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
       <div class="form-field">
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
    </form>
    <h4 class="errMsg"></h4>
    <!--Results from news API appended to this div using JS on front-end-->
    <div id="search-results"></div>
</body>
</html>