<?php
require_once '../../../config.php';
use Goutte\Client;
$availableLang =  [];

//Click to open options
// <div class="yt-spec-touch-feedback-shape__fill" style=""></div>
//Click to show Transcript box
// <tp-yt-paper-item class="style-scope ytd-menu-service-item-renderer" style-target="host" role="option" tabindex="0" aria-disabled="false"><!--css-build:shady-->
// </tp-yt-paper-item>


//---------------------GET VIDEO ID FROM FRONT-END, SCAPRE AVAILABLE LANGUAGES IF PRESENT-----------//
if(isset($_POST['videoID'])) {
    $client = new Client();
    $crawler = $client->request("GET", "https://www.captionsgrabber.com/8302/get-captions.00.php?id=" . $_POST['videoID']);
    $crawler->filter('div.col-md-4 > a')->each(function ($node) {
        $lang['lang'] = $node->text();
        $lang['link'] = $node->attr('href');
        array_push($GLOBALS['availableLang'], $lang);
    }); 
    //---------------------IF ONLY 1 LANG AVAILABLE, RESULT WILL BE IMMEDIATELY CONTAINED IN THIS DIV-----------//
    // $result = isset($crawler->filter('div#text')->text()) ? $crawler->filter('div#text')->text() : false;
    if(count($GLOBALS['availableLang']) != 0) {
        //MULTIPLE LANGUAGES AVAILABLE - RETURN RESULT TO FRONT-END SO USER CAN CHOOSE LANGUAGE
        //EXAMPLE: https://www.captionsgrabber.com/8302/get-captions.00.php?id=cE9qdEBkMPE
         echo json_encode($GLOBALS['availableLang']); 
    } else if ($crawler->filter('div#text')) {
        //CAPTIONS WERE IMMEDIATELY AVAILABLE SO RETURN THEM.. No need to return language choice
        //EXAMPLE - https://www.captionsgrabber.com/8302/get-captions.00.php?id=Oive66jrwBs
        $result = $crawler->filter('div#text')->text();
        echo json_encode(['status' => 'immediate result', 'result' => $result]);
    } else {
        //NO RESULTS AVAILABLE
        echo json_encode(['status' => 'No transcripts found for this video!']);
    }
//---------------------ELIF GET CAPTIONS URL FROM FRONT-END & SCRAPE THE CAPTIONS-----------//
} elseif(isset($_POST['captionsURL'])) {
    $client = new Client();
    $crawler = $client->request("GET", $_POST['captionsURL']);
    $result = $crawler->filter('div#text')->text();
    echo json_encode($result);
//--------------------ELSE DISPLAY DEFAULT SEARCH FORM-------------------------------------//
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../styles/header.css">
    <link rel="stylesheet" href="../../styles/translate-tool.css">
    <link rel="stylesheet" href="../../styles/youtube.css">
    <script src="../../scripts/header.js" type="module" defer></script>
    <script src="../../scripts/functions.js" type="module" defer></script>
    <script src="../../scripts/translation.js" defer type="module"></script>
    <script src="https://kit.fontawesome.com/4bdea9f4d2.js" crossorigin="anonymous" defer></script>
    <script src="../../scripts/youtube.js" type="module" defer></script>
    <title>Youtube</title>  
</head>
<body>
    <?php require_once '../../includes/header.php' ?>
    <div class="focus-article grid">
        <?php  require_once '../../includes/translation_tool.php' ?>
    </div>
    <div class="form-container">
        <form action="" method="get">
            <input type="text" name="video-url" id="video-url" placeholder="Enter a youtube video URL">
            <p class="errorMsg"></p>
            <button id="video-submit" type="button">Submit</button>
        </form>
        <div class="search-results">
        </div>
    </div>
    <!-- <div id="video-container"></div>
    <div id="results-container"> -->
    </div>
</body>
</html>

<?php } ?>