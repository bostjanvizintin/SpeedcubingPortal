<?php

require('db_connect.php');
session_start();

$username = $_POST['username'];
$password = $_POST['password'];
$password_retyped = $_POST['password_retyped'];
$email = $_POST['email'];
$email_retyped = $_POST['email_retyped'];
$errors = FALSE;



$db=db_conn_pdo();//connect to database



$query = $db->prepare("select * from user where username = :username");//prepare query 
$query->execute(array(':username' => $username));//insert variables into query
$res = $query->fetchAll();//get results from that query

//if there are already records of chosen username add text to notice
if($query->rowCount() != 0){
	$_SESSION['notice'] = $_SESSION['notice'].' Username already taken. Please chose other username.';
	$errors = TRUE;
}else{
	$_SESSION['username'] = $username;
}

$query = $db->prepare("select * from user where email = :email");//prepare query 
$query->execute(array(':email' => $email));//insert variables into query
$res = $query->fetchAll();//get results from that query

//if there are already records of chosen email add text to notice
if($query->rowCount() != 0){
	$_SESSION['notice'] = $_SESSION['notice'].' Email '.$email.' is already in use.';
	$errors = TRUE;
}else{
	$_SESSION['email'] = $email;
	$_SESSION['email_retyped'] = $email_retyped;
}

//if emails don't match add text to notice
if(strcmp($email, $email_retyped) != 0){
	$_SESSION['notice'] = $_SESSION['notice'].' Your emails don\'t match. Please retype emails.';
	unset($_SESSION['email']);
	unset($_SESSION['email_retyped']);	
	$errors = TRUE;
}else{
	$_SESSION['email'] = $email;
	$_SESSION['email_retyped'] = $email_retyped;
}

//if passwords don't match add text to notice
if(strcmp($password, $password_retyped) != 0){
	$_SESSION['notice'] = $_SESSION['notice'].' Your passwords don\'t match. Please retype passwords.';	
	$errors = TRUE;
}

//if password length is less than 8 add text to notice
if(strlen($password) < 8){
	$errors = TRUE;
	$_SESSION['notice'] = $_SESSION['notice'].' Password must be at least 8 characters long!';
}else{
	$_SESSION['password'] = $password;
	$_SESSION['password_retyped'] = $password_retyped;	
}



//if there were no errors insert new user into database else redirect back to register_me page
if(!$errors){
	
	//setup the query to insert new user into the database
	$query = $db->prepare("insert into user (email, password, username) values (:email, :password, :username)");//prepare query 

	$query->bindParam(':email', $email);
	$query->bindParam(':password', md5($password));
	$query->bindParam(':username', $username);

	$query->execute();


	$_SESSION['notice'] = "Your registration is complete. You can now begin to use all features by logging in. You can add more details about yourself by editing your profile page. Enjoy!";
	unset($_SESSION['username']);
	unset($_SESSION['password']);
	unset($_SESSION['password_retyped']);
	unset($_SESSION['email']);
	unset($_SESSION['email_retyped']);
	header('Location:../registerme.php'); 
	exit();
}else{
	header('Location:../registerme.php'); 
	exit();
}
?>