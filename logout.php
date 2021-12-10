<?php
   session_start();
   unset($_SESSION['user_id']);
   unset($_SESSION['user_name']);
   unset($_SESSION['email']);
   //echo 'You have cleaned session';
   echo 'Please Wait...';
   header('Refresh: 2; URL = index.php');
   
?>