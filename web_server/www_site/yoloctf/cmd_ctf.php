<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    header("Access-Control-Allow-Origin: *");
    //header("Content-Type: application/json; charset=UTF-8");

    session_start ();
    require_once('ctf_mail.php');
    require_once('ctf_sql.php');
    require_once('ctf_sql_pdo.php');

    //////////////////////////////////////////////////////////////////////////////
    // MUST HAVE a UID
    //
    if ( ! isset($_SESSION['uid'] )) {
        //echo "Ko, merci de vous connecter.";
        // 401 Unauthorized
        http_response_code(401);
        echo json_encode(array("message" => "Please authenticate."));
        exit();
    }


    //
    // Resend validation mail
    //
    if (isset($_GET['resendValidationMail'])){
        if ($_SESSION['status']!=='waiting_email_validation') {
            echo "Mail non nécessaire.";
            exit();
        }
        if (ctf_send_validation_mail($_SESSION['uid'], $_SESSION['mail'])) {
            echo "Mail envoyé.";
        } else {
            echo "Mail non envoyé.";
        }
        exit();
    }

    //
    // Set  email address
    //
    if (isset($_GET['setEmail'])){
        $newmail = $_GET['setEmail'];
        // Check email format
        // Warning : "'`whoami`'"@example.com is ok for filter_var()
        // \n\r will be detected
        if (!filter_var($newmail, FILTER_VALIDATE_EMAIL)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Bad email format."));
            exit();
        }
        $uid = $_SESSION['uid'];
        $pdo_request = "UPDATE users SET mail=:newmail WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
        $statement->execute([
            'newmail' => $newmail,
            'uid' => $uid 
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        http_response_code(200);
        echo json_encode(array("message" => "eMail mis à jour."));
        $_SESSION['mail']=$newmail;
        exit();
    }



    function username_exist($name) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM users WHERE login=:name";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'name' => $name,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function username_update($uid, $newname) {
        global $mysqli_pdo;

        $pdo_request = "UPDATE users SET login=:newname WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'newname' => $newname,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function password_update($uid, $password) {
        global $mysqli_pdo;

        $pdo_request = "UPDATE users SET passwd=:password WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'password' => $password,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }
    //
    // Set name
    //
    if (isset($_GET['setLogin'])){
        $desiredname = $_GET['setLogin'];
        // Whorst waf ever :)
        // Remove some char
        $desiredname = str_replace("'", "", $desiredname);

        // Escape string
        $newname = mysqli_real_escape_string($mysqli, $desiredname);

        
        // Check exist ?
        if (username_exist($newname)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Existing name."));
            exit();
        }

        // Save new name
        $uid = $_SESSION['uid'];
        username_update($uid, $newname);
        http_response_code(200);
        echo json_encode(array("message" => "Login mis à jour."));
        $_SESSION['login']=$newname;
        exit();
    }


    //
    // change Passwd
    //
    if (isset($_GET['setPassword'])) { 
        $desired_passwd = $_GET['setPassword'];

        // Some containts on pwd ?
        if (strlen($desired_passwd)<3) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Too short."));
            exit();
        }

        $newpwd = md5($desired_passwd);
        $uid = $_SESSION['uid'];
        password_update($uid, $newpwd);
        http_response_code(200);
        echo json_encode(array("message" => "Password mis à jour."));
        exit();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Account must be in state Enabled
    //
    if ( ($_SESSION['status']!=='enabled')) {
        //echo "Ko, Mail non validé ou Compte bloqué.";
        // 401 Unauthorized
        http_response_code(401);
        echo json_encode(array("message" => "Mail non validé ou Compte bloqué."));
        exit();
    }

    function generate_UIDCTF($len = 5){
        return strtoupper(substr(md5(microtime()),rand(0,26),$len));
      }

    //
    // Create CTF
    //

    function ctfs_name_exist($name) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM ctfs WHERE ctfname=:$name";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'name' => $name,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }

    function ctfs_create($uidctf, $name, $uid) {
        global $mysqli_pdo;

        $pdo_request = "INSERT into ctfs (creation_date, UIDCTF, ctfname, UIDADMIN) VALUES (now(), :uidctf, :name, :uid)";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidctf' => $uidctf,
                'name' => $name,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    if (isset($_GET['createCTF'])){
        $desiredname = $_GET['createCTF'];
        if(strlen($desiredname)<2) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Too short CTF name.".$name));
            exit();
        }
        $desiredname = str_replace("'", "", $desiredname);
        $name_sqlsafe  = mysqli_real_escape_string($mysqli, $desiredname);
        $name_htmlsafe = htmlspecialchars($desiredname, ENT_QUOTES| ENT_HTML401);

        // Already exist ?
        $exist  = ctfs_name_exist($name_sqlsafe);
        if($exist>0) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Existing CTF name.".$name_htmlsafe));
            exit();
        }
        
        $uid = $_SESSION['uid'];
        $creation_date = date("Y-m-d H:i:s");
        $uidctf = generate_UIDCTF(5);

        ctfs_create($uidctf, $name_sqlsafe, $uid);
        http_response_code(200);
        echo json_encode(array("message" => "CTF created."));
    }



    function getCTFFromUID($uidctf)
    {
        global $mysqli;
        $request = "SELECT * FROM ctfs WHERE UIDCTF='$uidctf'";
        $result = $mysqli->query($request);
        $count  = $result->num_rows;
        if($count>0) {
            $row = $result->fetch_array();
            $ctfname =  $row['ctfname'];
            return $ctfname;
        }
        return "";
    }


    
    //
    // Join CTF
    //
    if (isset($_GET['joinCTF'])){
        $desiredname = $_GET['joinCTF'];
        $name_sqlsafe  = mysqli_real_escape_string($mysqli, $desiredname);
        $name_htmlsafe = htmlspecialchars($desiredname, ENT_QUOTES| ENT_HTML401);

        // CTF exist ?
        $request = "SELECT * FROM ctfs WHERE UIDCTF='$name_sqlsafe'";
        $result = $mysqli->query($request);
        $count  = $result->num_rows;
        if($count<=0) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Unkown CTF name.".$name_htmlsafe));
            exit();
        }
        
        // Already in a CTF ?
        $uid = $_SESSION['uid'];
        $request = "SELECT * FROM ctfsusers WHERE UIDUSER='$uid'";
        $result = $mysqli->query($request);
        $count  = $result->num_rows;
        if($count>0) {
            $request = "UPDATE ctfsusers SET UIDCTF='$name_sqlsafe' WHERE UIDUSER='$uid';";
        } else {
            $request = "INSERT into ctfsusers (UIDCTF, UIDUSER) VALUES ('$name_sqlsafe', '$uid');";
        }
        $result = $mysqli->query($request);
        if ($result) {
            $_SESSION['ctfuid']=$desiredname;
            $_SESSION['ctfname']=getCTFFromUID($desiredname);
            http_response_code(200);
            echo json_encode(array("message" => "Joined CTF."));
        } else {
            // 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$request."] ".$mysqli->error));
        }
    }    

?>