<?php

session_start();
require('php/db_connect.php');

$db=db_conn_pdo();//connect to database

//check if any one is logged in
if(!isset($_SESSION['user_logged_in']))
	die('Please log in!');
//check for comp id and disc id
if(!isset($_GET['comp_id']) || !isset($_GET['disc_id'])){
	die('Please enter competition from competition site.');
}

//check if user has competition entry
$query = $db->prepare("SELECT discipline_id, user_user_id, competition_id_competition
FROM (SELECT * 
FROM competition_entry_has_discipline_has_result
INNER JOIN user_has_competition_entry ON competition_entry_has_discipline_has_result.competition_entry_id = user_has_competition_entry.competition_entry_competition_entry_id) as bla
INNER JOIN competition_entry
ON bla.competition_entry_id = competition_entry.competition_entry_id
WHERE user_user_id = :user_user_id AND discipline_id = :discipline_id AND competition_id_competition = :competition_id_competition
");//prepare query 


$query->bindParam(':user_user_id', $_SESSION['user_logged_in_id']);
$query->bindParam(':discipline_id', $_GET['disc_id']);
$query->bindParam(':competition_id_competition', $_GET['comp_id']);
$query->execute();//insert variables into query
$query->fetchAll();//get results from that query


if($query->rowCount() != 0){
	die('You already entered this competition with choosen discipline');
}

//insert competition_entry
$query = $db->prepare("INSERT INTO competition_entry (competition_id_competition) values (:comp_id)");
$query->bindParam(':comp_id', $_GET['comp_id']);
$query->execute();
$comp_entry_id = $db->lastInsertId();


//insert user_has_competition_entry
$query = $db->prepare("INSERT INTO user_has_competition_entry (user_user_id, competition_entry_competition_entry_id) values (:user_user_id, :competition_entry_id)");
$query->bindParam(':user_user_id', $_SESSION['user_logged_in_id']);
$query->bindParam(':competition_entry_id', $comp_entry_id);
$query->execute();



//get discipline name of discipline id
$query = $db->prepare("select name from discipline where discipline_id = :discipline_id");//prepare query 
$query->execute(array(':discipline_id' => $_GET['disc_id']));//insert variables into query
$res_dis = $query->fetchAll();//get results from that query


//get scrambles for competition id and discipline id
$query = $db->prepare("SELECT * FROM (SELECT competition_id, discipline_id, algorithm.algorithm_id, algorithm
FROM competition_has_discipline_has_algorithm
INNER JOIN algorithm
ON competition_has_discipline_has_algorithm.algorithm_id=algorithm.algorithm_id) as bla
WHERE competition_id = :competition_id AND discipline_id = :discipline_id");//prepare query 

$query->bindParam(':discipline_id', $_GET['disc_id']);
$query->bindParam(':competition_id', $_GET['comp_id']);
$query->execute();//insert variables into query
$res_scrambles = $query->fetchAll();//get results from that query

$_SESSION['comp_entry_id'] = $comp_entry_id;
$_SESSION['disc_id'] = $_GET['disc_id'];
$_SESSION['scrambles'] = $res_scrambles;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rubik portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

	<!--link rel="stylesheet/less" href="less/bootstrap.less" type="text/css" /-->
	<!--link rel="stylesheet/less" href="less/responsive.less" type="text/css" /-->
	<!--script src="js/less-1.3.3.min.js"></script-->
	<!--append ‘#!watch’ to the browser URL, then refresh the page. -->
	
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="img/favicon.png">
  
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
	<script type="text/javascript">

	<?php

	function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\'\-]/', '', $string); // Removes special chars.
		$string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
		return $string;
	}
	
	$tmp = 'var scrambles =[';
	foreach($res_scrambles as $value){
		$tmp = $tmp.'"'.clean($value['algorithm']).'",';
	}
	$tmp = substr($tmp, 0,-1);
	$tmp = $tmp.'];';
	echo $tmp;
	?>
	
	</script>

	<script type="text/javascript" src="js/competition_timer.js"></script>


</head>

<body onload="addScramble()">
<div class="container">
	<div id = "logo" class="row clearfix">
		<div class="col-md-12 column">
			<!-- logo -->
		</div>
	</div>
	<div id = "main" class="row clearfix">
		<div id = "menu" class="col-md-3 column">
			<h2>
				Menu
			</h2>
			<ul style = "list-style-type: none;">
				<li><a href="registerme.php">Register me</a></li>
				<li><a href="timer.php">Timer</a></li>
				<!-- <li><a href="market.php">Market</a></li> -->
				<!-- <li><a href="algorithm.php">Algorithm</a></li> -->
				<!--only print add competition option if moderator/administrator is logged in -->
				<?php 
					if(isset($_SESSION['user_logged_in']))
						echo '<li><a href="competition.php">Competition</a></li>';
					if(isset($_SESSION['rights'])){
						if($_SESSION['rights'] == 2 or $_SESSION['rights'] == 3) 
							echo '<li><a href="add_competition.php">Add competition</a></li>';
					}
				?>
			</ul>
			<br/>
		</div>
		<div id = "center" class="col-md-9 column">
			<div class="row clearfix" >
				<div class="col-md-12 column">
					<br/>
					<!--Form for searching user profiles-->
					<div id = "isci_uporabnika">
						<form action = "php/search_username.php" method = "GET">
							<input type ="text" name = "username" size = "10" placeholder = "username">
							<input type = "submit" value ="Search">
						</form>
						<?php 
							if(isset($_SESSION['no_such_username'])){
								echo $_SESSION['no_such_username'];
								unset($_SESSION['no_such_username']);
							}
						?>
					</div>
					<!--Form for login-->
					<div id = "prijava">
						<?php
						//if user is logged in print his/her name, else print login form
						if(!isset($_SESSION['user_logged_in'])){
							echo '<form action = "php/login.php" method = "POST"> ';
							echo '<input type = "text" name = "username" size = "10" placeholder = "username"> ';
							echo '<input type = "password" name = "password" size = "10" placeholder = "password"> ';
							echo '<input type = "submit" value ="Log in">';
							echo '</form>';
							if(isset($_SESSION['false_login'])){
								echo $_SESSION['false_login'];
								unset($_SESSION['false_login']);
							}
						}else{
							echo '<a href="php/search_username.php?username='.$_SESSION['user_logged_in'].'">'.$_SESSION['user_logged_in'].'</a>';
							echo '<a href="php/logout.php">   Logout!</a>';
						}


						?>
					</br>
					</div>
				</div>
			</div>
			<hr>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<!--choose puzzle-->
					<div id = "competition_data"></div>
					<div class="col-md-12 column">
						<div class="row clearfix">
							<div class="col-md-9 column">
								<!--timer-->
								<p id = "time">TIME</p>
							</div>
							<div class="col-md-3 column">
								<!--times-->
								<h2>Times:</h2>
								<ul>					
									<div id = "times"></div>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<hr>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<div id = "scramble">
					</div>
					<div id = "statistics">
							<p id = "numOfSolves"><strong>Number of solves: </strong>/</p>
							<p id = "mean"><strong>Mean: </strong>/</p>
							<p id = "mean5"><strong>Mean of 5: </strong>/</p>
							<p id = "mean12"><strong>Mean of 12: </strong>/</p>
							<p id = "best"><strong>Best time: </strong>/</p>
							<p id = "worst"><strong>Worst time: </strong>/</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
