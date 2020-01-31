<?php
/*
    INPUT: none
    CMD: 
        $_GET['clearFlags']
        $_GET['importParticipants']
	GLOBAL : $_SESSION

	*/
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    session_start ();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Y0L0 CTF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <link rel="stylesheet" href="/yoloctf/style.css">
  <script src="/yoloctf/js/jquery.min.js"></script>
  <script src="/yoloctf/js/popper.min.js"></script>
  <script src="/yoloctf/js/bootstrap.min.js"></script>
  <script src="/yoloctf/js/ctf-utils.js"></script>
  <script src="/yoloctf/js/moment.min.js"></script>


	<style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>


</head>
<body>

<!--- Page Header  -->
<?php
    include 'ctf_env.php'; 
    include "Parsedown.php";
    $Parsedown = new Parsedown();
    include 'header.php'; 

?>


<div class="container-fluid">
    <div class="row">
        <!--- Page TOC  -->
        <div class="col-md-auto">
            <?php include 'toc.php' ?>
        </div>

        <!--- Page Content  -->
        <div class="col">
        <div class="container">


<?php
    


    function dumpUserList(){
        include "ctf_sql.php";
        $user_query = "SELECT login, UID FROM users;";
        if ($result = $mysqli->query($user_query)) {
            echo "Nb users : ".$result->num_rows."</br>";
            while ($row = $result->fetch_assoc()) {
                $uid = $row['UID'];
                $login = $row['login'];
                echo "[".htmlspecialchars($login)."]  ".$uid."</br>";	
            }
            $result->close();
        }
        $mysqli->close();
    }


    function dumpUserFlags() {
        include "ctf_sql.php";
		$user_query = "SELECT login, UID FROM users;";
		if ($user_result = $mysqli->query($user_query)) {
			while ($row = $user_result->fetch_assoc()) {
				$uid = $row['UID'];
				$login = $row['login'];
                echo "</br><u>[".htmlspecialchars($login)."]  ".$uid."</u></br>";	
                $query = "SELECT UID,CHALLID, fdate, isvalid, flag FROM flags WHERE UID='$uid';";
                if ($fresult = $mysqli->query($query)) {
                   
                    while ($frow = $fresult->fetch_assoc()) {
                        $chall = getChallengeById($frow['CHALLID']);
                        if ($frow['isvalid']) {
                            printf ("%s (%s) (%s): ok</br>", $frow['fdate'], $frow['CHALLID'], $chall['name']);
                        } else {
                            printf ("%s (%s) (%s) </br>", $frow['fdate'], $frow['CHALLID'], htmlspecialchars($frow['flag']));
                        }
                    }
                    $fresult->close();	
                }		
			}
			$user_result->close();
		}
		$mysqli->close();
    }
    
    function clearFlags(){
        include "ctf_sql.php";
		$query = "DELETE FROM flags;";
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();

    }

    // Must be admin...
    function DBCreateExtTable(){
        include "ctf_sql.php";
        $query = 'CREATE TABLE IF NOT EXISTS participants (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            lycee VARCHAR,
            etablissement VARCHAR,
            nom1 VARCHAR,
            prenom1 VARCHAR,
            email1 VARCHAR,
            uid1 VARCHAR,
            ismail1confirmed boolean,
            nom2 VARCHAR,
            prenom2 VARCHAR,
            email2 VARCHAR,
            uid2 VARCHAR,
            ismail2confirmed boolean,
            uid VARCHAR,
            teamname VARCHAR,
            state INTEGER
        );';
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();

    }

    function DBImportUser($login, $upasswd, $mail, $pseudo, $uid, $status) {
        include "ctf_sql.php";
        $status = 'enabled';
        $request = "INSERT into users (login, passwd, mail, pseudo, UID, status) VALUES ('$login', md5('$upasswd'), '$mail','$pseudo', '$uid', '$status')";
        $result = $mysqli->query($request);
        $count  = $result->affected_rows;
        if ($result) {}
        $mysqli->close();
    }

    function DBImportParticipants($lycee, $etablissement, $teamname,
        $nom1, $prenom1, $email1, $uid1, $ismail1confirmed,
        $nom2, $prenom2, $email2, $uid2, $ismail2confirmed,
        $uid , $state){
        include "ctf_sql.php";
        
        $query = "INSERT INTO participants (
            lycee, etablissement, teamname,
            nom1, prenom1, email1, uid1, ismail1confirmed,
            nom2, prenom2, email2, uid2, ismail2confirmed,
            uid , state) 
        VALUES ('$lycee', '$etablissement', '$teamname',
            '$nom1', '$prenom1', '$email1', '$uid1', '$ismail1confirmed',
            '$nom2', '$prenom2', '$email2', '$uid2', '$ismail2confirmed',
            '$uid' , '$state');";
        if ($result = $mysqli->query($query)) {
			
		} else {

        }
		$mysqli->close();
    }

    function DBImportParticipantsFromFileLine($line){
        $f = explode(";", $line);

        $etablissement = trim($f[2]);
        $lycee = trim($f[3]);

        $uid = trim($f[4]);
        $teamname = trim($f[5]);

        $uid1 = trim($f[6]);
        $nom1 = trim($f[7]);
        $prenom1 = trim($f[8]);
        $email1 = trim($f[9]);
        $ismail1confirmed = trim($f[10]);

        $uid2 = trim($f[11]);
        $nom2 = trim($f[12]);
        $prenom2 = trim($f[13]);
        $email2 = trim($f[14]);
        $ismail2confirmed = trim($f[15]);

        $state = trim($f[16]);
        $flags = trim($f[17]);

        DBImportParticipants($lycee, $etablissement, $teamname,
            $nom1, $prenom1, $email1, $uid1, $ismail1confirmed,
            $nom2, $prenom2, $email2, $uid2, $ismail2confirmed,
            $uid , $state);

        DBImportUser($teamname, $uid, $email1, $teamname, $uid, 'enabled');
    }

    function DBImportParticipantsFromFile(){
        $handle = fopen("extract_sample.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                DBImportParticipantsFromFileLine($line);
            }

            fclose($handle);
        } else {
            // error opening the file.
        } 
    }

    function clearUsers(){
        include "ctf_sql.php";
        clearFlags();
		$query = "DELETE FROM users  where login!='$admin';";
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();

    }

    function dumpUserContainersList($cont){
        include "ctf_sql.php";
        $user_query = "SELECT login, UID FROM users;";
        if ($result = $mysqli->query($user_query)) {
            while ($row = $result->fetch_assoc()) {
                $uid = $row['UID'];
                $login = $row['login'];
                echo "<u>[".htmlspecialchars($login)."]  ".$uid."</u></br>";	
                if ($cont != null)	{
                    foreach ($cont as $c) {
                        if ('CTF_UID_'.$uid == $c->Uid) {
                            echo "    - ".$c->Name."</br>";
                        }
                    }
                }
            }
            $result->close();
        }
        $mysqli->close();
    }
?>



<?php
    
    
    function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        //curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    // ...Cant modify env variables
    // TO DO: Add a SESSION['CSRFEnabled'] parameter
    // SESSION['CSRFEnabled'] = $_ENV["CTF_CSRFGUARD_ENABLED"] if not set.
    function ctf_csrf_enable(){
        if (putenv("CTF_CSRFGUARD_ENABLED=true")) {
            echo "ok";
        } else {
            echo "ko";
        }
    }
    function ctf_csrf_disable(){
        if (putenv("CTF_CSRFGUARD_ENABLED=false")){
            echo "ok";
        } else {
            echo "ko";
        }
    }

    if (isset($_SESSION['login'] )) {
        // $admin from ctf_env.php
        if (($_SESSION['login']=== $admin )) {
            // Actions
            if (isset($_GET['clearFlags'])){
                clearFlags();
            }
            if (isset($_GET['importParticipants'])){
                DBImportParticipantsFromFile();
            }
            if (isset($_GET['CSRFEnable'])){
                ctf_csrf_enable();
            }
            if (isset($_GET['CSRFDisable'])){
                ctf_csrf_disable();
            }

            // Get containers
            $url = 'http://challenge-box-provider:8080/listChallengeBox/';
            $json = file_get_contents_curl($url);
            $cont = json_decode($json);

            echo "<h3>Php sessions</h3> ";
            echo "Nb sessions : ". get_active_users();

            echo "<h3>Users</h3>
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserList();
            print "</div></div></br>";

            echo "<h3>Flags submited</h3> 
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserFlags();
            print "</div></div></br>";
            
            echo "<h3>Containers</h3> ";
            echo "Nb Containers = ".count($cont)."</br>
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserContainersList($cont);
            print "</div></div></br>";

            echo "</br>";
            
            echo "<h4>BDD</h4>";
            print '<a href="zen.php?clearFlags" ><pre class="ctf-menu-color">[ClearFlags]</pre></a> ';

            echo "<h4>Import</h4>";
            print '<a href="zen.php?importParticipants" ><pre class="ctf-menu-color">[ImportParticipants]</pre></a> ';

            echo "<h3>Env</h3> ";
            echo "<div class='panel panel-primary'><div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
               foreach (getenv() as $key => $value){
                   echo "$key=$value<br />";
               }
            print "</div></div></br>";

            print "<h3>CSRF Enabled:";
            echo json_encode($_ENV["CTF_CSRFGUARD_ENABLED"]);
            print "</h3> ";
            print '<a href="zen.php?CSRFEnable" ><pre class="ctf-menu-color">[CSRFEnable]</pre></a> ';
            print '<a href="zen.php?CSRFDisable" ><pre class="ctf-menu-color">[CSRFDisable]</pre></a> ';


        } else {

        }

            

    } else {
        //echo "Merci de vous connecter.";
    }



 
?>
         </div>
        </div>
    </div>
</div>


  
</body>
</html>




