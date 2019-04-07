<?php
require_once ("../../server/classes/session.class.php");

$session = Session::getInstance();

//Cheap hack so the user doesn't see the log in form if they enter the logIn into the URL when they are logged in
if(!$session->getProperty("username"))
{
    echo '
<section id="login-form">
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
            
            <button data-ng-click="logIn(credentials)">Log In</button>
            <button data-ng-click="closeLogIn()">Close</button>
        </form>
    </div>
</section>';
}