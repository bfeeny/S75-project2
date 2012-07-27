<?php

/* Organized the same as Peter Myer Nore did in section */
define('PROJECT1', '/home/jharvard/vhosts/project2/');
define('APP', PROJECT1 . 'application/');
define('M',   APP      . 'model/');
define('V',   APP      . 'view/');
define('C',   APP      . 'controller/');

/* Database defines as demonstrated in section */
define('DB_HOST', 'localhost');
define('DB_DATABASE', 'jharvard_project2');
define('DB_USER', 'jharvard');
define('DB_PASS', 'crimson');
define('DSN', 'mysql:host='.DB_HOST.';dbname='.DB_DATABASE);


/*
gather absolute path information, per
http://us2.php.net/manual/en/function.header.php
*/
$host = $_SERVER["HTTP_HOST"];
$path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");

// start controller
require(C . "controller.php");
?>