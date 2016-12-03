<?php

//var_dump($_GET);

session_start();

if($_SESSION['rights'] < 2){
	die("You need to be admin or moderator to add competition!");	
}

require('db_connect.php');
$db=db_conn_pdo();//connect to database


$error = FALSE;
//test if input data is incorrect
	
//chech if disciplines are set
$query = $db->prepare("select * from discipline");//prepare query 
$query->execute();//insert variables into query
$res = $query->fetchAll();//get results from that query

$disciplines = $res;
$error = TRUE;
$_SESSION['notice'] = "No disciplines set. Please select disciplines you want to add to competition.";
foreach($disciplines as $value){
	if(isset($_GET[$value[1]])){
		$error = FALSE;
		unset($_SESSION['notice']);
		break;
	}
}


//check if competition with same name exists
$query = $db->prepare("select * from competition where name = :name");//prepare query 
$query->execute(array(':name' => $_GET['competition_name']));//insert variables into query
$res = $query->fetchAll();//get results from that query

if($query->rowCount() != 0){
	$_SESSION['notice'] = $_SESSION['notice'].' Competition with that name already exist.';
	$error = TRUE;
}else{
	$_SESSION['competition_name'] = $_GET['competition_name'];
}

//check dates
if ($_GET['from'] > $_GET['to']){
	$_SESSION['notice'] = $_SESSION['notice']." Invalid dates.";
	$error = TRUE;
}else{
	$_SESSION['from'] = $_GET['from'];
	$_SESSION['to'] = $_GET['to'];
}

if($error){
	header('Location:'.$_SERVER['HTTP_REFERER']); 
	exit();

}



$all_disciplines = array("3x3","4x4","5x5","6x6","7x7","8x8","9x9");
$competition_name = $_GET["competition_name"];
$scrambles = array(/*discipline_id => scrambles*/);
//creates array with discipline ids as keys and scrambles(in a string with separated with comma) as values
for($i=0;$i<7;$i++){
	if(isset($_GET[$all_disciplines[$i]])){
		$scrambles = array_push_assoc($scrambles, $_GET[$all_disciplines[$i]], $_GET["scrambles_".$all_disciplines[$i]]);
	}
}
//insert competition into database
$query = $db->prepare("insert into competition (start_date, end_date, name) values (:start_date, :end_date, :name)");//prepare query 
$query->bindParam(':start_date', $_GET['from']);
$query->bindParam(':end_date', $_GET['to']);
$query->bindParam(':name', $_GET['competition_name']);

$query->execute();
//save id of inserted competition
$competition_id = $db->lastInsertId();



//go through discipline scrambles
foreach($scrambles as $id => $scrambles_whole){
	//separate scrambles which are separated with comma
	$single_scrambles = explode(",", $scrambles_whole);
	//for each scramble of discipline with $id insert scramble into database and then add competition has discipline has algorithm
	foreach($single_scrambles as $value){
		//insert algorithm
		$query = $db->prepare("insert into algorithm (algorithm, scramble, user_user_id) values (:algorithm, :scramble, :user_user_id)");//prepare query 
		$query->bindParam(':algorithm', $value);
		$query->bindValue(':scramble', 1);
		$query->bindParam(':user_user_id', $_SESSION['user_logged_in_id']);

		$query->execute();
		//insert competition has discipline has algorithm
		$last_algorithm_id = $db->lastInsertId();
		$query = $db->prepare("insert into competition_has_discipline_has_algorithm (competition_id, discipline_id, algorithm_id) values (:competition_id, :discipline_id, :algorithm_id)");//prepare query 
		$query->bindParam(':competition_id', $competition_id);
		$query->bindParam(':discipline_id', $id);
		$query->bindParam(':algorithm_id', $last_algorithm_id);

		$query->execute();
	}

}

	unset($_SESSION['notice']);
	unset($_SESSION['from']);
	unset($_SESSION['to']);
	unset($_SESSION['competition_name']);
	header('Location:../add_competition.php'); 
	exit();
//function to push value to assoc array
function array_push_assoc($array, $key, $value){
	$array[$key] = $value;
	return $array;
}



?>