import * as functions from './functions.js';

const pageContainer = document.querySelector('div.page-container');
const registerFormContainer = document.querySelector('div.form-container.register-form');
const loginFormContainer = document.querySelector('div.form-container.login-form');
const registerForm = document.getElementById('register-form');
const loginForm = document.getElementById('login-form');
const showRegisterClick = document.getElementById('show-register-click');
const showLoginClick = document.getElementById('show-login-click');
const registerSubmit = document.getElementById('register-submit');
const loginSubmit = document.getElementById('login-submit');
const errorMsgs = document.querySelectorAll('#register-form > div.error')
const registerInputs = document.querySelectorAll('form#register-form > input');
const loginInputs = document.querySelectorAll('form#login-form > input')
const regStatusMsg = document.querySelector('#register-form .status-msg');
const logInStatusMsg = document.querySelector('#login-form .status-msg');
const formCloseBtn = document.querySelectorAll('span.close');

console.log(registerSubmit);
console.log(loginSubmit);

showRegisterClick.addEventListener('click', () => {
    registerFormContainer.style.display = 'block';
    registerForm.style.display = 'flex';
    pageContainer.classList.add('is-blurred');
});

showLoginClick.addEventListener('click', () => {
    loginFormContainer.style.display = 'block';
    loginForm.style.display = 'flex';
    pageContainer.classList.add('is-blurred');
});

//Handle Form Close Click
formCloseBtn.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        let form = e.target.parentNode;
        let formContainer = e.target.parentNode.parentNode
        if(formContainer.classList.contains('register-form')) {
            regStatusMsg.textContent = '';
            errorMsgs.forEach(element => {
                element.textContent = '';
            });
        } else {
            logInStatusMsg.textContent = '';
        }
        form.style.display = 'none';
        formContainer.style.display = 'none';
        pageContainer.classList.remove('is-blurred');
        form.reset();
    })
})

const handleLogin = async () => {
    let data = {};
    for(const val of loginInputs.values()) {
        if(!val.value) {
            logInStatusMsg.textContent = 'Provide both email and password to log in';
            return;
        } else {
            data[val.type] = val.value;
        } 
    }
    let request = functions.prepareRequest(
        'http://localhost/language-app/index.php?action=login',
        null, 
        [data],
        {method: 'POST'} 
    );
    console.log(request.url);
    console.log(request.options);
    let res = await functions.makeRequest(request.url, request.options);
    if(res.success) {
        window.location.href = "index.php?action=";
    } else {
        logInStatusMsg.textContent = res.details;
    }
}

const handleRegistration = async () => {
    let formData = {};
    let formDataErr = {};
    for (let i=0; i<registerInputs.length; i++) {
        let fieldId = registerInputs[i].getAttribute('id');
        let fieldErrId = `${fieldId}-error`;
        let fieldVal = registerInputs[i].value;
        //VAILDATE NOT NULL - APPLIED TO ALL FIELDS
        if(fieldVal == '') {
            formDataErr[fieldErrId] = `${fieldId} cannot be left blank`;
            continue;
        }
        //TEXT ONLY VALIDATOR
        if(fieldId == 'firstname' || fieldId == 'lastname') {
            if(!validateTextOnly(fieldVal)) {
                formDataErr[fieldErrId] = `${fieldId} cannot contain non-text characters`;
                continue;
            }
        }
        //EMAIL VALIDATOR
        if(fieldId == 'email') {
            if(!validateEmail(fieldVal)) {
                formDataErr[fieldErrId] = `Invalid email address`;
                continue;
            }
        }
        //PW VALIDATOR
        if(fieldId == 'password' || fieldId == 'confirm-password') {
            if(!validatePw(fieldVal)) {
                formDataErr[fieldErrId] = `Must be 10-15 chars with a numeric & special char`;
                continue;
            }   
        }
        //ALL VALIDATION STEPS PASSED SO POPULATE THE DATA OBJ
        formData[fieldId] = fieldVal;
    }
    // IF NO EXISTING PW ERRORS, CHECK PW'S ARE IDENTICAL
    if((!('password-error' in formDataErr) && !('confirm-password-error' in formDataErr)) &&
        (formData['password'] !== formData['confirm-password'])) {
            formDataErr['password-error'] =  formDataErr['confirm-password-error'] = `Passwords don't match`;
    }
    //IF ERRORS EXIST, POPULATE ERROR SPANS
    if(Object.keys(formDataErr).length !== 0) {
        populateFormErrors(formDataErr);
        regStatusMsg.textContent = 'Client side form validation failed';
    } else {
         //FIRST CLEAR EXISTING ERRORS
        for (const value of errorMsgs.values()) {
            value.textContent = '';
        }
        let request = functions.prepareRequest(
            'index.php?action=newUser', 
            null, 
            [formData], 
            {method: 'POST'}
        );
        let res = await functions.makeRequest(request.url, request.options);
        console.log(res);
        if(!res.success)  {
            populateFormErrors(res.errors);
        } else {
            //CLEAR EXISTING INPUTS
            for (const value of registerInputs.values()) {
                value.value = '';
            }
        }
        regStatusMsg.textContent = res.msg;
    }
};

const populateFormErrors = (errors) => {
    //FIRST CLEAR EXISTING ERRORS
    for (const value of errorMsgs.values()) {
        value.textContent = '';
    }
    //FOR EACH ERROR - POPULATE THE RELEVANT ERROR DIV WITH THE SAVED MSG
    for(let err in errors) {
        let errInput = document.getElementById(err);
        errInput.textContent = errors[err];
    }
}


const validateTextOnly = (str) => {
    let regex = /^[A-Za-z]+$/;
    if(str.match(regex)) {
        return true;
    } else {
        return false;
    }
};

const validateEmail = (email) => {
    let regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    if(email.match(regex)) {
        return true;
    } else {
        return false;
    }
};

const validatePw = (pw) => {
    let regex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{10,15}$/;
    if(pw.match(regex)) {
        return true;
    } else {
        return false;
    }
}

registerSubmit.addEventListener('click', handleRegistration);
loginSubmit.addEventListener('click', handleLogin);
