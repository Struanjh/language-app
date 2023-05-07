
<?php
use Google\Service\CloudSupport\ContentTypeInfo;
include_once 'db.php';

class UsersManager extends Db {
    
    protected function getUserRecord($email) {
        $conn = Db::connectDB();
        $sql = "SELECT id, email, password FROM users WHERE email = :email;";
        $statement = $conn->prepare($sql);
        $statement->execute(['email' => $email]);
        $result = $statement->fetch();
        return $result;
    }
    
    public static function getUserDetails() {
        $conn = Db::connectDB();
        $sql = "
            SELECT users.first_name, users.last_name, users.joined_on, COUNT(words.id) learned_words, COUNT(DISTINCT(words.news_id)) news_articles, COUNT(DISTINCT(words.youtube_id)) youtube_videos 
            FROM users 
            JOIN words ON words.user_id = users.id 
            WHERE users.id = :user_id;
        ";
        $statement = $conn->prepare($sql);
        $statement->execute(['user_id' =>  $_SESSION['user_id']]);
        $userDetails = $statement->fetchAll();
        return $userDetails[0];
    }

    protected static function recordLogin($id) {
        $conn = Db::connectDB();
        $sql = "UPDATE users 
        SET last_login = :last_login
        WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['last_login' => date("Y-m-d H:i:s"), 'id' => $id]);
    }
    

    public static function createUser($user) {
        $conn = DB::connectDB();
        $sql = "INSERT INTO users 
                (first_name, last_name, email, password, token, role,  oauth, joined_on, password_set, last_login)
                VALUES
                (:first_name, :last_name, :email,  :password, :token, :role, :oauth, :joined_on, :password_set, :last_login)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($user);
        return $conn->lastInsertId();
    }


    ////////------------------------------------------------------------------/////////
 

    public static function userExists($conn, $email) {
        $sql = "SELECT id, email FROM users WHERE email = :email;";
        $statement = $conn->prepare($sql);
        $statement->execute(['email' => $email]);
        $result = $statement->fetch();
        if($result) return $result;
    }


    public static function editUser() {

    }


    public static function deleteUser() {

    }

    // public static function validateNewUser() {
    //     $errors = [
    //         'firstname-error' => '',
    //         'lastname-error' => '',
    //         'email-error' => '',
    //         'password-error' => '',
    //         'confirm-password-error' => ''
    //     ];
    //     $submittedData = [
    //         'firstname' => '',
    //         'lastname' => '',
    //         'email' => '',
    //         'password' => '',
    //         'confirm-password' => ''
    //     ];
    //     //LOOP THROUGH POST DATA RECEIVED FROM CLIENT
    //     foreach ($_POST as $key => $value) {
    //         if(is_null($value)) {
    //             $errors[$key . '-error'] = 'cannot be left blank';
    //             continue;
    //         } else {
    //             //Value not null so trim and remove special chars to prevent XSS Attacks
    //             $value = trim(htmlspecialchars($value));
    //         }
    //         if($key == 'firstname' || $key == 'lastname') {
    //             if(!ctype_alpha($value)) {
    //                 $errors[$key . '-error'] = $key . 'cannot contain non-text characters';
    //                 continue;
    //             }
    //         }
    //         if($key == 'email') {
    //             if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
    //                 $errors[$key . '-error'] = 'Invalid email address';
    //                 continue;
    //             }
    //             $conn = DB::connectDB();
    //             $userExists = userExists($conn, $value);
    //             $conn = null;
    //             if($userExists) {
    //                 $errors[$key . '-error'] = 'A user with this email already exists';
    //                 continue;
    //             }  
    //         }
    //         if($key == 'password' || $key == 'confirm-password') {
    //             $regex = "/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{10,15}$/";
    //             if(!preg_match($regex, $value)) {
    //                 $errors[$key . '-error'] = "Must be 10-15 chars with a numeric & special char";
    //                 continue;
    //             }
    //         }
    //         //ALL VALIDATIONS PASSED FOR THAT FIELD - POPULATE SUBMITTED DATA
    //         $submittedData[$key] = $value;
    //     }
    //     //ONCE ALL FIELD CHECKS COMPLETE, CHECK PW'S ARE IDENTICAL
    //     if(($errors['password-error'] == '' && $errors['confirm-password-error'] == '') &&
    //         ($submittedData['password'] != $submittedData['confirm-password'])) {
    //             $errors[$key . 'error'] = "Passwords don't match";
    //         }
        
    //     //IF ANY ERRORS WERE FOUND, SEND BACK TO CLIENT AND END FUNCTION
    //     foreach ($errors as $val) {
    //         if($val != '') {
    //           return [
    //             'success' => false,
    //             'msg' => 'Server-side form validation failed',
    //             'errors' => $errors
    //           ];
    //         }
    //     }
    //     //Ensure user obj matches format of DB columns
    //     $submittedData['first_name'] = $submittedData['firstname'];
    //     $submittedData['last_name'] = $submittedData['lastname'];
    //     $submittedData['token'] = null;
    //     $submittedData['role'] = 'user';
    //     $submittedData['oauth'] = 0;
    //     $submittedData['joined_on'] = date("Y-m-d H:i:s");
    //     $submittedData['password'] = password_hash($submittedData['password'], PASSWORD_BCRYPT);
    //     $submittedData['password_set'] = date("Y-m-d H:i:s");
    //     $submittedData['last_login'] = null;
    //     unset($submittedData['firstname']);
    //     unset($submittedData['lastname']);
    //     unset($submittedData['confirm-password']);
    //     return createUser($submittedData);
    // }


    // public static function handleOAuthLogin($client) {
    //     //Google Auth Server returns Auth code in URL.. then get an Access token
    //     $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    //     $client->setAccessToken($token['access_token']);
    //     $google_oauth = new Google\Service\Oauth2($client);
    //     $google_account_info = $google_oauth->userinfo->get();
    //     $userinfo = [
    //         'email' => $google_account_info['email'] ?? null,
    //         'first_name' => $google_account_info['givenName'] ?? null,
    //         'last_name' => $google_account_info['familyName'] ?? null,
    //         'token' => $google_account_info['id'] ?? null,
    //         'role' => 'user',
    //         'oauth' => true,
    //         'password' => null,
    //         'password_set' => null
    //     ];
    //     $conn = DB::connectDB();
    //     if($conn) {
    //         $userDetails = userExists($conn, $userinfo['email']);
    //         if($userDetails) {
    //             $sql = "UPDATE users 
    //                     SET last_login = :last_login
    //                     WHERE id = :id";
    //             $stmt = $conn->prepare($sql);
    //             $stmt->execute(['last_login' => date("Y-m-d H:i:s"), 'id' => $userDetails['id']]);
    //             $_SESSION['user_id'] = $userDetails['id'];
    //         } else {
    //             $userinfo['joined_on'] = date("Y-m-d H:i:s");
    //             $userinfo['last_login'] = null;
    //             $createdUser  = createUser($userinfo);
    //             $_SESSION['user_id'] = $createdUser['user_id'];
    //         }
    //     }
    //     header('Location: '.$_SERVER['PHP_SELF']);
    //     die();
    // }

    // public static function logOutUser () {
    //     unset($_SESSION['user_id']);
    //     session_destroy();
    //     return ['status' => 'User session destroyed'];
    // }

}