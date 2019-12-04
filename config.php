<?php

ini_set('display_errors', 1);

session_save_path("/tmp");
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime ', 86400);

session_start();

define('DSN', 'mysql:host=localhost;dbname=mydb');
define('DB_USER', 'root');
define('DB_PASS', 'sakenomitai');

 ?>
