<?php
require_once ("../../server/classes/session.class.php");

$session = Session::getInstance();
$logInBody = null;

//Cheap hack so the user doesn't see the log in form if they enter the logIn into the URL when they are logged in
if($session->getProperty("username") && $session->getProperty("email"))
{
    $username = $session->getProperty("username");
    $logInBody = <<< BODY
    <div id="login-form-inner">
        <h2>Sign Out</h2>
        <p>Welcome $username</p>
        <p>Press the log out button when done to log out.</p>
        <button data-ng-click="logOut()">Log Out</button>
    </div>
BODY;
}
else
{
    //not logged in
    $logInBody = <<< BODY
<div id="login-form-inner">
    <h2>Log In</h2>
    <p>Log into the app to make changes.</p>
    <p id="logInError">{{logInMessage}}</p>
    <form method="post">
        <label>Email:
            <input type="email" name="email" data-ng-model="credentials.email"/>
        </label>

        <label>Password:
            <input type="password" name="password" data-ng-model="credentials.password"/>
        </label>
        
        <div id="login-buttons">
            <button data-ng-click="logIn(credentials)">Log In</button>
            <!--<button data-ng-click="closeLogIn()">Close</button>-->
        </div>
    </form>
</div>
BODY;

}


$body = <<< BODY
<section>
   $logInBody
</section>
BODY;

echo $body;
