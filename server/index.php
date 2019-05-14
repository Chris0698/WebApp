<?php
//imports
require_once ("classes/RecordSet.class.php");
require_once ("classes/session.class.php");
require_once ("classes/FileWriter.class.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null;
$searchTerm = isset($_REQUEST['searchTerm']) ? $_REQUEST['searchTerm'] : null;
$catID = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : null;
$filmID = isset($_REQUEST['film_id']) ? $_REQUEST['film_id'] : null;

if(empty($action)) {
    if((($_SERVER["REQUEST_METHOD"] == "POST") || ($_SERVER["REQUEST_METHOD"] == "PUT") ||
        ($_SERVER["REQUEST_METHOD"] == "DELETE")) && (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false)) {
        $input = json_decode(file_get_contents("php://input"), true);
        $action = isset($input['action']) ? $input['action'] : null;
        $subject = isset($input['subject']) ? $input['subject'] : null;
        $data = isset($input['data']) ? $input['data'] : null;
    }
}

$session = Session::getInstance();
$route = $action . ucfirst($subject);

header("Content-Type: application/json");

switch ($route)
{
    case "listFilms":
        if(empty($filmID) && empty($searchTerm) && empty($catID)) {
            //list every film
            try {
                $sql = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name AS catName, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features,
                           nfc_language.name AS filmLang
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    ORDER BY title";
                $resultSet = new JSONRecordSet();
                $rows = $resultSet->getRecordSet($sql);
                $output = array("status" => 200, "error" => "", "data" => $rows);
            }
            catch (Exception $exception) {
                $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());

                //Logging the exception to a file
                FileWriter::WriteExceptionToFile("GetFilmsException.txt", $exception);
            }

            echo json_encode($output);

        }
        else if(empty($filmID) && !empty($searchTerm) && empty($catID)) {
            //get films if similar to a search term
            try {
                $sql = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name AS catName, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features,
                           nfc_language.name AS filmLang
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    WHERE title LIKE :term
                    ORDER BY title";
                $resultSet = new JSONRecordSet();
                $rows = $resultSet->getRecordSet($sql, array(":term" =>  "%{$searchTerm}%"));
                $output = array("status" => 200, "error" => "", "data" => $rows);
            }
            catch (Exception $exception) {
                $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                FileWriter::WriteExceptionToFile("GetFilmsException.txt", $exception);
            }

            echo json_encode($output);
        }
        else if(!empty($catID) && empty($filmID) && empty($searchTerm))
        {
            if ($catID != 0) {     //List all films catID will either be 0 or null
                //list all films with a cat
                try {
                    $sql = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                               nfc_film.rating, nfc_film.last_update, nfc_category.name AS catName, nfc_film.rental_duration,
                               nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features,
                               nfc_language.name AS filmLang
                            FROM nfc_film
                            INNER JOIN nfc_film_category
                            ON nfc_film.film_id = nfc_film_category.film_id
                            INNER JOIN nfc_category
                            ON nfc_category.category_id = nfc_film_category.category_id
                            INNER JOIN nfc_language
                            ON nfc_language.language_id = nfc_film.language_id
                            WHERE nfc_category.category_id = :cat
                            ORDER BY title";
                    $resultSet = new JSONRecordSet();
                    $rows = $resultSet->getRecordSet($sql, array(":cat" => $catID));
                    $output = array("status" => 200, "error" => "", "data" => $rows);
                }
                catch (Exception $exception) {
                    $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                    FileWriter::WriteExceptionToFile("GetFilmsException.txt", $exception);
                }

                echo json_encode($output);

            }
            else {
                //list all films again
                try {
                    $sql = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name AS catName, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features,
                           nfc_language.name AS filmLang
                        FROM nfc_film
                        INNER JOIN nfc_film_category
                        ON nfc_film.film_id = nfc_film_category.film_id
                        INNER JOIN nfc_category
                        ON nfc_category.category_id = nfc_film_category.category_id
                        INNER JOIN nfc_language
                        ON nfc_language.language_id = nfc_film.language_id
                        ORDER BY title";
                    $resultSet = new JSONRecordSet();
                    $rows = $resultSet->getRecordSet($sql);
                    $output = array("status" => 200, "error" => "", "data" => $rows);
                }
                catch (Exception $exception) {
                    $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                    FileWriter::WriteExceptionToFile("GetFilmsException.txt", $exception);
                }

                echo json_encode($output);
            }
        }
        else {
            //list a specific film
            try {
                $sql = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name AS catName, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features,
                           nfc_language.name AS filmLang
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    WHERE nfc_film.film_id = :film_id";
                $resultSet = new JSONRecordSet();
                $rows = $resultSet->getRecordSet($sql, array("film_id" => $filmID));
                $output = array("status" => 200, "error" => "", "data" => $rows);
            }
            catch (Exception $exception) {
                $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                FileWriter::WriteExceptionToFile("GetFilmsException.txt", $exception);
            }

            echo json_encode($output);
        }

        break;

    case "logInUser":
        //end the current user session
        $session->endProperty("email");
        $session->endProperty("username");

        $output = null;

        try {
            //hack for testing page
            $userEmail = filter_has_var(INPUT_POST, 'email') ? $_POST['email'] : null;
            $userPassword = filter_has_var(INPUT_POST, 'password') ? $_POST['password'] : null;

            if(empty($userEmail) && empty($userPassword))
            {
                $userEmail = $data["email"];
                $userPassword = $data["password"];
            }

//            if(!empty($data)) {
                //i made data an array, simply because it works, could of maybe made it JSON
//                $userEmail = $data["email"];
//                $userPassword = $data["password"];

                //user could of completed one field
                if(!empty($userEmail) && !empty($userPassword)) {
                    $userEmail = trim($userEmail);
                    $userPassword = trim($userPassword);

                    $sql = "SELECT email, username, password
                            FROM nfc_user
                            WHERE email = :email";
                    $resultSet = new RecordSet();
                    $resultSet = $resultSet->getRecordSet($sql, array(":email" => $userEmail));

                    if($resultSet !== false) {
                        $user = $resultSet->fetchObject();
                        if(!empty($user)) {
                            if(password_verify($userPassword, $user->password)) {
                                //user details correct
                                $session->setProperty("email", $user->email);
                                $session->setProperty("username", $user->username);
                                $output = array("status" => 200, "error" => "", "data" => array("results" => "success"));
                            }
                            else {
                                $output = array("status" => 401, "error" => "Incorrect Log In details", "data" => array());
                            }
                        }
                    }
                }
//            }
            else {
                $output = array("status" => 401, "error" => "Please Complete the Form", "data" => array());
            }
        }
        catch (Exception $exception) {
            $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
            FileWriter::WriteExceptionToFile("LogInUserException.txt", $exception);
        }

        echo json_encode($output);
        break;

    case "logOutUser":
        //log the user out by ending the property
        $session->endProperty("email");
        $session->endProperty("username");
        $output = array("status" => 200, "error" => "", "data" => array("results" => "Successfully logged out"));
        echo json_encode($output);
        break;
    case "listCategories":
        //get the select categories
        $data = null;

        try {
            $sql = "SELECT category_id, name
                    FROM nfc_category";
            $resultSet = new JSONRecordSet();
            $rows = $resultSet->getRecordSet($sql);
            $output = array("status" => 200, "error" => "", "data" => $rows);
        }
        catch (Exception $exception) {
            $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
            FileWriter::WriteExceptionToFile("GetSelectCatException.txt", $exception);
        }

        echo json_encode($output);
        break;
    case "listActors" :
        //Get the actors for a film
        try {
            $sql = "SELECT DISTINCT nfc_actor.first_name, nfc_actor.last_name
                    FROM nfc_actor 
                    INNER JOIN nfc_film_actor
                    ON nfc_film_actor.actor_id = nfc_actor.actor_id
                    WHERE nfc_film_actor.film_id = :film_id";
            $resultSet = new JSONRecordSet();
            $rows = $resultSet->getRecordSet($sql, array(":film_id" => $filmID));
            $output = array("status" => 200, "error" => "", "data" => $rows);
        }
        catch (Exception $exception) {
            $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
            FileWriter::WriteExceptionToFile("GetActorsException.txt", $exception);
        }

        echo json_encode($output);
        break;
    case "listNotes":
        //get the notes for a film if the user is logged in
        $data = null;
        if($session->getProperty("email") && $session->getProperty("username")) {
            try {
                $sql = "SELECT comment, user, nfc_note.film_id, lastupdated
                        FROM nfc_note
                        WHERE nfc_note.film_id = :film_id";
                $resultSet = new JSONRecordSet();
                $rows = $resultSet->getRecordSet($sql, array("film_id" => $filmID));
                $output = array("status" => 200, "error" => "", "data" => $rows);
            }
            catch (Exception $exception) {
                $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                FileWriter::WriteExceptionToFile("ListNotesException.txt", $exception);
            }
        }
        else {
            $output = array("status" => 401, "error"=> "not logged in", "data" => array());
        }

        echo json_encode($output);
        break;
    case "updateNote":
        //check the user is still signed in
        if($session->getProperty("email") && $session->getProperty("username")) {
            try {
                //for testing
                $filmID = filter_has_var(INPUT_POST, 'noteID') ? $_POST['noteID'] : null;
                $noteComment = filter_has_var(INPUT_POST, 'note') ? $_POST['note'] : null;

                //legit way
                if(empty($filmID) && empty($noteComment)) {
                    $note = json_decode($data);
                    $filmID = $note->film_id;
                    $noteComment = $note->comment;
                }

                if(!empty($filmID) && !empty($noteComment)) {
                    $date = date('Y-m-d H:i:s');

                    //trim the note from whitespace
                    $noteComment = trim($noteComment);

                    //get if the note is already in the DB
                    $sql = "SELECT film_id FROM nfc_note WHERE film_id = :filmID";
                    $resultSet = new RecordSet();
                    $resultSet = $resultSet->getRecordSet($sql, array(":filmID" => $filmID));
                    if($resultSet !== false) {
                        $data = $resultSet->fetchObject();
                        //if the note does not exist already in nfc_note then $data is empty.
                        //If it does already exists then $data has data.
                        if(!empty($data)) {
                            //record exists
                            $sql = "UPDATE nfc_note
                                SET user = :user, comment = :comment, lastupdated = :date
                                WHERE film_id = :film_id";
                            $resultSet = new JSONRecordSet();
                            $resultSet = $resultSet->getRecordSet($sql, array
                                (
                                    "user" => $session->getProperty("email"),
                                    ":comment" => $noteComment,
                                    ":date" => $date,
                                    "film_id" => $note->film_id
                                )
                            );
                        }
                        else {
                            //no record exists
                            $sql = "INSERT INTO nfc_note VALUES (:user, :filmID, :comment, :date)";
                            $resultSet = new JSONRecordSet();
                            $resultSet = $resultSet->getRecordSet($sql, array
                                (
                                    "user" => $session->getProperty("email"),
                                    "filmID" => $filmID,
                                    ":comment" => $noteComment,
                                    ":date" => $date
                                )
                            );
                        }
                    }

                    $output = array("status" => 200, "error" => "", "data" => array("results" => "success"));
                }
            }
            catch (Exception $exception) {
                $output = array("status" => 500, "error" => $exception->getMessage(), "data" => array());
                FileWriter::WriteExceptionToFile("UpdateNoteException.txt", $exception);
            }

            echo json_encode($output);
        }
        break;
    default:
        $output = array("status" => 500, "error" => "No action taken", "data" => array("results" => "Default no action taken"));
        echo json_encode($output);
        break;
}