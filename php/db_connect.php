<?php
function db_conn()
{
	//3346
	// default non persistant connection should close automatically
	// if we want to close beforehand, call mysqli_close();
	$mysqli = new mysqli("localhost", "89111190", "z1p2o3i4", "npb2015_vb");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	return $mysqli;
}

function db_close($mysqli)
{
	mysqli_close($mysqli);
}

function db_conn_pdo()
{
	// does not close automatically
	try {
	   $mysqlPDO = new PDO('mysql:host=localhost;dbname=npb2015_VB', '89111190', 'z1p2o3i4', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));;
		return $mysqlPDO;
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
}

?>