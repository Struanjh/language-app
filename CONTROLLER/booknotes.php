
<?php

//Files should have same names as classes they contain 

//Not currently using properties at any point in classes. When pulling info about user (ie name, username).. could populate
//properties in the class with that info

//VISIBILITY OF MEMBERS:
//-- Public = accessible from anywhere
//--Protected = only accessible from within the object itself
//--GETTER: To allow the value of a protected property to be accessible generally add a GETTER method which simply returns 
// the value of the protected property
//-- SETTER: Function which allows the value of a protect property to be updated...

//--ABSTRACT CLASSES--//
//= Clases which are only accessed FROM another class by extending...
// ie. you can't create an objec instance of an abstract class
//--can create abstract methods within an abstract class which means other classes extending the 
//abstract must include those methods..//

//--ANONYMOUS CLASSES--//
//=NON-Reusable class..
//-- You create class and assign it to a variable.. but the class is destroyed immediately afterwards...
//-- So it's properties and methods are only accessible from the initial variable it was assigned to
//--Have to use new class keyword
// $newObj = new class {
//     pu blic function printMsg() {
//         echo "Hello world";
//     }
// }


//AUTOLOAD CLASSES -> Put that code in seperate file and include in index.php with route to classes...

spl_autoload_register('myAutoLoader');
function myAutoLoader($className) {
    $path = "pathToWhereClassLive";
    $ext = ".php";
    $fullPath = $path . $className . $ext;
    if(!file_exists($fullPath)) {
        return false;
    }
    include_once $fullPath;
}


//----SIGN UP----//

//FILE FORM DATA SENT TO SIGNUP PAGE
//if submit button set, store POST data in variables
//include the class files  and instantiate controller clas
//Call the signupUser method from the class instance
//Redirect user to front page
//header("Location: ../index.php?error=none")

//IN CONTROLLER CLASS
//Add constrcut function which takes varialbes from POST Array and assigns them to class properties
//Also add methods to perform all relevant validations (i.e. empty inputs, validEmails)
//Lastly inside controller add a signUpUser which has an if statement to check each validation in turn
//If a validation fails.. redirect user and exit script
//header("locationL ../index.php?error=email")
//exit()
//If all validations pass.. call setUser function in DB to add user

//--LOG IN---//
//in same class create loginUser() method
//inside that method, call getEmptyInput & getUser methods
 
//In Login(model) class
//create getUser function which selects user by supplied email and PW
//if user not found ($stmt-rowCount ==0) then header("location: index.php?error=userNotFound"); exit();



