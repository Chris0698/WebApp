<?php

/**
 * Class Webpage
 */
class Webpage
{
    private $head = null;
    private $body = null;

    /**
     * Webpage constructor.
     * @param $title is the text to be displayed inside the web page tab
     * @param array $stylesheets, the stylesheets to be added to the site
     */
    public function __construct($title = "", array $stylesheets = null)
    {
        $stylesheetCSS = null;

        if($stylesheets != null)
        {
            foreach ($stylesheets as $stylesheet)
            {
                $stylesheetCSS.="<link rel='stylesheet' type='text/css' href='$stylesheet'/>\n";
            }
        }

        $this->head = <<< HEAD
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>$title</title>
    $stylesheetCSS
</head>
<body>
HEAD;
        $this->head.="\n";
        return $this->head;
    }

    /**
     * Added html to main body
     * @param $line, line of text to be added
     */
    public function addToBody($line)
    {
        $this->body.= $line;
    }

    /**
     * Gets the web page
     * @return string that contains the website
     */
    public function getPage()
    {
        return $this->head. $this->body. "\n" . "</body>" . "\n"  . "</html>";
    }
}