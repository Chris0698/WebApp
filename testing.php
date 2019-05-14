<?php
require_once ("server/classes/session.class.php");
require_once ("server/classes/webpage.class.php");

$session = Session::getInstance();

if($session->getProperty("username") && $session->getProperty("email")) {
    $username = $session->getProperty("username");
    $logInBody = <<< BODY

    <h2>Sign Out</h2>
    <p>Welcome $username</p>
    <p>Press the log out button when done to log out.</p>
    <form action="server/index.php?action=logOutUser" method="post">
        <input type="submit" value="Log Out"/>
    </form>
BODY;
}
else {
    //not logged in
    $logInBody = <<< BODY
    <h2>Log In</h2>
    <p>Log into the app to make changes.</p>
    <p></p>
    <form method="post" action="server/index.php?action=logInUser">
        <label>Email:
            <input type="email" name="email" data-ng-model="credentials.email"/>
        </label>

        <label>Password:
            <input type="password" name="password" data-ng-model="credentials.password"/>
        </label>
        
        <div id="login-buttons">
            <input type="submit" value="Log In"/>
        </div>
    </form>
BODY;

}

if($session->getProperty("username") && $session->getProperty("email")) {
    //show the note form
    $note = <<< NOTE
    <form method="post" action="server/index.php?action=updateNote">
       <p>Check the link above to see the JSON code for the updated record in the database.</p>
        <fieldset>
            <legend>Note</legend>
            <input type="text" value="10" name="noteID" readonly hidden/>
            <label>Create note for film id: 10
                <textarea name="note"></textarea>
            </label>
            <input type="submit" value="Save"/>
        </fieldset>
    </form>

NOTE;

}
else {
    $note = <<< NOTE
    <p>Log in to see note.</p>
NOTE;

}


$body = <<< BODY
<!DOCTYPE html>
<html lang="en" data-ng-app="FilmApp">
<head>
    <meta charset="UTF-8">
    <title>Epic Fans Film App | Testing</title>
    <link rel="stylesheet" type="text/css" href="css/main.css"/>
</head>
<body>
    <div id="page">
        <main>
            <h1>Epic Fans Films Testing</h1>
             <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="testing.php" target="_blank">Testing</a></li>
                </ul>
            </nav>
      
            <noscript>This website requires Javascript to be enabled.</noscript>
            
            <div>Show All films:
                <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films">Show Films</a>
            </div>
     

            <div>Show all films where the term "air" is entered into search:
                <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films&term=air">Show Search Results</a>
            </div>
    
            <div>List films with the Cat ID of 4:
                <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=films&cat=4">List films by Category with ID 4</a>
            </div>
    
            <div>List actors of a film (where film id equals 3):
                <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=actors&film_id=3">List Actors</a>
            </div>
            
            <div>Show note for film id 10:
                <a href="http://localhost/wai-assignment/server/index.php?action=list&subject=notes&film_id=10">Show note for film id 10</a>
            </div>
            
             <div>
                <h2>Note</h2>
                $note
            </div>
    
            <div>
                <div id="login-form-inner">
                    $logInBody
                </div>
            </div>
        </div>
    </main>
    
    <!--Angular imports.-->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular-route.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular-resource.js"></script>

    <script src="js/app.js"></script>
    <script src="js/controller.js"></script>
    <script src="js/dataServices.js"></script>

</body>
</html>
BODY;

echo $body;
