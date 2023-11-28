<?php
session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'hci_login';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ( !isset($_POST['username'], $_POST['password'], $_POST['email']) ) {
	exit('Please fill all fields available');
}

$username = $_POST['username'];

$email = $_POST['email'];

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
            
            $stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)');
            if (!$stmt) {
                exit('Error in SQL statement');
            }

            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt->bind_param('sss', $username, $hashed_password, $email);
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