<?php
    require_once 'conf.php';

    global $mailconn;
    $mailconn = new mysqli($maildb["hostname"], $maildb["username"], $maildb["password"], $maildb["table"]);
    mysqli_set_charset($mailconn, 'utf8mb4');

    if(!$mailconn)
        die('Cannot established connection with database: ' . mysqli_connect_error());
    
    date_default_timezone_set('Asia/Bangkok');

?>