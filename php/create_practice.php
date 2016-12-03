<?php
session_start();


if(!isset($_SESSION['user_logged_in']))
	exit();


require('db_connect.php');






$date = new DateTime();
$now = $date->getTimestamp(); 

$db=db_conn_pdo();//connect to database




//insert new practice
$query = $db->prepare("insert into practice (date) values (:date)");//prepare query 

$query->bindValue(":date", $now);

$query->execute();

//insert new user_has_practice
$query = $db->prepare("insert into user_has_practice (user_id_user, practice_id_practice) values (:id, :practice)");//prepare query 

$_SESSION['current_practice_id'] = $db->lastInsertId();

$query->bindValue(":id", $_SESSION['user_logged_in_id']);
$query->bindValue(":practice", $_SESSION['current_practice_id']);

$query->execute();





?>