<?php
session_start();



if(!isset($_SESSION['user_logged_in_id'])){
	exit();
}

require('db_connect.php');


$db=db_conn_pdo();//connect to database

$time = $_GET['time'];
$scramble = $_GET['scramble'];

	//insert scramble into database
	$query = $db->prepare("insert into algorithm (algorithm, scramble, user_user_id) values (:algorithm, :scramble, :user_user_id)");//prepare query 

	$query->bindParam(':algorithm', $scramble);
	$query->bindValue(':scramble', 1);
	$query->bindValue(':user_user_id', $_SESSION['user_logged_in_id']);

	$query->execute();
	$scramble_id = $db->lastInsertId();


	//insert new result into database
	$query = $db->prepare("insert into result (time, algorithm_id_algorithm) values (:time, :algorithm_id_algorithm)");//prepare query 

	$query->bindParam(':time', $time);
	$query->bindParam(':algorithm_id_algorithm', $scramble_id);

	$query->execute();


	//insert practice_has_discipline_has_result
	$query = $db->prepare("insert into practice_has_discipline_has_result (practice_id, discipline_id, result_id) values (:practiceid, :disciplineid, :resultid)");//prepare query 

	$query->bindValue(':practiceid', $_SESSION['current_practice_id']);
	$query->bindValue(':disciplineid', $_SESSION['current_discipline_id']);
	$query->bindValue(':resultid', $db->lastInsertId());
	
	$query->execute();

	echo $time."\n".$scramble;-

exit();
?>