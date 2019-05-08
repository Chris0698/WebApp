<?php
require_once ("server/classes/session.class.php");

$session = Session::getInstance();

if($session->getProperty("username") && $session->getProperty("email"))
{
    $username = $session->getProperty("username");
    $logInBody = <<< BODY

        <h2>Sign Out</h2>
        <p>Welcome $username</p>
        <p>Press the log out button when done to log out.</p>
        <button data-ng-click="logOut()">Log Out</button>
BODY;
}
else
{
    //not logged in
    $logInBody = <<< BODY

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
BODY;

}

$body = <<< BODY
<!DOCTYPE html>
<html lang="en" data-ng-app="FilmApp">
<head>
    <meta charset="UTF-8">
    <title>Epic Fans Film App | Testing</title>
</head>
<body>
    <main>
        <div>Show ALl films:
            <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films">Show Films</a>
        </div>

        <div>Show all films where the term "air" is entered into search:
            <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films&term=air">Show Search Results</a>
        </div>

        <div>List films with the Cat ID of 4:
            <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films&cat=4">List films by Category with ID 4</a>
        </div>

        <div>List actors of a film (where film id equals 4):
            <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=actors&film_id=4">List Actors</a>
        </div>

        <div>
            <div id="login-form-inner">
                $logInBody
            </div>
        </div>

        <form>

        </form>
    </main>

    <script src="js/app.js"></script>
    <script src="js/controller.js"></script>
    <script src="js/dataServices.js"></script>

</body>
</html>
BODY;

echo $body;
