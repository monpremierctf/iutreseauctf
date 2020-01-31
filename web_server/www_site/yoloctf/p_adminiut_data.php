<?php
    session_start();
    include "ctf_env.php";

    if (! isset($_SESSION['login'])) {        
        echo "Ko: Must be Admin";
        exit();
    }
    if ($_SESSION['login'] !== $admin) {
            echo "Ko: Must be Admin";
            exit();
    }

    

    function DBUpdatePassword($uid, $passwd) {
        include "ctf_sql_pdo.php";
        $query = "UPDATE users SET passwd = :passwd WHERE UID=:uid ; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'passwd' => $passwd,
            'uid' => $uid 
            ])) {
            $count  = $stmt->rowCount();
        } else {            
            printf("Update failed");
            exit();
        }
    }


    function DBUpdateUser($login, $mail, $pseudo, $u_uid, $status) {
        include "ctf_sql_pdo.php";
        $query = "UPDATE users SET login=:login, mail=:mail, pseudo=:pseudo, status=:status WHERE UID=:u_uid ;";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'login' => $login,
            'mail' => $mail,
            'pseudo' => $pseudo,
            'status' => $status,
            'u_uid' => $u_uid 
            ])) {
            $count  = $stmt->rowCount();
            echo "row=$count";
        } else {            
            printf("Update failed");
            exit();
        }
    }

    function DBUpdateParticipants($lycee, $etablissement, $teamname,
        $nom1, $prenom1, $email1, $uid1, $ismail1confirmed,
        $nom2, $prenom2, $email2, $uid2, $ismail2confirmed,
        $u_uid , $u_state) 
    {

        include "ctf_sql_pdo.php";
        $query = "UPDATE participants SET 
            lycee=:lycee, etablissement=:etablissement, teamname=:teamname,
            nom1=:nom1, prenom1=:prenom1, email1=:email1, uid1=:uid1, ismail1confirmed=:ismail1confirmed, 
            nom2=:nom2, prenom2=:prenom2, email2=:email2, uid2=:uid2, ismail2confirmed=:ismail2confirmed, 
            state=:u_state
            WHERE UID=:u_uid ;";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'lycee' => $lycee,
            'etablissement' => $etablissement,
            'teamname' => $teamname,

            'nom1' => $nom1,
            'prenom1' => $prenom1,
            'email1' => $email1,
            'uid1' => $uid1,
            'ismail1confirmed' => $ismail1confirmed,
            
            'nom2' => $nom2,
            'prenom2' => $prenom2,
            'email2' => $email2,
            'uid2' => $uid2,
            'ismail2confirmed' => $ismail2confirmed, 

            'u_state' => $u_state,
            'u_uid' => $u_uid 
            ])) {
            $count  = $stmt->rowCount();
            echo "row=$count";
        } else {            
            printf("Update failed");
            exit();
        }
    }

    function DBReset()
    {
        include "ctf_sql.php";
        $query = "DELETE FROM flags;";
		if ($result = $mysqli->query($query)) {
			
		}
		$query = "DELETE FROM users  where login!='$admin';";
		if ($result = $mysqli->query($query)) {
			
        }
        $query = "DELETE FROM participants  where 1=1;";
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();
    }



    if ($_POST['cmd']==="dbReset") {
        DBReset();
        echo "dbReset: done";
    }

    if ($_POST['cmd']==="resetPassword") {
        DBUpdatePassword($_POST['uid'], md5($_POST['password']));
        echo "resetPassword ".$_POST['uid']." ".$_POST['password'];
    }
    if ($_POST['cmd']==="saveEntry") {
        DBUpdateUser($_POST['login'], $_POST['mail'], $_POST['pseudo'], $_POST['uid'], $_POST['status']) ;
        DBUpdateParticipants(
            $_POST['lycee'], $_POST['etablissement'], $_POST['teamname'],
            $_POST['nom1'], $_POST['prenom1'], $_POST['email1'],$_POST['uid1'], $_POST['ismail1confirmed'], 
            $_POST['nom2'], $_POST['prenom2'], $_POST['email2'],$_POST['uid2'], $_POST['ismail2confirmed'], 
            $_POST['uid'], $_POST['state']);
        echo "saveEntry";
    }
    echo "Done";

?>
