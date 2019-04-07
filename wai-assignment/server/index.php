<?php
require_once ("classes/RecordSet.class.php");
require_once ("classes/session.class.php");
require_once ("classes/User.class.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null;
$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : null;
$catID = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : null;
$filmID = isset($_REQUEST['film_id']) ? $_REQUEST['film_id'] : null;
$actorID = isset($_REQUEST['act_id']) ? $_REQUEST['act_id'] : null;

$session = Session::getInstance();

if(empty($action))
{
    if((($_SERVER["REQUEST_METHOD"] == "POST") ||
        ($_SERVER["REQUEST_METHOD"] == "PUT") ||
        ($_SERVER["REQUEST_METHOD"] == "DELETE")) &&
        (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false))
        {
            $input = json_decode(file_get_contents("php://input"), true);
            $action = isset($input['action']) ? $input['action'] : null;
            $subject = isset($input['subject']) ? $input['subject'] : null;
            $data = isset($input['data']) ? $input['data'] : null;
        }
}

$route = $action . ucfirst($subject);
$db = AppRegistry::getDBConnection();

header("Content-Type: application/json");
switch ($route)
{
    case "checkLogIn":
        if($session->getProperty('email'))
        {
            $data = '{"results" : "true"}';

        }
        else
        {
            $data = '{"results" : "false"}';
        }
        echo $data;
        break;
    //list all caterogies for the drop down select menu
    case "listCategories":

        $selectSQL = "SELECT category_id, name
                      FROM nfc_category";
        $resultSet = new JSONRecordSet();
        $data = $resultSet->getRecordSet($selectSQL);
        echo $data;
        break;
    case "listNotes":
        if(!empty($filmID) && $session->getProperty("username") && $session->getProperty("email"))
        {
            $sql = "SELECT comment
                    FROM nfc_note
                    WHERE nfc_note.film_id = $filmID";
            $resultSet = new JSONRecordSet();
            //$data = $resultSet->getRecordSet($sql, "", array(":filmID" => $filmID));
            $data = $resultSet->getRecordSet($sql);
            echo $data;
            break;
        }
        else
        {
            $data = '{"results" : "notLoggedIn" , "data" : {"text": "Not Logged In"}}';
            echo $data;
            break;
        }
break;
    case "listFilm":
        //List films
        if(empty($filmID) && empty($term) && empty($catID))
        {
            $sql = "SELECT DISTINCT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features, 
                           nfc_language.name, nfc_note.comment
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    LEFT JOIN nfc_note
                    ON nfc_note.film_id = nfc_film.film_id
                    ORDER BY title";

            $resultSet = new JSONRecordSet();
            $data = $resultSet->getActorsAndALlFilmsRecordSet($sql);
            echo $data;
            break;
        }
        else if(!empty($term) && empty($filmID) && empty($catID))
        {
            //list film that have a search term
            $sql = "SELECT DISTINCT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                           nfc_film.rating, nfc_film.last_update, nfc_category.name, nfc_film.rental_duration,
                           nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features, 
                           nfc_language.name, nfc_note.comment
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    LEFT JOIN nfc_note
                    ON nfc_note.film_id = nfc_film.film_id
                    WHERE title LIKE :term
                    ORDER BY title";
            $resultSet = new JSONRecordSet();
            $data = $resultSet->getActorsAndALlFilmsRecordSet($sql, "", array(":term" => "%{$term}%"));
            echo $data;
            break;
        }
        else if(!empty($catID) && empty($filmID) && empty($term))
        {
            if($catID != 0)     //List all films catID will either be 0 or null
            {
                //list all films with a cat
                $filmSQL = "SELECT DISTINCT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                               nfc_film.rating, nfc_film.last_update, nfc_category.name, nfc_film.rental_duration,
                               nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features, 
                               nfc_language.name, nfc_note.comment
                            FROM nfc_film
                            INNER JOIN nfc_film_category
                            ON nfc_film.film_id = nfc_film_category.film_id
                            INNER JOIN nfc_category
                            ON nfc_category.category_id = nfc_film_category.category_id
                            INNER JOIN nfc_language
                            ON nfc_language.language_id = nfc_film.language_id
                            LEFT JOIN nfc_note
                            ON nfc_note.film_id = nfc_film.film_id
                            WHERE nfc_category.category_id = :cat
                            ORDER BY title";
                $resultSet = new JSONRecordSet();
                $data = $resultSet->getActorsAndALlFilmsRecordSet($filmSQL, "", array(":cat" => $catID));
                echo $data;
                break;
            }
            else
            {
                //list all films again
                $sql = "SELECT DISTINCT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                               nfc_film.rating, nfc_film.last_update, nfc_category.name, nfc_film.rental_duration,
                               nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features, 
                               nfc_language.name, nfc_note.comment
                        FROM nfc_film
                        INNER JOIN nfc_film_category
                        ON nfc_film.film_id = nfc_film_category.film_id
                        INNER JOIN nfc_category
                        ON nfc_category.category_id = nfc_film_category.category_id
                        INNER JOIN nfc_language
                        ON nfc_language.language_id = nfc_film.language_id
                        LEFT JOIN nfc_note
                        ON nfc_note.film_id = nfc_film.film_id
                        ORDER BY title";

                $resultSet = new JSONRecordSet();
                $data = $resultSet->getActorsAndALlFilmsRecordSet($sql);
                echo $data;
                break;
            }
        }
        else
        {
            //list a specific film
            $sql = "SELECT DISTINCT nfc_film.film_id, nfc_film.title, nfc_film.description, nfc_film.release_year,
                               nfc_film.rating, nfc_film.last_update, nfc_category.name, nfc_film.rental_duration,
                               nfc_film.rental_rate, nfc_film.length, nfc_film.replacement_cost, nfc_film.special_features, 
                               nfc_language.name, nfc_note.comment
                    FROM nfc_film
                    INNER JOIN nfc_film_category
                    ON nfc_film.film_id = nfc_film_category.film_id
                    INNER JOIN nfc_category
                    ON nfc_category.category_id = nfc_film_category.category_id
                    INNER JOIN nfc_language
                    ON nfc_language.language_id = nfc_film.language_id
                    LEFT JOIN nfc_note
                    ON nfc_note.film_id = nfc_film.film_id
                    WHERE nfc_film.film_id = $filmID";

            $resultSet = new JSONRecordSet();
            //$data = $resultSet->getRecordSet($sql, "", array(":film_id" => $filmID));
            $data = $resultSet->getRecordSet($sql);
            echo $data;
            break;

        }
    case "listActors" :
        $sql = "SELECT DISTINCT nfc_actor.first_name, nfc_actor.last_name
                FROM nfc_actor 
                INNER JOIN nfc_film_actor
                ON nfc_film_actor.actor_id = nfc_actor.actor_id
                WHERE nfc_film_actor.film_id = $filmID";

        $resultSet = new JSONRecordSet();
        //$data = $resultSet->getRecordSet($sql, "", array(":film_id" => $filmID));
        $data = $resultSet->getRecordSet($sql);
        echo $data;
        break;
    case "logInUser":
        $session->endProperty("email");
        $session->endProperty("username");

        if(!empty($data))
        {
            $userCredentials = $data;

            $userEmail = $userCredentials["email"];
            $userPassword = $userCredentials["password"];

            //user could enter email but not password, don't proceed in code if one of them are empty
            if(!empty($userEmail) || (!empty($userPassword)))
            {
                $userEmail = trim($userEmail);
                $userPassword = trim($userPassword);

                $sql = "SELECT email, username, password
                        FROM nfc_user
                        WHERE email = :email";

                $resultSet = new RecordSet();
                $resultSet = $resultSet->getRecordSet($sql, "", array(":email" => $userEmail));

                if($resultSet !== false)
                {
                    $user = $resultSet->fetchObject();

                    if(!empty($user))
                    {
                        $userPassword = md5($userPassword);

                        if (/*password_verify($userPassword, $user->password)*/  $userPassword == $user->password)
                        {
                            $session->setProperty("email", $user->email);
                            $session->setProperty("username", $user->username);
                            echo '{"result": "Log in success"}';
                        }
                        else
                        {
                            header("Content-Type: application/json", true, 401);
                            echo '{"result": "Login credentials incorrect"}';
                        }

                        break;
                    }
                }
            }
            else
            {
                header("Content-Type: application/json", true, 401);
                echo '{"result": "No Data"}';
                break;
            }
        }

        header("Content-Type: application/json", true, 401);  // 401 means 'authorisation failed failed'
        echo '{"result" : {"data" : "Please enter both details."}}';
        break;
    case "logOutUser":
        $session->endProperty("email");
        $session->endProperty("username");
        break;
    case "updateNote":
        $film = json_decode($data);
        $film_id = $film->film_id;

        $userEmail = $session->getProperty("email");

        try
        {
            $lastUpdate = new DateTime();
            $lastUpdate->format('Y-m-d H:i:s');     //formatted to match the format in the DB
            $lastUpdate->getTimestamp();
        }
        catch (Exception $exception)
        {
            echo '{"result": "exception"}';
            break;
        }

        //check that the note already exists first
        $sql = "SELECT nfc_note.film_id
                FROM nfc_note
                WHERE  nfc_note.film_id = $film_id";
        $resultSet = new RecordSet();
        $data = $resultSet->getRecordSet($sql);

        if($data === false)
        {
            //note does not exists, so insert one into the table
            $sql = "INSERT INTO nfc_note VALUES (:email, :filmID, :comment, :lastUpdated)";
            $resultSet = new JSONRecordSet();
            $data = $resultSet->getRecordSet($sql, "", array
                                                                    (
                                                                        ":email" => $userEmail,
                                                                        ":filmID" => $film_id,
                                                                        ":comment" => $film->comment,
                                                                        ":lastUpdated" => $lastUpdate
                                                                    )
            );
            echo '{"result": "success insert"}';
        }
        else
        {
            //if it note does not exists then insert the note
            $sql = "UPDATE nfc_note SET email = :email, comment = :comment WHERE film_id = :film_id";
            $data = new JSONRecordSet();
            $data = $data->getRecordSet($sql, "", array
                                                                (
                                                                    ":email" => $userEmail,
                                                                    ":film_id" => $film_id
                                                                )
            );
            echo '{"result" : "success update"}';
        }

        break;
    default:
        echo '{"result" : [{"data": "default no action taken"}]}';
        break;
}