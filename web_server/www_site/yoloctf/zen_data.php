<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    
    session_start ();
    include 'ctf_env.php'; 
    require_once('db_requests.php');

    function dumpUserCount(){
        include "ctf_sql.php";
        $user_query = "SELECT count(*) as total from users;";
        if ($result = $mysqli->query($user_query)) {
            if ($row = $result->fetch_assoc()) {
                
                echo $row['total'];	
            }
            $result->close();
        }
        $mysqli->close();
    }

    function dumpUserList(){
        header('Content-Type: application/text');
        echo getNbUsers();
    }

    // {"a":1,"b":2,"c":3,"d":4,"e":5}
    // login, passwd, mail, pseudo, UID, status
    function dumpUserListJSON(){
        include "ctf_sql.php";
        $user_query = "SELECT * FROM users;";
        if ($result = $mysqli->query($user_query)) {
            header('Content-Type: application/json');
            echo '[ ';
            $isfirstrow=true;
            while ($row = $result->fetch_assoc()) {
                if (!$isfirstrow) {
                    echo ",";
                } else {
                    $isfirstrow=false;
                }
                echo '{ ';
                echo '"login":"'.htmlspecialchars($row['UID']).'", ';
                echo '"passwd":"'.htmlspecialchars($row['UID']).'", ';
                echo '"mail":"'.htmlspecialchars($row['UID']).'", ';
                echo '"pseudo":"'.htmlspecialchars($row['UID']).'", '; 
                echo '"UID":"'.htmlspecialchars($row['UID']).'", ';
                echo '"status":"'.htmlspecialchars($row['UID']).'" ';
                echo "}\n";
            }
            echo "]\n";
            $result->close();
        }
        $mysqli->close();
    }

    if (isset($_SESSION['login'] )) {
        // $admin from ctf_env.php
        if (($_SESSION['login']=== $admin )) {
                        
            // Datas
            if (isset($_GET['UsersList'])){
                dumpUserListJSON();
                exit;
            }
            // Datas
            if (isset($_GET['UsersCount'])){
                dumpUserCount();
                exit;
            }
            // Datas
            if (isset($_GET['UsersFlags'])){

                require_once('ctf_challenges.php');
                dumpUserFlagDataSet($_GET['UsersFlags']);
                exit;
            }
        }
    }
?>