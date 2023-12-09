<?php

session_start();

include_once 'databaseConnect.php';

if ($_SESSION['sessionToken'] != $_POST['sessionToken']){
    exit('Invalid Session');
}

if (isset($_POST['authenticator_code'])) {   
    $authenticator_code_post = $_POST['authenticator_code'];
    $authenticator_code_session = $_SESSION['authenticator_code_auth'];

    if (hash_equals($authenticator_code_post, $authenticator_code_session)) {
        $_SESSION['loggedin'] = TRUE;
        header('Location: home.php');
        exit();
    } else {
        header('Location: 2FAHTML.php?incorrect=1');
        exit();
    }
}else {
    exit("No authorisation code!");
}
?>