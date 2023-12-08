<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

if ($_SERVER["REQUEST_METHOD"] == "POST" ){
    if (!isset($_POST['password'], $_POST["confirm_password"])) {
        exit('Please fill all fields available');
    }
    $email = $_SESSION['email'];
    $password = $_POST["password"];
    $confirm_password = $_POST['confirm_password'];

    if ($confirm_password === $password){
        $stmt = $con->prepare('UPDATE accounts SET password = ? WHERE email = ?');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('ss', $hashed_password, $email);
        if ($stmt->execute()) {
            $stmt->close();
            header('Location: index.html?reset=1');
            exit();
    
        } else {
            exit('Error updating verification status: ' . $update_stmt->error);
        }

    }else{
        header('location: forgotPasswordInput.html?passfail=1');
        exit();
    }

} else if (isset($_GET['passToken']) && isset($_GET['email'])) {
    $passToken = $_GET['passToken'];
    $email = $_GET['email'];
    $_SESSION['email'] = $email;
    $stmt = $con->prepare("SELECT passwordToken FROM accounts WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($passwordToken);
    $stmt->fetch();
    
    if($passToken != $passwordToken){
        header('Location: index.html');
        exit();
    }
    header('Location: forgotPasswordInput.html');
    exit();
} else {
    header('Location: index.html');
    exit();
}
?>