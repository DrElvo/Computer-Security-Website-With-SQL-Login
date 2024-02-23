<?php

require 'vendor/autoload.php';

#INITIALISE CAPTCHA

include_once 'captcha.php';

#INITIALISE SESSION

session_start();

#INITIALISE DATABASE

include_once 'databaseConnect.php';

#CHECK LOGIN CREDENTIALS

if(!isset($_SESSION['sessionToken'], $_POST['sessionToken']) || $_SESSION['sessionToken'] != $_POST['sessionToken']){
    header('Location: index.php');
    exit('Invalid Session');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["loggedin"])){

    $postedOn = date('Y_m_d_H_i_s');
    $id = $_SESSION['id'];
    $contactType = htmlspecialchars(trim($_POST['contact']));
    $comment = htmlspecialchars(trim($_POST['comment']));

    $target_dir = "uploads/";
    $originalFileName = $_FILES["fileToUpload"]["tmp_name"];
    $fileName = $target_dir . 'id_' . $id . '_time_' . $postedOn . '_file_' . basename($_FILES["fileToUpload"]["name"]);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

    if(isset($_POST["submit"])) {
        $check = getimagesize($originalFileName);
        if($check === false) {
            exit("File is not an image.");
        }
    }

    if (file_exists($fileName)) {
        exit("Sorry, file already exists.");
    } else if ($_FILES["fileToUpload"]["size"] > 5000000) {
        exit( "Sorry, your file is too large.");
    } else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        
        exit("Sorry, only JPG, JPEG, PNG & GIF files are allowed. " . $imageFileType . '    ' . $fileName);
    } 

    if($contactType == 'email'){
        $stmt = $con->prepare('SELECT encryptedEmail, iv FROM accounts WHERE id = ?');
    } else if ($contactType == 'phone'){
        $stmt = $con->prepare('SELECT encryptedNumber, iv FROM accounts WHERE id = ?');
    } else {
        exit('error: invalid contact');
    }
    if (!$stmt) {
        exit('error: SQL statement');
    }
    $stmt->bind_param('s', $id);
    if (!$stmt->execute()) {
        exit('Error in SQL statement: ' . $con->error);
    }

    $stmt->bind_result($encryptedContact, $iv);
    $stmt->fetch();
    $stmt->close();

    $secretKey = 'ASuperSecretKey';
    $contact = openssl_decrypt($encryptedContact, 'aes-256-cbc', $secretKey, 0, $iv);

    $stmt = $con->prepare('INSERT INTO comments (id, comment, contact, contactType, postedOn, nameOfFile) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        exit('Error in SQL statement: ' . $con->error);
    }

    $stmt->bind_param('ssssss', $id, $comment, $contact, $contactType, $postedOn, $fileName);
    if (move_uploaded_file($originalFileName, $fileName)) {
        if ($stmt->execute()) {
            $stmt->close();
            if($id == 1){
                header('Location: adminHome.php');
                exit();
            }
        }
        else {
            exit('Error executing statement: ' . $stmt->error);
        }
        header('Location: home.php?posted=1');
        exit();
    } else {
        exit('Error in file upload');
    }

} else {
    header('Location: index.php');
    exit('Not Logged In');
}

?>

