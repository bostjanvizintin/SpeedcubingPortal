<?php

session_start();

require('php/db_connect.php');

$db=db_conn_pdo();//connect to database


$query = $db->prepare("select * from discipline");//prepare query 
$query->execute();//insert variables into query
$res = $query->fetchAll();//get results from that query

$disciplines = $res;


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

</head>

<body>
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
					<h2>
						Search competitions:
					</h2>
					<!--Form for adding a new competition-->
					<form action = "php/show_competitions.php" method = "GET">
						<table>
							<tr>
							</tr>
							<tr>
								<td>
									Disciplines:
								</td>
								<td>
									<?php
									//adding all disciplines as checkboxes to add to competition.
									foreach($disciplines as $value){
										echo $value[1].':';
										echo '<input id = "disciplines" type = "checkbox" name = "'.$value[1].'" value = "'.$value[0].'">';
									}
									?>
								</td>
							</tr>
						</table>
						</br>
						<br/>
						<input type = "submit" value ="Show competitions">
					</form>
				</div>
			</div>
			<br/>
			<hr>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<h2>
						Competitions:
					</h2>
					<div id = "competitions">
						<?php
							if (isset($_SESSION['all_competitions'])){
								foreach ($_SESSION['all_competitions'] as $key => $value) {
									echo '<div class = "discipline">';
									echo '<strong>Name: </strong>'.ucfirst($value['name']);
									echo '<br>';
									echo '<strong>Start date: </strong>'.$value['start_date'];
									echo '<br>';
									echo '<strong>End date: </strong>'.$value['end_date'];
									echo '<br>';
									echo '<strong>Disciplines:</strong>';
									foreach ($value['discipline_id'] as $val) {
										echo '<a href = "competition_entry.php?comp_id='.$value['competition_id'].'&disc_id='.$val.'">';
										echo get_discipline_name($disciplines, $val);
										echo '<img class  = "discipline_picture" src="img/competition_cubes/'.get_discipline_name($disciplines, $val).'.jpg" style="width:30px;height:30px;">';
										echo '</a>';
									}
									echo "</div>";
								}
								unset($_SESSION['all_competitions']);
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>

<?php
	
	function get_discipline_name($disciplines, $id){
		foreach($disciplines as $value){
			if($value['discipline_id'] == $id)
				return $value['name'];
		}
	}

?>
