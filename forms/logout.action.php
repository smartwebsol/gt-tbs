<?php 
  include('../includes/config.inc.php');

  unset($_SESSION['teacherData']);
  header('location:'._URL.'index.php');
?>