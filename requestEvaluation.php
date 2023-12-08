<?php

require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["loggedin"])){
    if (!isset($_POST['comment'], $_POST["contact"])) {
        exit('Please fill all fields available');
    }
    $id = $_SESSION['id'];
    $contactType = $_POST['contact'];
    $comment = $_POST['comment'];


    if($contactType == 'email'){
        $stmt = $con->prepare('SELECT email FROM accounts WHERE id = ?');
    } else if ($contactType == 'phone'){
        $stmt = $con->prepare('SELECT phoneNumber FROM accounts WHERE id = ?');
    } else {
        exit('error: invalid contact');
    }
    if (!$stmt) {
        exit('error: SQL statement');
    }
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->bind_result($contact);
    $stmt->fetch();
    $stmt->close();
   
    $postedOn = date('Y-m-d H:i:s'); 

    $stmt = $con->prepare('INSERT INTO comments (id, comment, contact, contactType, postedOn) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        exit('Error in SQL statement');
    }
    $stmt->bind_param('sssss', $id, $comment, $contact, $contactType, $postedOn);
    $stmt->execute();
    $stmt->close();

    if($id == 1){
        header('Location: adminHome.html');
        exit();
    }

    header('Location: home.html?posted=1');
    exit();

} else {
    header('Location: index.html');
    exit();
}
?>