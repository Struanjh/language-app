<?php

// require_once '../config.php';


require_once $_SERVER["DOCUMENT_ROOT"] . '/language-app/config.php';

session_start();

//NOTE: POST DATA MUST BE Arr of Arr format JS [{}] / PHP [[]]

//NOT USING PHP $_POST ARR BECAUSE IT REMOVES DUPLICATE KEY NAMES (multiple newWord Values passed from front-end)

$contentType = trim($_SERVER["CONTENT_TYPE"] ?? '');
if ($contentType !== "application/json")
  die(json_encode([
    'value' => 0,
    'error' => 'Content-Type is not set as "application/json"',
    'data' => null,
  ]));

$content = trim(file_get_contents("php://input"));
$data = json_decode($content, true);
print_r($data);

if($data['requestIdentifier'] == 'newWords') {
  $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
  if($conn) {
    if($data['contentType'] == 'news') {
      $contentId = createNewsArticle($conn, $data['contentDetails']);
    } else {
      $contentId = createYouTubeVideoEntry($conn, $data['contentDetails']);
    }
    $existingWords = getExistingWords($conn, $contentId, $data['contentType']);
    $updates = getWordsToUpdate($contentId, $data['words'], $existingWords);
    if(!empty($updates['wordsToAdd'])) addNewWords($conn, $updates['wordsToAdd'], $contentId, $data['contentType']);
    if(!empty($updates['wordsToDelete'])) deleteWords($conn, $updates['wordsToDelete'], $contentId, $data['contentType']);
    die(json_encode([
      'msg' => "DB updated"
    ])); 
  } else {
    die(json_encode([
                'msg' => "Error connecting to DB!"
    ]));  
  }
}
