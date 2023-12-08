<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LdrRiIpAAAAAPNvdZx84VErn6h5RD-E0aPRVbpx"></script>
    <style>
    </style>
</head>

<body onload="confirmed()">
    <div>
    <h1>Home: <a href="requestEvaluation.html">Request Evaluation</a></h1>
    </div>
   <div class="Login-Cont">
    

        <h1> You have successfully logged in </h1>

    </div>

    <script>
        
        function confirmed() {
        //CHECKS WHETHER OR NOT THERE IS A TAG IN THE URL WHICH INDICATES THAT THE USER HAS JUST SIGNED UP, AND BEEN REDIRECTED BACK TO THE LOGIN PAGE

        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('confirmed');
        const posted = urlParams.get('posted');

        if (success === '1') {
            alert('Your account has been confirmed');
            urlParams.delete('confirmed');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }
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
