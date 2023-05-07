<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/styles/index.css">
    <link rel="stylesheet" href="public/styles/header.css">
    <script src="public/scripts/header.js" type="module" defer></script>
    <script src="public/scripts/functions.js" type="module" defer></script>
    <title>Document</title>
</head>
<body>
    <?php 
        require_once 'public/includes/header.php'; 
    ?>
    <div id="user-welcome-message">
        <h4>Welcome <?=$this->getProperty('first_name')?> <?=$this->getProperty('last_name')?></h4>
        <p>You joined Language Up on <?=date('l jS F Y', $this->getProperty('joined_on'));?></p>
        <p>You've been a member for <?=intval((time() - $this->getProperty('joined_on')) / 60 / 60 / 24);?> days</p>
        <p>You've learned <?=$this->getProperty('learned_words')?> words from <?=$this->getProperty('youtube_videos')?> YouTube videos and <?=$this->getProperty('news_articles')?> News Articles</p>
        <p>To review the words you've already learned, go to the <a href="">Review Content</a> section</p>
        <p>To add new words go to the <a href="">Study with YouTube</a> or <a href="">Study with News Articles</a> sections</p>
    </div>
</body>
</html>