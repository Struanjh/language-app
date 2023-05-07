<?php
require_once './CONTROLLER/contr.user.php';

class OauthLogIn extends User {

    private $googleOauthClient;
    private $authCode;
    private $accessToken;
    private $googleOauthInstance;
    private $googleUserInfo;
    private $dbUserInfo;

    
    function __construct($client) {
        $this->googleOauthClient = $client;
        $this->authCode = $_GET['code'];
        $this->getUserInfo();
        $this->addUser();
    }

    private function getUserInfo() {
        //$token = $client->fetchAccessToken($_GET['code])
        //
        //
        $this->accessToken =  $this->googleOauthClient->fetchAccessTokenWithAuthCode($this->authCode);
        $this->googleOauthClient->setAccessToken($this->accessToken['access_token']);
        $this->googleOauthInstance = new Google\Service\Oauth2($this->googleOauthClient);
        $this->googleUserInfo = $this->googleOauthInstance->userinfo->get();
        $this->dbUserInfo = [
            'email' => $this->googleUserInfo['email'] ?? null,
            'first_name' => $this->googleUserInfo['givenName'] ?? null,
            'last_name' => $this->googleUserInfo['familyName'] ?? null,
            'token' => $this->googleUserInfo['id'] ?? null,
            'role' => 'user',
            'oauth' => true,
            'password' => null,
            'password_set' => null
        ]; 
    }
    
    private function addUser() {
        $userExists = $this->getUserRecord($this->dbUserInfo['email']);
        if($userExists) {
            $this->recordLogin($userExists['id']);
            $_SESSION['user_id'] = $userExists['id'];
        } else {
            $this->dbUserInfo['joined_on'] = date("Y-m-d H:i:s");
            $this->dbUserInfo['last_login'] = null;
            $this->userID = $this->createUser($this->dbUserInfo);
            $_SESSION['user_id'] = $this->userID;
        }
        header("Location: index.php?action=");
        die();
    }
}