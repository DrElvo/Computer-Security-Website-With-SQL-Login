<?php

function blockIP() {
    $exampleBlackIPs = [
        '0.0.0.0',
        '1.0.0.0',
        '1.1.0.0'
    ];

    $IP = $_SERVER['REMOTE_ADDR'];

    if (in_array($IP, $exampleBlackIPs)) {
        header('location: index.php');
        exit('IP found in blacklist');
    }

}

?>