<?php

require 'email.php';
require 'vendor/autoload.php';
require_once 'IPblock.php';

#CHECK BLOCKED IPS (this is a manual process, but an implementation could include and automatic blocker of ips)

blockIP();

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

if(!isset($_SESSION['sessionToken'], $_POST['sessionToken']) || $_SESSION['sessionToken'] != $_POST['sessionToken']){
    header('Location: index.php');
    exit('Invalid Session');
}

if ( !isset($_POST['username_login'], $_POST['password_login']) ) {
    header('Location: LoginHTML.php?incorrect=2');
	exit();
}

$username = htmlspecialchars(trim($_POST['username_login']));

if($username == 'admin'){
    $stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();  
    $stmt->bind_result($id, $password);
    $stmt->fetch();
    if (password_verify(trim($_POST['password_login']), $password)) {
        session_regenerate_id();
        $_SESSION['id'] = $id;
        $_SESSION['loggedin'] = TRUE;
        header('Location: adminHome.php');
        exit();
    }
    header('Location: loginHTML.php?incorrect=1');
    exit();
}

if ($stmt = $con->prepare('SELECT id, password, encryptedEmail, verified, iv, verifiedExpiry, lockout, passwordLockoutCount FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $encryptedEmail, $verified, $iv, $verifiedExpiry, $lockout, $lockCount);
        $stmt->fetch();
        $time = date('Y-m-d H:i:s'); 

        if ($lockout >= $time){
            header('Location: index.php?lockout=1');
            exit();
        }
        
        if (password_verify(trim($_POST['password_login']), $password)) {
            session_regenerate_id();
            $secretKey = 'ASuperSecretKey';
            $email = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $secretKey, 0, $iv);
            
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

                    header('Location: index.php?expired=1');
                    exit();
                }
                $_SESSION['attemptLogin'] = true;
                header('Location: confirmHTML.php');
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

                $_SESSION['attemptLogin'] = true;

                header('Location: 2FAHTML.php');
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
                header('Location: index.php?lockout=1');
                exit();
            }

            $stmt = $con->prepare('UPDATE accounts SET passwordLockoutCount = ? WHERE username = ?');
            $stmt->bind_param('ss', $lockCount, $username);
            $stmt->execute();

            header('Location: loginHTML.php?incorrect=1');
            exit();
        }
    } else {
        header('Location: loginHTML.php?incorrect=1');
        exit();
    }
    
    $stmt->close();
}else{
    header('Location: index.php');
    exit();
}
?>