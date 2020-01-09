<?php
    include 'ctf_env.php'; 
    
    // Init PDO for the whole script
    $host = "webserver_mysql";
    $dbname="dbctf";
    $user="ctfuser";
    $charset = 'utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $mysqli_pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset",$user, $passwd,$options);
    } catch (PDOException $e) {
        print "DB Connect failed: " . $e->getMessage() . "<br/>";
        die();
    }


    
?>