<?php

session_start();

//make array of solves for 3x3 and pass it to js
require('php/db_connect.php');


$db=db_conn_pdo();//connect to database







$query = $db->prepare("SELECT time
FROM(SELECT user_id_user, result_id
FROM user_has_practice
INNER JOIN practice_has_discipline_has_result
ON user_has_practice.practice_id_practice  = practice_has_discipline_has_result.practice_id
WHERE discipline_id = 1) as bla
INNER JOIN result
ON bla.result_id = result.result_id");//prepare query 
$query->execute();//insert variables into query
$res = $query->fetchAll();//get results from that query

$graph_array = array(array('Number of solve', 'Time')); 

$i = 0;
foreach ($res as $value) {
	$tmp = array();
	array_push($tmp, ''.$i++);
	array_push($tmp, intval($value['time']));
	array_push($graph_array, $tmp);
}
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
	  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	    <script type="text/javascript">
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = google.visualization.arrayToDataTable(<?php echo json_encode($graph_array);?>);
	        var options = {
	          title: 'times',
	          legend: { position: 'none' },
	        };

	        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	        chart.draw(data, options);
	      }
	    </script>

</head>
<body onresize = "drawChart()">
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
					<div class="col-md-12 column">
						<div class="row clearfix">
							<!--user data -->
							<h3>Practice times:</h3>
							<div id="chart_div"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<!--user data on the bottom-->
					
				</div>
				<div id = "nextPage">
					<a href = "profile.php">Profile</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
