<?php
session_start();

include_once 'databaseConnect.php';
#INITIALISE CAPTCHA

$reCaptchaSecretKey = '6LdrRiIpAAAAAPp850fkuM1Hz7UgxifNGt7tX3Hk';

if (!isset($_POST['token']) || empty($_POST['token'])) {
    exit('reCAPTCHA token missing or empty');
}

$token = $_POST['token'];

$url = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($url . '?secret=' . $reCaptchaSecretKey . '&response=' . $token);
$result = json_decode($response);

if (!$result->success) {
    exit('reCAPTCHA verification failed: ' . $response);
}

#CHECK LOGIN CREDENTIALS

if ( !isset($_POST['username_login'], $_POST['password_login']) ) {
    header('Location: confirm.php');
	exit('Please fill both the username and password fields!');
}

#PREPARE SQL STATEMENT

if ($stmt = $con->prepare('SELECT id, password, verified, emailVerifiedDate FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username_login']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $verified, $emailVerifiedDate);
        $stmt->fetch();
        if (password_verify($_POST['password_login'], $password)) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['id'] = $id;
            
            if($verified == 0){
                header('Location: confirm.php');
                exit();
            }else{
                header('Location: Home.html');
                exit();
            }
        } else {
            header('Location: Login.html?incorrect=1');
            exit();
        }
    } else {
        header('Location: Login.html?incorrect=1');
        exit();
    }
    
    $stmt->close();
}
?>