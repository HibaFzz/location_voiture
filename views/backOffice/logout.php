<?php

session_start();

session_unset();  
session_destroy(); 

header('Location: ../frontOffice/login.php');
exit();
?>
