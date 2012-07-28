<?php

/* Organized the same as Peter Myer Nore did in section */
define('PROJECT2', '/home/jharvard/vhosts/project2/');
define('APP', PROJECT2 . 'application/');
define('M',   APP      . 'model/');
define('V',   APP      . 'view/');
define('C',   APP      . 'controller/');

/* Database defines as demonstrated in section */
define('DB_HOST', 'localhost');
define('DB_DATABASE', 'jharvard_project2');
define('DB_USER', 'jharvard');
define('DB_PASS', 'crimson');
define('DSN', 'mysql:host='.DB_HOST.';dbname='.DB_DATABASE);

/* start controller */
require(C . "controller.php");
?>