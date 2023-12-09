<?php

session_start();

if(!isset($_SESSION['loggedin'], $_SESSION['id'])){
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Home</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div>
        <h1>Home: <a href="requestEvaluationHTML.php">Request Evaluation</a> <a href="adminAllHTML.php">Admin Access</a> </h1>
    </div>
    <script>
    </script>
</body>
</html>