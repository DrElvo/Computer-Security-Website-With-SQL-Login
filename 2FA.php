<?php

session_start();

include_once 'databaseConnect.php';

if (isset($_POST['authenticator_code'])) {   
    $authenticator_code_post = $_POST['authenticator_code'];
    $authenticator_code_session = $_SESSION['authenticator_code_auth'];

    if ($authenticator_code_post == $authenticator_code_session) {
        $_SESSION['loggedin'] = TRUE;
        header('Location: home.html');
        exit();
    } else {
        header('Location: 2FA.html?incorrect=1');
        exit();
    }
}else {
    exit("No authorisation code!");
}
?>