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

if (!isset($_POST['username_signup'], $_POST['password_signup'], $_POST['email'], $_POST['phoneNumber'], $_POST["confirm_password"])) {
    exit('Please fill all fields available');
}

$username = $_POST['username_signup'];
$email = $_POST['email'];
$password = $_POST["password_signup"];
$password_confirm = $_POST["confirm_password"];
$phoneNumber = $_POST['phoneNumber'];

if ($password_confirm != $password){
    header('location: signupHTML.php?passfail=1');
    exit();
}


//const lengthRegex = /^.{8,}$/; // Checks for a minimum length of 8 characters
//const numberRegex = /\d/;      // Checks for at least one digit
//const specialCharRegex = /[!@#$%^&*()_+[\]{};':"\\|,.<>/?-]/; // Checks for special characters



//if ()



$check_query = "SELECT username FROM accounts WHERE username = ?";
$check_stmt = $con->prepare($check_query);

if ($check_stmt) {
    $check_stmt->bind_param('s', $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result) {
        if ($check_result->num_rows > 0) {
            $existing_data = $check_result->fetch_assoc();
            if ($existing_data['username'] === $username) {
                exit('Username already exists. Please choose a different username.');
            } 

        } else {
            $secretKey = 'ASuperSecretKey';

            $check_query = "SELECT encryptedEmail, iv FROM accounts";
            $check_stmt = $con->prepare($check_query);

            if ($check_stmt) {
                $check_stmt->execute();
                $result = $check_stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $encryptedEmail = $row['encryptedEmail'];
                    $iv = $row['iv'];

                    $decryptedEmail = openssl_decrypt($encryptedEmail, 'aes-256-cbc', $secretKey, 0, $iv);

                    if ($decryptedEmail === $email) {
                        exit('Email already exists.');
                    }
                }
            } else {
                exit('Error in SQL statement: ' . $con->error);
            }
            
            $stmt = $con->prepare('INSERT INTO accounts (username, password, encryptedEmail, encryptedNumber, verifyCode, iv) VALUES (?, ?, ?, ?, ?, ?)');
            if (!$stmt) {
                exit('Error in SQL statement' . $con->error);
            }
            
            
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $hashed_password = password_hash($_POST['password_signup'], PASSWORD_DEFAULT);

            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encryptedEmail = openssl_encrypt($email, 'aes-256-cbc', $secretKey, 0, $iv);
            $encryptedNumber = openssl_encrypt($phoneNumber, 'aes-256-cbc', $secretKey, 0, $iv);

            $stmt->bind_param('ssssss', $username, $hashed_password, $encryptedEmail, $encryptedNumber, $verification_code, $iv);

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
            
                header('Location: index.php?signedup=1');
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