<?php

use Google\Service\CloudSupport\ContentTypeInfo;

function createDbConn($host, $dbname, $username, $password) {
    // Create connection
    try {
        $dbConn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        return $dbConn;
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        return false;
    }  
}

//----------------------------AUTH------------------------------//
function handleOAuthLogin($client) {
    //Google Auth Server returns Auth code in URL.. then get an Access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $userinfo = [
        'email' => $google_account_info['email'] ?? null,
        'first_name' => $google_account_info['givenName'] ?? null,
        'last_name' => $google_account_info['familyName'] ?? null,
        'token' => $google_account_info['id'] ?? null,
        'role' => 'user',
        'oauth' => true,
        'password' => null,
        'password_set' => null
    ];
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
    if($conn) {
        $userDetails = userExists($conn, $userinfo['email']);
        if($userDetails) {
            $sql = "UPDATE users 
                    SET last_login = :last_login
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['last_login' => date("Y-m-d H:i:s"), 'id' => $userDetails['id']]);
            $_SESSION['user_id'] = $userDetails['id'];
        } else {
            $userinfo['joined_on'] = date("Y-m-d H:i:s");
            $userinfo['last_login'] = null;
            $createdUser  = createUser($userinfo);
            $_SESSION['user_id'] = $createdUser['user_id'];
        }
    }
    header('Location: '.$_SERVER['PHP_SELF']);
    die();
}

//----------------------------USERS------------------------------//

function userExists($conn, $email) {
    $sql = "SELECT id, email FROM users WHERE email = :email;";
    $statement = $conn->prepare($sql);
    $statement->execute(['email' => $email]);
    $result = $statement->fetch();
    if($result) return $result;
}

function validateNewUser() {
    $errors = [
        'firstname-error' => '',
        'lastname-error' => '',
        'email-error' => '',
        'password-error' => '',
        'confirm-password-error' => ''
    ];
    $submittedData = [
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'password' => '',
        'confirm-password' => ''
    ];
    //LOOP THROUGH POST DATA RECEIVED FROM CLIENT
    foreach ($_POST as $key => $value) {
        if(is_null($value)) {
            $errors[$key . '-error'] = 'cannot be left blank';
            continue;
        } else {
            //Value not null so trim and remove special chars to prevent XSS Attacks
            $value = trim(htmlspecialchars($value));
        }
        if($key == 'firstname' || $key == 'lastname') {
            if(!ctype_alpha($value)) {
                $errors[$key . '-error'] = $key . 'cannot contain non-text characters';
                continue;
            }
        }
        if($key == 'email') {
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$key . '-error'] = 'Invalid email address';
                continue;
            }
            $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
            if(!$conn) {
                return [
                    'success' => false,
                    'msg' => 'Connection to DB failed'
                ];
            }
            $userExists = userExists($conn, $value);
            $conn = null;
            if($userExists) {
                $errors[$key . '-error'] = 'A user with this email already exists';
                continue;
            }  
        }
        if($key == 'password' || $key == 'confirm-password') {
            $regex = "/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{10,15}$/";
            if(!preg_match($regex, $value)) {
                $errors[$key . '-error'] = "Must be 10-15 chars with a numeric & special char";
                continue;
            }
        }
        //ALL VALIDATIONS PASSED FOR THAT FIELD - POPULATE SUBMITTED DATA
        $submittedData[$key] = $value;
    }
    //ONCE ALL FIELD CHECKS COMPLETE, CHECK PW'S ARE IDENTICAL
    if(($errors['password-error'] == '' && $errors['confirm-password-error'] == '') &&
        ($submittedData['password'] != $submittedData['confirm-password'])) {
            $errors[$key . 'error'] = "Passwords don't match";
        }
    
    //IF ANY ERRORS WERE FOUND, SEND BACK TO CLIENT AND END FUNCTION
    foreach ($errors as $val) {
        if($val != '') {
          return [
            'success' => false,
            'msg' => 'Server-side form validation failed',
            'errors' => $errors
          ];
        }
    }
    //Ensure user obj matches format of DB columns
    $submittedData['first_name'] = $submittedData['firstname'];
    $submittedData['last_name'] = $submittedData['lastname'];
    $submittedData['token'] = null;
    $submittedData['role'] = 'user';
    $submittedData['oauth'] = 0;
    $submittedData['joined_on'] = date("Y-m-d H:i:s");
    $submittedData['password'] = password_hash($submittedData['password'], PASSWORD_BCRYPT);
    $submittedData['password_set'] = date("Y-m-d H:i:s");
    $submittedData['last_login'] = null;
    unset($submittedData['firstname']);
    unset($submittedData['lastname']);
    unset($submittedData['confirm-password']);
    return createUser($submittedData);
}

function createUser($user) {
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD); 
    if(!$conn) {
        return [
            'success' => false,
            'msg' => 'Connection to DB failed'
        ];
    };
    $sql = "INSERT INTO users 
            (first_name, last_name, email, password, token, role,  oauth, joined_on, password_set, last_login)
            VALUES
            (:first_name, :last_name, :email,  :password, :token, :role, :oauth, :joined_on, :password_set, :last_login)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($user);
    $id = $conn->lastInsertId();
    return [
        'success' => true,
        'msg' => 'User successfully created',
        'user_id' => $id
    ];
}

//Get User Info
function readUser() {
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD); 
    if($conn) {
        $sql = "
            SELECT users.first_name, users.last_name, users.joined_on, COUNT(words.id) learned_words, COUNT(DISTINCT(words.news_id)) news_articles, COUNT(DISTINCT(words.youtube_id)) youtube_videos 
            FROM users 
            JOIN words ON words.user_id = users.id 
            WHERE users.id = :user_id;
        ";
        $statement = $conn->prepare($sql);
        $statement->execute(['user_id' =>  $_SESSION['user_id']]);
        return $statement->fetchAll();
    } else {
        return 'Connection to DB failed';
    }
}

function editUser() {

}


function deleteUser() {

}



function authenticateUser () {
    $email = trim(strtolower(htmlspecialchars($_POST['email'])));
    $pw = trim(htmlspecialchars($_POST['password']));
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
    if($conn) {
        $sql = "SELECT id, email, password FROM users WHERE email = :email;";
        $statement = $conn->prepare($sql);
        $statement->execute(['email' => $email]);
        $result = $statement->fetch();
        if($result) {
            //EMAIL FOUND
            if(password_verify($pw, $result['password'])) {
                //PW MATCHES HASH-> USER AUTHENTICATED
                //UPDATE LOGIN TIME
                $sql = "UPDATE users 
                        SET last_login = :last_login
                        WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['last_login' => date("Y-m-d H:i:s"), 'email' => $email]);
                $conn = null;
                //CREATE SESSION
                $_SESSION['user_id'] = $result['id'];
                return ['success' => true, 'details' => 'Session Created'];
            } else {
                $conn = null;
                return ['success' => false, 'details' => 'Incorrect password'];
            }
        } else {
            return ['success' => false, 'details' => 'Email not found'];
        }
    } else {
        $conn = null;
        return ['success' => false, 'details' => 'Error connecting to DB'];
    }
}

function logOutUser () {
    unset($_SESSION['user_id']);
    session_destroy();
    return ['status' => 'User session destroyed'];
}


//----------------------------LANGUAGE CONTENT..------------------------------//

function deleteContent ($contentId, $contentType) {
    $conn = createDbConn(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD); 
    if(!$conn) {
        return [
            'success' => false,
            'msg' => 'Connection to DB failed'
        ];
    };
    if($contentType == "news") {
        $sql = "DELETE FROM news WHERE id = :id;";
    } else {
        $sql = "DELETE FROM youtube_id WHERE id = :id;";
    }
    $statement = $conn->prepare($sql);
    $statement->execute(['id' => $contentId]);
    return [ 'success' => true ];
}

//Function creates the article if it doesn't exist yet.... & RETURNS ARTICLE ID
function createNewsArticle($conn, $article) {
    $sql = "SELECT id 
            FROM news 
            WHERE user_id = :user_id
            AND title = :title
            AND author = :author;";
    $statement = $conn->prepare($sql);
    $statement->execute([
        'user_id' => $_SESSION['user_id'],
        'title' => trim(htmlspecialchars($article['title'])),
        'author' => trim(htmlspecialchars($article['author']))
    ]);
    $result = $statement->fetch();
    if($result) return $result['id'];
    //Article doesn't exist in DB yet so create it
    $p_date = strtotime($article['published_date']);
    $p_date = date('Y-m-d');
    $sql = "INSERT INTO news 
    (user_id, title, author, article_url, country, language, topic, published_date)
    VALUES
    (:user_id, :title, :author, :article_url, :country, :language, :topic, :published_date);";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'title' => trim(htmlspecialchars($article['title'])),
        'author' => trim(htmlspecialchars($article['author'])),
        'article_url' => trim(htmlspecialchars($article['article_url'])),
        'country' => trim(htmlspecialchars($article['country'])),
        'language' => trim(htmlspecialchars($article['language'])),
        'topic' => trim(htmlspecialchars($article['topic'])),
        'published_date' => $p_date
    ]);
    //FETCH ID OF THE ARTICLE THAT WAS JUST INSERTED.....
    $stmt = $conn->query("SELECT LAST_INSERT_ID()");
    $id = $stmt->fetchColumn();
    return $id;
}

//Function creates the article if it doesn't exist yet.... & RETURNS ARTICLE ID
function createYouTubeVideoEntry($conn, $video) {
    $sql = "SELECT id 
        FROM youtube_videos 
        WHERE user_id = :user_id
        AND youtube_id = :youtube_id;";
    $statement = $conn->prepare($sql);
    $statement->execute([
    'user_id' => $_SESSION['user_id'],
    'youtube_id' => trim(htmlspecialchars($video['video-id']))
    ]);
    $result = $statement->fetch();
    if($result) return $result['id'];
    //ELSE CREATE ENTRY IN DB
    $sql = "INSERT INTO youtube_videos
    (user_id, youtube_id , title, upload_date, duration, url)
    VALUES
    (:user_id, :youtube_id , :title, :upload_date, :duration, :url);";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'youtube_id' => trim(htmlspecialchars($video['video-id'])),
    'title' => trim(htmlspecialchars($video['video-title'])),
    'upload_date' => trim(htmlspecialchars($video['video-upload-date'])),
    'duration' => trim(htmlspecialchars($video['video-duration'])),
    'url' => trim(htmlspecialchars($video['video-url']))
    ]);
    //FETCH ID OF THE ARTICLE THAT WAS JUST INSERTED.....
    $stmt = $conn->query("SELECT LAST_INSERT_ID()");
    $id = $stmt->fetchColumn();
    return $id;
}

function getWordsToUpdate($articleId, $newWords, $existingWords) {
    //FOR EACH NEW WORD, CHECK IDENTICAL ENTRY FOR USER DOESN'T ALREADY EXIST IN DB
    $wordsToAdd = [];
    for($i=0; $i<count($newWords); $i++) {
        $addWord = true;
        for($j=0; $j<count($existingWords); $j++) {
            $filteredDbEntry = array_filter(
                $existingWords[$j],
                fn ($key) => in_array($key, ['src_lang', 'targ_lang', 'src_lang_content', 'targ_lang_content']),
                ARRAY_FILTER_USE_KEY
            );
            if(!array_diff($newWords[$i], $filteredDbEntry)) {
                //Identical Entry found in DB
                $addWord = false;
                break;
            }
        }
        //UI Entry found in DB Arr.. store the entry to be added to DB later..
        if($addWord) array_push($wordsToAdd, $newWords[$i]);
    }

    //FOR EACH WORD IN DB, CHECK AN IDENTICAL WORD WAS SENT FROM UI
    $wordsToDelete = [];
    for($i=0; $i<count($existingWords); $i++) {
        $deleteWord = true;
        $filteredDbEntry = array_filter(
            $existingWords[$i],
            fn ($key) => in_array($key, ['src_lang', 'targ_lang', 'src_lang_content', 'targ_lang_content']),
            ARRAY_FILTER_USE_KEY
        );
        for($j=0; $j<count($newWords); $j++) {
            if(!array_diff($filteredDbEntry, $newWords[$j])) {
                 //DB Entry found in UI arr
                 $deleteWord = false;
                 break;
            }
        }
        //Entry not found in UI Arr.. add to delete arr to remove from DB
        if($deleteWord) array_push($wordsToDelete, $existingWords[$i]);
    }
    return ['wordsToAdd' => $wordsToAdd, 'wordsToDelete' => $wordsToDelete];
 }

//Content type should be news_id, OR youtube_id
function getExistingWords($conn, $contentId, $contentType) {
    if($contentType == 'news') {
        $stmt = $conn->prepare("SELECT * FROM words WHERE news_id = :news_id AND user_id = :user_id");
        $stmt->execute(['news_id' => $contentId, 'user_id' => $_SESSION['user_id']]); 
    } else {
        $stmt = $conn->prepare("SELECT * FROM words WHERE youtube_id = :youtube_id AND user_id = :user_id");
        $stmt->execute(['youtube_id' => $contentId, 'user_id' => $_SESSION['user_id']]); 
    }
    $result = $stmt->fetchAll();
    return $result;
    }


    function addNewWords($conn, $newWords, $contentId, $contentType) {
        $sql = "INSERT INTO words
        (user_id, news_id, youtube_id, src_lang, targ_lang, src_lang_content, targ_lang_content, added_date, familiarity)
        VALUES
        (:user_id, :news_id, :youtube_id, :src_lang, :targ_lang, :src_lang_content, :targ_lang_content, :added_date, :familiarity)";
        $statement = $conn->prepare($sql);
        foreach($newWords as $row) {
            $statement->execute(
                [
                    'user_id' => $_SESSION['user_id'],
                    'news_id' =>  $contentType == 'news' ? $contentId : null,
                    'youtube_id' => $contentType == 'youtube' ? $contentId : null,
                    'src_lang' => trim($row['src_lang']),
                    'targ_lang' => trim($row['targ_lang']),
                    'src_lang_content' => $row['src_lang_content'],
                    'targ_lang_content' => $row['targ_lang_content'],
                    'added_date' => date("Y-m-d H:i:s"),
                    'familiarity' => 'new'
                ]
            ); 
        }
    }

    function deleteWords($conn, $wordsToDelete, $contentId, $contentType) {
        if($contentType == 'news') {
            $sql = "DELETE FROM words WHERE id = :id AND user_id = :user_id AND news_id = :news_id ";
            $stmt= $conn->prepare($sql);
            foreach($wordsToDelete as $row) {
                $stmt->execute(
                    [
                        'id' => $row['id'],
                        'user_id' => $_SESSION['user_id'],
                        'news_id' => $contentId,
                    ]
                ); 
            }
        } else {
            $sql = "DELETE FROM words WHERE id = :id AND user_id = :user_id AND youtube_id = :youtube_id ";
            $stmt= $conn->prepare($sql);
            foreach($wordsToDelete as $row) {
                $stmt->execute(
                    [
                        'id' => $row['id'],
                        'user_id' => $_SESSION['user_id'],
                        'youtube_id' => $contentId,
                    ]
                ); 
            }
        }
    }

    function getUserYouTubeVideos($conn) {
        $sql = '
            SELECT * FROM youtube_videos WHERE user_id = :user_id;
        ';
        if($conn) {
            $statement = $conn->prepare($sql);
            $statement->execute(['user_id' =>  $_SESSION['user_id']]);
            return $statement->fetchAll();
        } else {
            return json_encode(
                [
                    'success' => false,
                    'msg' => 'Connection to DB failed'
                ]
            );
        }
    }

    function getUserNewsArticles($conn) {
        $sql = '
            SELECT * FROM news WHERE user_id = :user_id;
        ';
        if($conn) {
            $statement = $conn->prepare($sql);
            $statement->execute(['user_id' =>  $_SESSION['user_id']]);
            return $statement->fetchAll();

        } else {
            return json_encode(
                [
                    'success' => false,
                    'msg' => 'Connection to DB failed'
                ]
            );
        }
    }