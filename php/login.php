<?php

require('db_connect.php');
session_start();

$db=db_conn_pdo();//connect to database


$username = $_POST['username'];
$password = md5($_POST['password']);




$query = $db->prepare("select * from user where username = :username and password = :password");//prepare query 
$query->execute(array(':username' => $username, ':password' => $password));//insert variables into query
$res = $query->fetchAll();//get results from that query

if($query->rowCount() == 1){
	$_SESSION['user_logged_in'] = $username;
	$_SESSION['user_logged_in_id'] = $res[0][0];
	$_SESSION['rights'] = $res[0][1];
}else{
	$_SESSION['false_login'] = "Wrong username or password!";
}



header('Location:'.$_SERVER['HTTP_REFERER']); 
exit();

?>