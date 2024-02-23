<?php

session_start();

if(!isset($_SESSION['loggedin'], $_SESSION['id'])){
    header('Location: index.php');
    exit('invalid session');
}

if($_SESSION['loggedin'] != true){
    header('Location: index.php');
    exit('invalid session');
}

if($_SESSION['id'] == 1){
    header('location: adminHome.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="confirmed()">
    <div>
    <h1>Home: <a href="requestEvaluationHTML.php">Request Evaluation</a></h1>
    </div>
   <div class="Login-Cont">

        <h1> You have successfully logged in </h1>

    </div>

    <script>
        
        function confirmed() {

        const urlParams = new URLSearchParams(window.location.search);
        const posted = urlParams.get('posted');
        
        if (posted === '1') {
            alert('Your account has posted an evaluation');
            urlParams.delete('posted');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }

        }

    </script>
</body>
</html>
