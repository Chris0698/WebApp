<?php
require_once ("../../server/classes/session.class.php");

$session = Session::getInstance();

if($session->getProperty("email"))
{
    $username = $session->getProperty("username");

    echo "<div class='logInFragment'>
              <h2>Welcome: $username</h2>
              <button data-ng-click='logOut()'>Log Out</button>
          </div>";
}
else
{
    echo "<div class='logInFragment'>
              <h2>Sign In</h2>
              <a href='#/logIn'>Log In</a>
          </div>";
}