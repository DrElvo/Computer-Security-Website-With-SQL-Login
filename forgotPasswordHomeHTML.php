<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password Email</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx"></script>
</head>

<body onload="check()">

<section id="forgotPassword">
    <h1><a href="loginHTML.php">Login</a><a href="signupHTML.php">Signup</a> <a href="index.php">Home</a></h1>
    <form action="forgotPasswordHome.php" method="POST">
                <label for="email"> Please enter your account email </label> 
                <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? htmlspecialchars($_SESSION['sessionToken']) : ''; ?>" >
                <input type="email" id="email" name="email" placeholder="Email" onkeyup='check();' required>
                <button type="submit" id="submit" onclick="getRecaptchaToken()">Submit</button>
                <span id='message'></span>
    </form>
    </section>
    </body>

    <script>

        function getRecaptchaToken() {
            grecaptcha.ready(function () {
                grecaptcha.execute('6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx', { action: 'submit' }).then(function (token) {
                    formAction = 'login.php'

                    document.getElementById('token').value = token;
                    document.getElementById('submit_login').formAction = formAction;
                    document.getElementById('submit_login').form.submit();
                });
            });
        }

        function check() {

            var username = document.getElementById('email').value;

            if (username === '') {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'empty fields';
                document.getElementById('submit').disabled = true;
            } else {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = 'fields full';
                document.getElementById('submit').disabled = false;
            }
        }

    </script>


</html>