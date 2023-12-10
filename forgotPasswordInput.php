<?php

require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

$secretKey = 'ASuperSecretKey';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if ($_SESSION['sessionToken'] != $_POST['sessionToken']){
        exit('Invalid Session');
    }
    if (!isset($_POST['password'], $_POST["confirm_password"], $_POST["answer"])) {
        exit('Please fill all fields available');
    }
    $id = $_SESSION['id'];
    $password = $_POST["password"];
    $confirm_password = $_POST['confirm_password'];
    $answer = $_POST['answer'];
    $encryptedAnswer = $_SESSION['encryptedAnswer']; 
    $iv = $_SESSION['iv'];

    if ($confirm_password === $password){

        $decryptedAnswer = openssl_decrypt($encryptedAnswer, 'aes-256-cbc', $secretKey, 0, $iv);
        if ($decryptedAnswer !== $answer){
            header('Location: index.php');
            exit('wrong security question answer');
        }

        $stmt = $con->prepare('UPDATE accounts SET password = ?, passwordToken = NULL WHERE id = ?');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('ss', $hashed_password, $id);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: index.php?reset=1');
            exit('your password has been reset');
    
        } else {
            exit('Error updating verification status: ' . $update_stmt->error);
        }

    }else{
        header('location: forgotPasswordInputHTML.php?passfail=1');
        exit('passwords did not match');
    }

} else if (isset($_GET['passToken']) && isset($_GET['id'])) {
    $passToken = $_GET['passToken'];
    $id = $_GET['id'];
    $_SESSION['id'] = $id;
    $stmt = $con->prepare("SELECT passwordToken, encryptedQuestion, encryptedAnswer, iv FROM accounts WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($passwordToken, $encryptedQuestion, $encryptedAnswer, $iv);
    $stmt->fetch();
    if($passToken != $passwordToken){
        header('Location: index.php');
        exit('invalid password token');
    } else { 
    
    $question = openssl_decrypt($encryptedQuestion, 'aes-256-cbc', $secretKey, 0, $iv);   

    $_SESSION['encryptedAnswer'] = $encryptedAnswer;    
    $_SESSION['question'] = $question;
    $_SESSION['iv'] = $iv;

    header('Location: forgotPasswordInputHTML.php');
    exit('ready for password reset');
    }
} else {
    header('Location: index.php');
    exit('not a post and not a valid link');
}
?>