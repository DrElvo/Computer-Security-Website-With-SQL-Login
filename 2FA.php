<?php

session_start();

include_once 'databaseConnect.php';

if(!isset($_SESSION['sessionToken'], $_POST['sessionToken']) || $_SESSION['sessionToken'] != $_POST['sessionToken']){
    header('Location: index.php');
    exit('Invalid Session');
}

if (isset($_POST['authenticator_code'])) {   
    $authenticator_code_post = $_POST['authenticator_code'];
    $authenticator_code_session = $_SESSION['authenticator_code_auth'];
    if (hash_equals($authenticator_code_post, $authenticator_code_session)) {
        $_SESSION['loggedin'] = TRUE;
        session_regenerate_id(true);
        header('Location: home.php');
        exit();
    } else {
        header('Location: 2FAHTML.php?incorrect=1');
        exit();
    }
}else {
    exit("No authorisation code!");
}

session_unset();
session_destroy();

?>