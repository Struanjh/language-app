<?php

require_once 'model/userManager.php';

class User extends UsersManager {

    protected string $userID;
    protected string $email;
    protected string $first_name;
    protected string $last_name;
    protected string $token;
    protected string $pw;
    protected string $pwHash;
    protected string $password_set;
    protected $last_login;
    protected string $role;
    protected int $oauth;
    protected $joined_on;
    protected int $learned_words;
    protected int $news_articles;
    protected int $youtube_videos; 
    protected static $pwRegex = "/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{10,15}$/";


    public function getProperty($property){
        return $this->$property;
     }

    public function isUserLoggedIn($client) {
        if(isset($_SESSION['user_id'])) {
            $userDetails = $this->getUserDetails();
            $this->setUserDetails($userDetails);
            require_once './VIEW/home.php';
        } else {
            require_once './VIEW/login.php';
        }
    }
    
    public function logOutUser () {
        unset($_SESSION['user_id']);
        session_destroy();
        die(json_encode(['status' => 'User session destroyed']));
    }

    protected function setUserDetails($user) {
        $this->first_name = $user['first_name'];
        $this->last_name = $user['last_name'];
        $this->joined_on = strtotime($user['joined_on']);
        $this->learned_words = $user['learned_words'];
        $this->news_articles = $user['news_articles'];
        $this->youtube_videos = $user['youtube_videos'];
    }


    protected function isValueNull ($value) {
        if(is_null($value)) {
            return true;
        } else {
            return false;
        }
    }

    protected function textOnlyChars($value) {
        if(!ctype_alpha($value)) {
            return false;
        } else {
            return true;
        }
    } 

    protected function strongPw($value) {
        if(!preg_match(self::$pwRegex, $value)) {
            return false;
        } else {
            return true;
        }
    }

    protected function pwMatch($pw, $confirmPW) {
        if(($pw != '' && $confirmPW != '') && ($pw != $confirmPW)) {
            return false;
        } else {
            return true;
        }
    }

    protected function validEmail ($value) {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }
}