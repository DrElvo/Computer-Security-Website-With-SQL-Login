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

if (!isset($_POST['username_signup'], $_POST['password_signup'], $_POST['email'], $_POST['phoneNumber'], $_POST["confirm_password"])) {
    exit('Please fill all fields available');
}

$username = $_POST['username_signup'];
$email = $_POST['email'];
$password = $_POST["password_signup"];
$password_confirm = $_POST["confirm_password"];
$phoneNumber = $_POST['phoneNumber'];

if ($password_confirm != $password){
    header('location: Signup.html?passfail=1');
    exit();
}

$check_query = "SELECT username, email FROM accounts WHERE username = ? OR email = ?";
$check_stmt = $con->prepare($check_query);

if ($check_stmt) {
    $check_stmt->bind_param('ss', $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result) {
        if ($check_result->num_rows > 0) {
            $existing_data = $check_result->fetch_assoc();
            if ($existing_data['username'] === $username) {
                exit('Username already exists. Please choose a different username.');
            } elseif ($existing_data['email'] === $email) {
                exit('Email already linked.');
            }

        } else {

            $stmt = $con->prepare('INSERT INTO accounts (username, password, email, phoneNumber, verifyCode) VALUES (?, ?, ?, ?, ?)');
            if (!$stmt) {
                exit('Error in SQL statement');
            }

            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $hashed_password = password_hash($_POST['password_signup'], PASSWORD_DEFAULT);
            $stmt->bind_param('sssss', $username, $hashed_password, $email, $phoneNumber, $verification_code);

            if ($stmt->execute()) {
        
                $stmt->close();
                $stmt = $con->prepare('SELECT id FROM accounts WHERE username = ?');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id);
                $stmt->fetch();
                
                $subject = 'Email_Verification';
                $body = '<p>Your verification code is:    <b style="font-size: 30px;">' . $verification_code. '</b></p>' . 

                "http://localhost/PHP/confirm.php?linked=1&id=$id&verificationCode=$verification_code";

                sendEmail($email, $username, $subject, $body);

                $verifiedExpiry = date('Y-m-d H:i:s', strtotime('+240 seconds'));

                $stmt = $con->prepare('UPDATE accounts SET verifiedExpiry = ? WHERE username = ?');
                $stmt->bind_param('ss', $verifiedExpiry, $username);
                $stmt->execute();
                
                $stmt->close();        
                $con->close();
            
                // Close the session and then redirect
                session_unset();
                session_destroy();
            
                header('Location: index.html?signedup=1');
                exit();
            } else {
                exit('Error executing query: ' . $stmt->error);
            }
        }
    }
    $check_stmt->close();
} else {
    exit('Error in SQL statement');
}
?>