<?php

session_start();



//if the user inserted wrong information/missing information we store inserted data to fill form with inserted data and we unset session variables.
if(isset($_SESSION['username']))
	$username = $_SESSION['username'];
else
	$username = '';

unset($_SESSION['username']);


if(isset($_SESSION['email']))
	$email = $_SESSION['email'];
else
	$email = '';

unset($_SESSION['email']);


if(isset($_SESSION['email_retyped']))
	$email_retyped = $_SESSION['email_retyped'];
else
	$email_retyped = '';

unset($_SESSION['email_retyped']);


if(isset($_SESSION['notice']))
	$notice = $_SESSION['notice'];
else
	$notice = '';

unset($_SESSION['notice']);

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
						Register!
					</h2>
					<!--Form for register a new user-->
					<form action = "php/register.php" method = "POST">
						<table>
							<tr>
								<td>
									Username:
								</td>
								<td>
									<input type = "text" name = "username" size = "20" placeholder = "username" value = <?php echo '"'.$username.'"'; ?>>
								</td>
							</tr>
							<tr>
								<td>
									Password:
								</td>
								<td>
									<input type = "password" name = "password" size = "20" placeholder = "password">
								</td>
							</tr>
							<tr>
								<td>
									Retype password:
								</td>
								<td>
									<input type = "password" name = "password_retyped" size = "20" placeholder = "retype password">
								</td>
							</tr>
							<tr>
								<td>
									E-mail:
								</td>
								<td>
									<input type = "email" name = "email" size = "20" placeholder = "e-mail" value = <?php echo '"'.$email.'"'; ?>>
								</td>								
							</tr>
							<tr>
								<td>
									Retype e-mail:
								</td>
								<td>
									<input type = "email" name = "email_retyped" size = "20" placeholder = "retype e-mail" value = <?php echo '"'.$email_retyped.'"'; ?>>
								</td>								
							</tr>
							<tr>
								<td></td>
								<td></td>
							</tr>
						</table>
						</br>
						<p>
							<?php echo $notice; ?>
						</p>
						<br/>
						<input type = "submit" value ="Register me!">
					</form>
				</div>
			</div>
			<br/>
			<hr>
			<div class="row clearfix">
				<div class="col-md-12 column">
					<p>
						Please visit your profile page and edit more information about yourself there.
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
