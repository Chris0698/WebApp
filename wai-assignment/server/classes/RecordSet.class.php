<?php

require_once ("DBConnection.class.php");
require_once ("Film.class.php");

/**
 * Class RecordSet
 */
class RecordSet
{
    protected $connection = null;
    protected $stmt = null;

    public function __construct()
    {
        $this->connection = DBConnection::getConnection();
    }

    public function getRecordSet($sql, $elementName = "ResultSet", $params = null)
    {
        if(is_array($params))
        {
            $this->stmt = $this->connection->prepare($sql);
            $this->stmt->execute($params);
        }
        else
        {
            $this->stmt = $this->connection->query($sql);
        }

        return $this->stmt;
    }
}

/**
 * Class PDORecordSet
 */
class PDORecordSet extends RecordSet
{
    public function getRecordSet($sql, $elementName = "element", $params = null)
    {
        return parent::getRecordSet($sql);
    }
}

/**
 * Class XMLRecordSet
 */
class XMLRecordSet extends RecordSet
{
    /**
     * @param $sql
     * @param string $elementName
     * @return false|PDOStatement|string|null
     */
    public function getRecordSet($sql, $elementName = "element", $params = null)
    {
        $stmt = null;
        try
        {
            $stmt = parent::getRecordSet($sql);
            $returnValue = "";
            header("content-type: text/xml");
            $returnValue.= "<?xml version=\"1.0\" encoding=\"ISO - 8859 - 1\"?>\n";
            $returnValue.= "<$elementName>s\n";
            while ($data = $stmt->fetchObject(PDO::FETCH_ASSOC))
            {
                $returnValue.= "\t<$elementName>\n";
                foreach ($data as $key => $value)
                {
                    $returnValue.= "\t\t<$key>$value</$key>\n";
                }
                $returnValue.= "\t<$elementName>\n";
            }
            $returnValue.= "<$elementName>s\n";
            return $returnValue;
        }
        catch (Exception $exception)
        {
            return $exception->getMessage();
        }
    }
}

/**
 * Class JSONRecordSet
 */
class JSONRecordSet extends RecordSet
{
    public function getRecordSet($sql, $elementName = "ResultSet", $params = null)
    {
        try
        {
            $stmt = parent::getRecordSet($sql);
            $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $numberOfResults = count($recordSet);

            return json_encode
            (
                array
                (
                    //"$elementName" => array
                    //(
                    "rowCount" => $numberOfResults,
                    "results" => $recordSet
                    //)
                ),
                JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT
            );
        }
        catch (Exception $exception)
        {
            return $exception->getMessage();
            //return json_decode('"Error" : "'.$exception->getMessage().'""');
        }
    }

    public function getActorsAndALlFilmsRecordSet($sql, $elementName = "ResultSet", $params = null)
    {
        try
        {
            $stmt = parent::getRecordSet($sql, $elementName, $params);
            $rows = array();

            $sql = "SELECT nfc_actor.first_name, nfc_actor.last_name
                    FROM nfc_actor 
                    INNER JOIN nfc_film_actor
                    ON nfc_film_actor.actor_id = nfc_actor.actor_id
                    WHERE nfc_film_actor.film_id = :film_id
                    ORDER BY nfc_actor.last_name";

            $actorStmt = $this->connection->prepare($sql);
            $filmCount = 0;

            while ($film = $stmt->fetchObject())
            {
                $filmCount++;
                $actorStmt->execute(array(":film_id" => $film->film_id));
                $actors = $actorStmt->fetchAll(PDO::FETCH_ASSOC);

                $rows [] = array
                (
                    "film_id" => $film->film_id,
                    "title" => $film->title,
                    "description" => $film->description,
                    "release_year" => $film->release_year,
                    "last_update" => $film->last_update,
                    "category" => $film->name,
                    "language" => $film->name,
                    "rating" => $film->rating,
                    "rental_duration" => $film->rental_duration,
                    "rental_rate" => $film->rental_rate,
                    "film_length" => $film->length,
                    "replacement_cost" => $film->replacement_cost,
                    "special_features" => $film->special_features,
                    "actors" => $actors,
                    "comment" => $film->comment
                );
            }


            if(empty($rows))
            {
                $output = array
                          (
                              "rowCount" => 0,
                              "results" => "No data!"
                          );
            }
            else
            {
                $output = array
                          (
                              "rowCount" => $filmCount,
                              "results" => $rows
                           );
            }
        }
        catch (Exception $exception)
        {
            $output = array("results" => $exception->getMessage());
        }
        finally
        {
            return json_encode($output, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
        }
    }
}