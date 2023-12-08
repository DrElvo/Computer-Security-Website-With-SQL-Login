<?php

session_start();

require 'email.php';
include_once 'databaseConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" ){
    
    if (isset($_POST['id'])) {

        $confirm_code = $_POST['confirm_code'];
        $id = $_POST['id'];

        $_SESSION['loggedin'] = TRUE;
        $_SESSION['id'] = $id;
    } 

    if (isset($_SESSION['loggedin'], $_SESSION['id'])) {
        $id = $_SESSION['id'];
        if (isset($_POST['confirm_code'])) {   
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
    header('index.html');
    exit();
}

$stmt = $con->prepare('SELECT email, username, verifyCode, verified, verifiedExpiry FROM accounts WHERE id = ?');
if (!$stmt) {
    die('Error in SQL query: ' . $con->error);
}

$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($email,$username,$verifyCode,$verified,$verifiedExpiry);
$stmt->fetch();

if($verified == 1){
    header('Location: index.html?verified=1');
    exit();
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

    header('Location: index.html?expired=1');
    exit();

}

if ($confirmationCode == $verifyCode) {
    $update_stmt = $con->prepare('UPDATE accounts SET verified = ? WHERE id = ?');
    $verified = 1;
    $update_stmt->bind_param('is', $verified, $id);

    if ($update_stmt->execute()) {
        $update_stmt->close();
        header('Location: home.html?confirmed=1');
        exit();

    } else {
        exit('Error updating verification status: ' . $update_stmt->error);
    }

} else {
    header('Location: confirm.html?incorrect=1');
    exit();
}


?>