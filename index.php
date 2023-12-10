<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="check()">

    <div class="Login-Cont">
        <h1> <a href="loginHTML.php">Login</a> <a href="signupHTML.php">Signup</a> <a href="forgotPasswordHomeHTML.php">ForgotPassword</a></h1>
    </div>

    <script>
        
        function check() {
        //CHECKS WHETHER OR NOT THERE IS A TAG IN THE URL WHICH INDICATES THAT THE USER HAS JUST SIGNED UP, AND BEEN REDIRECTED BACK TO THE LOGIN PAGE

        const urlParams = new URLSearchParams(window.location.search);
        const signedup = urlParams.get('signedup');
        const verified = urlParams.get('verified');
        const resetLink = urlParams.get('resetLink');
        const expired = urlParams.get('expired');
        const lockout = urlParams.get('lockout');
        const success = urlParams.get('confirmed'); 

        if (success === '1') {
            alert('Your account has been confirmed');
            urlParams.delete('confirmed');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (signedup === '1') {
            alert('Your account has been signed up');
            urlParams.delete('signedup');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (verified === '1') {
            alert('Your account is already verified');
            urlParams.delete('verified');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (resetLink === '1') {
            alert('If an account with this email is found, a reset link will have been sent');
            urlParams.delete('verified');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (expired === '1') {
            alert('The verification code for this account has expired, check your email for a new one');
            urlParams.delete('expired');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
        if (lockout === '1') {
            alert('This account has been locked out, try again later');
            urlParams.delete('lockout');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }

        }

    </script>
</body>
</html>

  