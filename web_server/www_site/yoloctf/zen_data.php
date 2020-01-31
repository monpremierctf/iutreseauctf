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

         
            // Datas
            if (isset($_GET['UsersList'])){
                dumpUserListJSON();
                exit;
            }
            if (isset($_GET['IUTList'])){
                dumpIUTListJSON();
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
            if (isset($_GET['Top20'])){
                $nb= 0;
                $enb = intval($_GET['Top20']);
                if ( $enb >= 10) { $nb = $enb; }; 
                $iut="";
                $lycee="";
                if (isset($_GET['iut'])) { $iut= $_GET['iut']; }
                if (isset($_GET['lycee'])) { $lycee= $_GET['lycee']; }
                dumpTop20($nb, $iut, $lycee);
                exit;
            }
            if (isset($_GET['LoginExist'])){
                echo json_encode(db_login_exists($_GET['LoginExist']));
                exit;
            }           


?>