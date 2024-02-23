<?php
    session_start();
    $_SESSION['sessionToken'] = bin2hex(random_bytes(32));
    if(!isset($_SESSION['attemptLogin']) || $_SESSION['attemptLogin'] !== true){
        header('Location: index.php');
        exit('Invalid Session');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirm</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body onload="inialisePage()">
    <form action="confirm.php" method="POST">
        <div>
            <section id="login">
                <h1>Confirm code:</h1>
                <input type="hidden" name= "sessionToken" value = "<?php echo isset($_SESSION['sessionToken']) ? htmlspecialchars($_SESSION['sessionToken']) : ''; ?>" >
                <input type="text" id="confirm_code" name="confirm_code" placeholder="Account Verification Code: " oninput='check();' required inputmode="numeric" pattern="[0-9]*" min="0">
                <button type="submit" id="submit">Submit</button>
                <span id='message'></span>
            </section>
        </div>
    </form>
    <script>
        var confirmCode = document.getElementById('confirm_code');

        confirmCode.addEventListener('input', function(event) {
            var code = confirmCode.value.replace(/\D/g, '');
            confirmCode.value = code;
        });

        inialisePage = function(){
            incorrect()
            check()
            
        }

        function incorrect() {
            const urlParams = new URLSearchParams(window.location.search);
            const incorrect = urlParams.get('incorrect');

            if (incorrect === '1') {
                alert('Your confirmation code was incorrect');
                urlParams.delete('incorrect');
                const newURL = window.location.pathname + '?' + urlParams.toString();
                history.replaceState(null, '', newURL);
            }
        }

        function check() {
            var code = confirmCode.value;

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

    </script>
</body>

</html>