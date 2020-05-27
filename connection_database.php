<?php
include_once 'config.php';
try{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER);}
catch (PDOException $e){
    echo 'Connection is failed: '. $e->getMessage();
    exit();
}