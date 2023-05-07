<?php

require_once './CONTROLLER/contr.user.php';

class RegisterUser extends User {

    private $registerUserErrors = [
        'firstname-error' => '',
        'lastname-error' => '',
        'email-error' => '',
        'password-error' => '',
        'confirm-password-error' => ''
    ];
    private $registerUserSubmittedData = [
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'password' => '',
        'confirm-password' => ''
    ];

    public function validateNewUser() {
        foreach ($_POST as $key => $value) {
            if($this->isValueNull($value)) {
                $this->registerUserErrors[$key . '-error'] = 'cannot be left blank';
                continue;
            }
            if($key == 'firstname' || $key == 'lastname') {
                if(!$this->textOnlyChars($value)) {
                    $this->registerUserErrors[$key . '-error'] = $key . 'cannot contain non-text characters';
                    continue;
                }
            }
            if($key == 'email') { 
                if(!$this->validEmail($value)) {
                    $this->registerUserErrors[$key . '-error'] = 'Invalid email address';
                    continue;
                }
                if($this->getUserRecord($key)) {
                    $this->registerUserErrors[$key . '-error'] = 'A user with this email already exists';
                    continue;
                }
            }
            if($key == 'password' || $key == 'confirm-password') {
                if(!$this->strongPw($value)) {
                    $this->registerUserErrors[$key . '-error'] = "Must be 10-15 chars with a numeric & special char";
                    continue;
                }
            }
            $this->registerUserSubmittedData[$key] = trim(htmlspecialchars($value));
        }
        if(!$this->pwMatch($this->registerUserSubmittedData['password'], $this->registerUserSubmittedData['confirm-password'])) {
            $this->registerUserErrors['confirm-password-error'] = "Passwords don't match";
        }
        $this->handleFormErrors();
        $this->userID = $this->createUser([
            'first_name' => $this->registerUserSubmittedData['firstname'],
            'last_name' => $this->registerUserSubmittedData['lastname'],
            'email' => $this->registerUserSubmittedData['email'],
            'password' => password_hash($this->registerUserSubmittedData['password'], PASSWORD_BCRYPT),
            'token' => null,
            'role' => 'user',
            'oauth' => 0,
            'joined_on' => date("Y-m-d H:i:s"),
            'password_set' => date("Y-m-d H:i:s"),
            'last_login' => null
        ]);
        die(
            json_encode(
                [
                    'success' => true,
                    'msg' => 'User successfully created',
                    'user_id' => $this->userID
                ]
            )
        );
    }

    private function handleFormErrors() {
        foreach ($this->registerUserErrors as $val) {
            if($val != '') {
                die(
                    json_encode(
                        [ 
                            'success' => false,
                            'msg' => 'Server-side form validation failed',
                            'errors' => $this->registerUserErrors
                        ]
                    )
                );
            }
        }
    }
   
}
