<?php

require_once './CONTROLLER/contr.user.php';

class LoginUser extends User {

    public function authenticateUser () {
        $this->email = trim(htmlspecialchars($_POST['email']));
        $this->pw = trim(htmlspecialchars($_POST['password']));
        $result = $this->getUserRecord($this->email);
        if($result) {
            if(password_verify($this->pw, $result['password'])) {
                $this->recordLogin($result['id']);
                $_SESSION['user_id'] = $result['id'];
                die(json_encode(['success' => true, 'details' => 'Incorrect password']));
            } else {
                die(json_encode(['success' => false, 'details' => 'Incorrect password']));
            }
        } else {
            die(json_encode(['success' => false, 'details' => 'Email not found']));
        }
    }
}