<?php
unset($_SESSION['comp_entry_id']);
unset($_SESSION['disc_id']);
unset($_SESSION['scrambles']);

header('Location:../competition.php');
exit();
?>