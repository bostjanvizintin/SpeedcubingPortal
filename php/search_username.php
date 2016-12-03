<?php
session_start();

require('db_connect.php');


$db=db_conn_pdo();//connect to database


$query = $db->prepare("select * from user where username = :username");//prepare query 
$query->execute(array(':username' => $_GET['username']));//insert variables into query
$res = $query->fetchAll();//get results from that query

if($query->rowCount() == 0){
	$_SESSION['no_such_username'] = "Username ".$_GET['username']." not found.";
	header('Location:'.$_SERVER['HTTP_REFERER']);
	exit();
}else{
	//fill SESSION with user data
	$query = $db->prepare("select rights, name, surname, email, username, date_of_birth, picture, description from user where username = :username");//prepare query 
	$query->execute(array(':username' => $_GET['username']));//insert variables into query
	$res = $query->fetch(PDO::FETCH_ASSOC);//get results from that query

	$_SESSION['user_data'] = $res;
}


header('Location: ../profile.php?username='.$_GET['username']);
exit();


?>