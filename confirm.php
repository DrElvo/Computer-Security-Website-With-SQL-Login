<?php
session_start();

if (isset($_SESSION['loggedin'], $_SESSION['name'])) {
    if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_SESSION['name'];

    echo $username;
    
    $verifyCode = $_SESSION['verifyCode'];

    if (isset($_POST['confirm_code'])) {
        $confirmationCode = $_POST['confirm_code'];
        
        // Now you can use $confirmationCode to perform verification or any necessary checks
        // For example:
        if ($confirmationCode == $verifyCode) {
            // Code matches, perform further actions or redirect as needed
            echo "Confirmation code verified!";
            
        } else {
            // Code doesn't match, handle accordingly
            echo "Invalid confirmation code!";
        }
    }
}
    include('confirm.html');
} else {
    echo "Session data not found";
}
?>