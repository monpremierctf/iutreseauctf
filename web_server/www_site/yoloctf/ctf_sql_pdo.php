<?php
    include 'ctf_env.php'; 
    
    try {
        $mysqli_pdo = new PDO('mysql:host=webserver_mysql;dbname=dbctf',"ctfuser", $passwd);
    } catch (PDOException $e) {
        print "DB Connect failed: " . $e->getMessage() . "<br/>";
        die();
    }
?>