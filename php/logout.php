<?php
session_start();
session_unset();
session_destroy();


header('Location:http://www.student.famnit.upr.si/~89111190'); /* Redirect browser */
exit();
?>