<?php
session_start();

include_once 'databaseConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    $confirm_code = $_POST['confirm_code'];
    $id = $_POST['id'];

    $_SESSION['loggedin'] = TRUE;
    $_SESSION['id'] = $id;
} 

if (isset($_SESSION['loggedin'], $_SESSION['id'])) {

    if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id = $_SESSION['id'];

    $stmt = $con->prepare('SELECT verifyCode, verified, emailVerifiedDate FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($verifyCode,$verified,$emailVerifiedDate);
    $stmt->fetch();

    if($verified == 1){
        header('Location: index.html?verified=1');
        exit();
    }

    if (isset($_POST['confirm_code'])) {   
        $confirmationCode = $_POST['confirm_code'];

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
    }else {
        exit("No confirmation code!");
    }
}
    include('confirm.html');
} else {
    exit("Session data not found");
}
?>