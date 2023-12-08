<?php

require 'email.php';
require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

if ($_SESSION['sessionToken'] != $_POST['sessionToken']){
    exit('Invalid Session');
}

if ( !isset($_POST['username_login'], $_POST['password_login']) ) {
    header('Location: Login.html?incorrect=2');
	exit();
}

$username = $_POST['username_login'];

if($username == 'admin'){
    $stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();  
    $stmt->bind_result($id, $password);
    $stmt->fetch();
    if (password_verify($_POST['password_login'], $password)) {
        session_regenerate_id();
        $_SESSION['id'] = $id;
        $_SESSION['loggedin'] = TRUE;
        header('Location: adminHome.php');
        exit();
    }
}

#PREPARE SQL STATEMENT

if ($stmt = $con->prepare('SELECT id, password, email, verified, verifiedExpiry, lockout, passwordLockoutCount FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $email, $verified, $verifiedExpiry, $lockout, $lockCount);
        $stmt->fetch();
        $time = date('Y-m-d H:i:s'); 

        if ($lockout >= $time){
            header('Location: index.html?lockout=1');
            exit();
        }
        
        if (password_verify($_POST['password_login'], $password)) {
            session_regenerate_id();
            
            $_SESSION['id'] = $id;
            if($verified == 0){
                if ($verifiedExpiry <= $time){
                    
                    $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                    $verifiedExpiry = date('Y-m-d H:i:s', strtotime('+240 seconds'));
                    $subject = 'Email_Verification';
                    $body = '<p>Your verification code is:    <b style="font-size: 30px;">' . $verification_code. '</b></p>' . 

                    "http://localhost/PHP/confirm.php?linked=1&id=$id&verificationCode=$verification_code";
                    
                    sendEmail($email, $username, $subject, $body);

                    $stmt = $con->prepare('UPDATE accounts SET verifiedExpiry = ?, verifyCode = ? WHERE username = ?');
                    $stmt->bind_param('sss', $verifiedExpiry, $verification_code, $username);
                    $stmt->execute();

                    header('Location: index.html?expired=1');
                    exit();
                    
                }
                header('Location: confirm.html');
                exit();

            }else{

                $lockCount = 0;
                $stmt = $con->prepare('UPDATE accounts SET passwordLockoutCount = ? WHERE username = ?');
                $stmt->bind_param('ss',$lockCount, $username);
            
                $authenticator_code_auth = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $_SESSION['authenticator_code_auth'] = $authenticator_code_auth;
                $subject = '2FA Code';
                $body = '<p>Your authentication code is:    <b style="font-size: 30px;">' . $authenticator_code_auth. '</b></p>';

                sendEmail($email, $username, $subject, $body);

                header('Location: 2FA.html');
                exit();
     
            }
        } else {

            $lockCount = $lockCount + 1;

            if($lockCount >= 3){
                $lockCount = 0;

                $lockout = date('Y-m-d H:i:s', strtotime('+240 seconds'));
                
                $stmt = $con->prepare('UPDATE accounts SET passwordLockoutCount = ?, lockout = ? WHERE username = ?');
                $stmt->bind_param('sss',$lockCount, $lockout, $username);
                $stmt->execute();
                header('Location: index.html?lockout=1');
                exit();
            }

            $stmt = $con->prepare('UPDATE accounts SET passwordLockoutCount = ? WHERE username = ?');
            $stmt->bind_param('ss', $lockCount, $username);
            $stmt->execute();

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