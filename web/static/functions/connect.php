<?php declare(strict_types=1);
    ob_start();
    require_once 'init.php';
    session_start();

    $start_time = microtime(TRUE);
    if (!isset($_SESSION['dark_mode'])) $_SESSION['dark_mode'] = false;

    require_once 'conf.php';
 
    global $conn;
    $conn = new mysqli($db["hostname"], $db["username"], $db["password"], $db["table"]);
    mysqli_set_charset($conn, 'utf8mb4');

    if(!$conn)
        die('Cannot established connection with database: ' . mysqli_connect_error());

    $private_key = md5("THANK YOU OTOG.CF, OTOG.ORG, PROGRAMMING.IN.TH, KIYAGO, GAREDAMI");

    require_once 'function.php';
    
    @ini_set('upload_max_size','128M');
    @ini_set('post_max_size','128M');
    @ini_set('max_execution_time','300');
    
    date_default_timezone_set('Asia/Bangkok');

    header("Access-Control-Allow-Origin: *");
?>
