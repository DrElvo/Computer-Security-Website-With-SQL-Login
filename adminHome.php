<?php

require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

if(isset($_SESSION['loggedin'], $_SESSION['id'])){
    header('Location: adminHome.html?');
    exit();
}
?>