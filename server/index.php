<?php
require_once ("classes/RecordSet.class.php");
require_once ("classes/session.class.php");
//require_once ("classes/User.class.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null;
$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : null;
$catID = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : null;
$filmID = isset($_REQUEST['film_id']) ? $_REQUEST['film_id'] : null;

if(empty($action))
{
    if((($_SERVER["REQUEST_METHOD"] == "POST") || ($_SERVER["REQUEST_METHOD"] == "PUT") ||
        ($_SERVER["REQUEST_METHOD"] == "DELETE")) && (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false))
    {
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
        if(empty($filmID) && empty($term) && empty($catID))
        {
            //list every film
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
            $resultSet = $resultSet->getRecordSet($sql);
            echo $resultSet;
        }
        elseif(empty($filmID) && !empty($term) && empty($catID))
        {
            //get films if similar to a search term
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
            $resultSet = $resultSet->getRecordSet($sql, "", array(":term" => "%{$term}%"));
            echo $resultSet;
        }
        else if(!empty($catID) && empty($filmID) && empty($term))
        {
            if ($catID != 0)     //List all films catID will either be 0 or null
            {
                //list all films with a cat
                $filmSQL = "SELECT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
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
                $data = $resultSet->getRecordSet($filmSQL, "", array(":cat" => $catID));
                echo $data;
            }
            else
            {
                //list all films again
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
                $data = $resultSet->getRecordSet($sql);
                echo $data;
            }
        }

        break;

    case "logInUser":
        $session->endProperty("email");
        $session->endProperty("username");

        if(!empty($data)) {
            $userEmail = $data["email"];
            $userPassword = $data["password"];

            //user could enter email but not password, don't proceed in code if one of them are empty
            if (!empty($userEmail) || (!empty($userPassword))) {
                $userEmail = trim($userEmail);
                $userPassword = trim($userPassword);

                //server validation needed such as removing tags

                $sql = "SELECT email, username, password
                        FROM nfc_user
                        WHERE email = :email";

                $resultSet = new RecordSet();
                $resultSet = $resultSet->getRecordSet($sql, "", array(":email" => $userEmail));

                if ($resultSet !== false) {
                    $user = $resultSet->fetchObject();

                    if (!empty($user)) {
                        if (password_verify($userPassword, $user->password)) {
                            $session->setProperty("email", $user->email);
                            $session->setProperty("username", $user->username);
                            echo '{"results" : "success"}';
                            break;
                        }
                    }
                }
            }
        }

        // 401 means 'authorisation failed failed'
        header("Content-Type: application/json", true, 401);
        echo '{"results" : {"data" : "Complete the form"}}';
        break;

    case "logOutUser":
        $session->endProperty("email");
        $session->endProperty("username");
        break;
    case "listCategories":
        $selectSQL = "SELECT category_id, name
                      FROM nfc_category";
        $resultSet = new JSONRecordSet();
        $data = $resultSet->getRecordSet($selectSQL);
        echo $data;
        break;
    case "listActors" :
        $sql = "SELECT DISTINCT nfc_actor.first_name, nfc_actor.last_name
                FROM nfc_actor 
                INNER JOIN nfc_film_actor
                ON nfc_film_actor.actor_id = nfc_actor.actor_id
                WHERE nfc_film_actor.film_id = :film_id";

        $resultSet = new JSONRecordSet();
        $data = $resultSet->getRecordSet($sql, "", array(":film_id" => $filmID));
        echo $data;
        break;
    case "listNotes":
        if($session->getProperty("username") && $session->getProperty("email"))
        {
            $sql = "SELECT comment, user, nfc_note.film_id, lastupdated
                    FROM nfc_note
                    WHERE nfc_note.film_id = :film_id";
            $resultSet = new JSONRecordSet();
            $resultSet = $resultSet->getRecordSet($sql, "", array(":film_id" => $filmID));
            echo $resultSet;
        }
        else
        {
            echo '{"results" : "NotLoggedIn"}';
        }
        break;
    case "updateNote":
        //check the user is still signed in
        if($session->getProperty("email") && $session->getProperty("username"))
        {
            if(!empty($data))
            {
                var_dump($data);
                $note = json_decode($data);

                $username = $session->getProperty("email");
                $date = date('Y-m-d H:i:s');

                //check that the note is in the database table first
                $sql = "SELECT film_id FROM nfc_note WHERE film_id = :filmID";
                $resultSet = new RecordSet();
                $resultSet = $resultSet->getRecordSet($sql, "", array("filmID" => $note->film_id));
                if($resultSet !== false)
                {
                    //record exists
                    $sql = "UPDATE nfc_note SET user = :user, film_id = :filmID, comment = :comment, lastupdated = :date";
                }
                else
                {
                    //record does not exist
                    $sql = "INSERT into nfc_note VALUES (:user, :filmID, :comment, :date)";
                }

                $resultSet = new JSONRecordSet();
                $resultSet = $resultSet->getRecordSet($sql, "", array
                                                                            (
                                                                                "user" => $username,
                                                                                "filmID" => $note->film_id,
                                                                                ":comment" => $note->comment,
                                                                                ":date" => $date
                                                                            )
                );

                echo '{"results": "success"}';
            }

            echo '{"results": "No data"}';
        }
        else
        {
            header("Content-Type: application/json", true, 412);
        }
        break;
    default:
        echo '{"results" : [{"data": "default no action taken"}]}';
        break;
}