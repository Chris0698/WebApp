<?php

class Film
{
    public $film_id;
    public $title;
    public $description;
    public $release_year;
    public $last_update;
    public $name;               //category name
    public $filmLang;
    public $rating;
    public $rental_duration;
    public $rental_rate;
    public $film_length;
    public $replacement_cost;
    public $special_features;
    public $actors = array();
    public $comment;    //note
}