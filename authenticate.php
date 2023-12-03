<?php
session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'hci_login';

/*
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'id21600928_th443';
$DATABASE_PASS = 'Password1%';
$DATABASE_NAME = 'id21600928_accounts';
*/

#INITIALISE DATABSE

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

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
	exit('Please fill both the username and password fields!');
}

#PREPARE SQL STATEMENT

if ($stmt = $con->prepare('SELECT id, password, verifyCode, emailVerifiedDate FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username_login']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $verifyCode, $emailVerifiedDate);
        $stmt->fetch();
        if (password_verify($_POST['password_login'], $password)) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username_login'];
            $_SESSION['verifyCode'] = $verifyCode;
            $_SESSION['id'] = $id;
            echo 'Welcome ' . $_SESSION['name'];
            if($emailVerifiedDate == null){
                header('Location: confirm.php');
                exit();
            }
        } else {
            echo 'Incorrect password';
        }
    } else {
        echo 'Incorrect username and/or password';
    }
    
    $stmt->close();
}
?>