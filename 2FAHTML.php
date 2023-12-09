<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="inialisePage()">
    <form action="2FA.php" method="POST">
        <div>
            <section id="login">
                <h1>Authenticator code:</h1>
                <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? htmlspecialchars($_SESSION['sessionToken']) : ''; ?>" >
                <input type="text" id="authenticator_code" name="authenticator_code" placeholder="Authenticator Code" oninput='check();' required inputmode="numeric" pattern="[0-9]*" min="0">
                <button type="submit" id="submit" >Submit</button>
                <span id='message'></span>
            </section>
        </div>
    </form>

    <script>
        var authenticatorCode = document.getElementById('authenticator_code');

        authenticatorCode.addEventListener('input', function(event) {
            var code = authenticatorCode.value.replace(/\D/g, '');
            authenticatorCode.value = code;
        });

        inialisePage = function(){
            incorrect()
            check()
        }

        var check = function() {
            var code = authenticatorCode.value;

            if (code.length < 6) {
                document.getElementById('message').style.color = 'red';
                document.getElementById('message').innerHTML = 'Too short';
                document.getElementById('submit').disabled = true;
            } else if (code.length > 6) {
                confirmCode.value = code.slice(0, 6);
            } else {
                document.getElementById('message').style.color = 'green';
                document.getElementById('message').innerHTML = 'Ready';
                document.getElementById('submit').disabled = false;
            }
        }

        function incorrect() {

        const urlParams = new URLSearchParams(window.location.search);
        const incorrect = urlParams.get('incorrect');

        if (incorrect === '1') {
            alert('Your authorisation code was incorrect');
            urlParams.delete('incorrect');
            const newURL = window.location.pathname + '?' + urlParams.toString();
            history.replaceState(null, '', newURL);
        }

        }

    </script>
</body>

</html>