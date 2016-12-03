<?php

session_start();

require('db_connect.php');


$db=db_conn_pdo();//connect to database



$statement = "SELECT competition.competition_id, competition.name, competition.start_date, competition.end_date, competition_has_discipline_has_algorithm.discipline_id
FROM competition
INNER JOIN competition_has_discipline_has_algorithm
ON competition.competition_id=competition_has_discipline_has_algorithm.competition_id";

$statement = $statement." WHERE ";
$statement = $statement."(start_date < '".date('Y-m-d')."' AND end_date > '".date('Y-m-d')."')";




if(count($_GET) != 0){
	$statement = $statement." AND (";
	foreach($_GET as $value){
		$statement = $statement."competition_has_discipline_has_algorithm.discipline_id =".$value." OR ";
	}
	$statement = substr($statement, 0, -4);
	$statement = $statement.")";
}


$query = $db->prepare($statement);

$query->execute();//insert variables into query
$result_disciplines = $query->fetchAll();//get results from that query

if($query->rowCount() != 0){
	$statement = $statement." GROUP BY competition_id";
	$query = $db->prepare($statement);

	$query->execute();//insert variables into query
	$result_competitions = $query->fetchAll();//get results from that query


	$tmp_competition_id = $result_disciplines[0]['competition_id'];
	$bla = '';
	$tmp = array();

	foreach($result_disciplines as $value){
		if($tmp_competition_id == $value['competition_id']){
			$bla = $bla.$value['discipline_id'].',';
		}else{
			$tmp[$tmp_competition_id] = $bla;
			$tmp_competition_id = $value['competition_id'];
			$bla = $value['discipline_id'].",";
		}
	}
	$tmp[$tmp_competition_id] = $bla;


	$i = 0;
	foreach ($tmp as $key => $value) {
		if($result_competitions[$i]['competition_id'] == $key)
			$result_competitions[$i]['discipline_id'] = distinct_values($value);
		$i++;
	}
	echo "<br><br><br><br><br>";
	var_dump($result_competitions);
	$_SESSION['all_competitions'] = $result_competitions;
}


header("Location:".$_SERVER['HTTP_REFERER']);
exit();


function distinct_values($text){
	$text = substr($text, 0, -1);
	$dis = explode(',', $text);
	$tmp = array();
	foreach ($dis as $value) {
		$tmp[$value] = $value;
	}
	return $tmp;
}
?>