<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'hci_login';

/*
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'id21600928_th443';
$DATABASE_PASS = 'Password1%';
$DATABASE_NAME = 'id21600928_accounts';
*/

#INITIALISE DATABASE

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

#INITIALISE CAPTCHA

$reCaptchaSecretKey = '6LdrRiIpAAAAAPp850fkuM1Hz7UgxifNGt7tX3Hk';

if (!isset($_POST['token']) || empty($_POST['token'])) {
    $token = $_POST['token'];
    echo 'Debugging Token: ' . $token . '<br>';
    exit('reCAPTCHA token missing or empty');
}

$token = $_POST['token'];

$url = 'https://www.google.com/recaptcha/api/siteverify';
$response = file_get_contents($url . '?secret=' . $reCaptchaSecretKey . '&response=' . $token);

$result = json_decode($response);

if (!$result->success) {
    exit('reCAPTCHA verification failed: ' . $response);
}

#CHECK SIGNUP CREDENTIALS

if (!isset($_POST['username_signup'], $_POST['password_signup'], $_POST['email'], $_POST['phoneNumber'])) {
    exit('Please fill all fields available');
}

$username = $_POST['username_signup'];
$email = $_POST['email'];
$password = $_POST["password_signup"];
$phoneNumber = $_POST['phoneNumber'];

$mail = new PHPMailer(true);

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

            try {

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

            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            $mail->Subject = 'Email_Verification';
            $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' .
            $verification_code. '</b></p>';

            $mail->send();

            } catch (Exception $e) {

                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

            }

            $stmt = $con->prepare('INSERT INTO accounts (username, password, email, phoneNumber, verifyCode) VALUES (?, ?, ?, ?, ?)');
            if (!$stmt) {
                exit('Error in SQL statement');
            }

            $hashed_password = password_hash($_POST['password_signup'], PASSWORD_DEFAULT);
            $stmt->bind_param('sssss', $username, $hashed_password, $email, $phoneNumber, $verification_code);

            if ($stmt->execute()) {
                $stmt->close();
                $con->close();
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