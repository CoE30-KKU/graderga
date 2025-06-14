<?php
    // place this file in /web/static/functions/mail/conf.php
    
    $maildb = array(
        "hostname" => "",
        "username" => "",
        "password" => "",
        "table" => ""
    );

    $mailsender = array(
        "name"=>"",
        "email"=>"",
        "password"=>""
    );

    $mailsenderData = json_encode($mailsender);
?>