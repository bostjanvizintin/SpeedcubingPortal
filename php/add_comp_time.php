<?php

session_start();
require('db_connect.php');

$competition_entry_id = $_SESSION['comp_entry_id'];
$disc_id = $_SESSION['disc_id'];
$scramble = $_GET['scramble'];
$time = $_GET['time'];
$scrambles = $_SESSION['scrambles'];


$db=db_conn_pdo();//connect to database

//insert result
$query = $db->prepare("INSERT INTO result (time, algorithm_id_algorithm) values (:time, :algorithm_id)");//prepare query 
$query->bindParam(':time', $time);
$query->bindValue(':algorithm_id', getScrambleId($scrambles, $scramble));
$query->execute();
$result_id = $db->lastInsertId();


//insert into competition_entry_has_discipline_has_result
$query = $db->prepare("INSERT INTO competition_entry_has_discipline_has_result (competition_entry_id, discipline_id, result_id) values (:competition_entry_id, :discipline_id, :result_id)");//prepare query 
$query->bindParam(':competition_entry_id', $competition_entry_id);
$query->bindParam(':discipline_id', $disc_id);
$query->bindParam(':result_id', $result_id);
$query->execute();
$res = $query->fetchAll();//get results from that query


function getScrambleId($scr_array, $scr){
	foreach ($scr_array as $value) {
		if(strcmp(trim($value['algorithm']),trim($scr)) == 0){
			//echo "comparing ". $value['algorithm']. " with ".$scr."<br><br>";
			return $value['algorithm_id'];
		}
			
	}

}


?>