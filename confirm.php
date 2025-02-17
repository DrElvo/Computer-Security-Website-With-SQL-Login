<?php

session_start();

require 'email.php';
include_once 'databaseConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" ){

    if(!isset($_SESSION['sessionToken'], $_POST['sessionToken']) || $_SESSION['sessionToken'] != $_POST['sessionToken']){
        header('Location: index.php');
        exit('Invalid Session');
    }

    if (isset($_SESSION['attemptLogin'], $_SESSION['id'])) {
        $id = $_SESSION['id'];
        if (isset($_POST['confirm_code']) || $_POST['confirm_code'] !== '') {   
            $confirmationCode = $_POST['confirm_code'];
        }else {
            exit("No confirmation code!");
        }
    } else {
        exit("Session data not found");
    }

} else if (isset($_GET['verificationCode']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $confirmationCode = $_GET['verificationCode']; 
} else {
    header('Location: index.php');
    exit('not a post, not a code');
}

$stmt = $con->prepare('SELECT encryptedEmail, username, verifyCode, verified, verifiedExpiry, iv FROM accounts WHERE id = ?');
if (!$stmt) {
    die('Error in SQL query: ' . $con->error);
}

$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($encryptedEmail,$username,$verifyCode,$verified,$verifiedExpiry,$iv);
$stmt->fetch();

$secretKey = 'ASuperSecretKey';
$email = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $secretKey, 0, $iv);
            
if($verified == 1){
    header('Location: index.php?verified=1');
    exit('account already verified');
}

$time = date('Y-m-d H:i:s'); 

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
    exit('expired link');
}

if ($confirmationCode == $verifyCode) {
    $update_stmt = $con->prepare('UPDATE accounts SET verified = ?, verifyCode = NULL WHERE id = ?');
    $verified = 1;
    $update_stmt->bind_param('is', $verified, $id);

    if ($update_stmt->execute()) {
        $update_stmt->close();
        header('Location: index.php?confirmed=1');
        exit('confirmed account');

    } else {
        exit('Error updating verification status: ' . $update_stmt->error);
    }

} else {
    header('Location: confirmHTML.php?incorrect=1');
    exit('incorrect code');
}


?>