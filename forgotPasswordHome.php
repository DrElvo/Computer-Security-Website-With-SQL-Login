<?php

require 'email.php';
require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK SIGNUP CREDENTIALS

if(!isset($_SESSION['sessionToken'], $_POST['sessionToken']) || $_SESSION['sessionToken'] != $_POST['sessionToken']){
    header('Location: index.php');
    exit('Invalid Session');
}

if (!isset($_POST['email'])) {
    exit('Please fill in email field');
}

$email = $_POST['email'];
$secretKey = 'ASuperSecretKey';

$check_stmt = $con->prepare('SELECT id, encryptedEmail, iv FROM accounts');

if ($check_stmt) {
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        $id = $row['id'];
        $encryptedEmail = $row['encryptedEmail'];
        $iv = $row['iv'];

        $decryptedEmail = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $secretKey, 0, $iv);

        if ($decryptedEmail === $email) {

            if ($stmt = $con->prepare('SELECT username FROM accounts WHERE encryptedEmail = ?')) {
                $stmt->bind_param('s', $encryptedEmail);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($username);
                    $stmt->fetch();

                    $passToken = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

                    $stmt = $con->prepare('UPDATE accounts SET passwordToken = ? WHERE encryptedEmail = ?');
                    if (!$stmt) {
                        header('Location: index.php');
                        exit();
                    }

                    $stmt->bind_param('ss', $passToken, $encryptedEmail);

                    if ($stmt->execute()) {
                        $subject = 'Password Reset Link';
                        $body = "http://localhost/PHP/forgotPasswordInput.php?linked=1&id=$id&passToken=$passToken";
                        sendEmail($email, $username, $subject, $body);
                    } else {
                        exit('Error in SQL statement: ' . $con->error);
                    }

                    }
                    header('Location: index.php?resetLink=1');
                        exit();
                }

            }
        }
} else {
    header('Location: index.php');
    exit();
}


