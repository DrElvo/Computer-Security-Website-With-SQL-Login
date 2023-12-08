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

#CHECK SIGNUP CREDENTIALS

if (!isset($_POST['email'])) {
    exit('Please fill in email field');
}

$email = $_POST['email'];

#PREPARE SQL STATEMENT


if ($stmt = $con->prepare('SELECT username FROM accounts WHERE email = ?')) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username);
        $stmt->fetch();

        try {
            $passToken = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            $stmt = $con->prepare('UPDATE accounts SET passwordToken = ? WHERE email = ?');

            if (!$stmt) {
                exit('Error in SQL statement: ' . $con->error);
            }

            $stmt->bind_param('ss', $passToken, $email);

            if ($stmt->execute()) {
                $mail = new PHPMailer(true);
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'humphrey.tomw@gmail.com';
                $mail->Password = 'eshi jufi eosr jkrp';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('humphrey.tomw@gmail.com', 'adnan-tech.com');
                $mail->addAddress($email, $username);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Link';
                $mail->Body = "http://localhost/PHP/forgotPasswordInput.php?linked=1&email=$email&passToken=$passToken";
                $mail->send();
            }

        
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        
        }
        header('Location: index.html?resetLink=1');
            exit();
    }


