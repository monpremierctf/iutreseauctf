<style>
    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-container {
        overflow-x: scroll;
        max-width: 100%;
    }
</style>



<?php function table_begin()
{ ?>
<div class="container table-container">
    <table class="table is-fullwidth">
        <thead>
            <tr>
                <th></th>
                <th>N° Arrivé</th>
                <th>Team Id</th>

                <th>IUT</th>
                <th>Lycée</th>

                <th>Team Name</th>
                <th>Login</th>
                <th>Pseudo</th>
                <th>eMail</th>

                <th>1-Id</th>
                <th>1-Nom</th>
                <th>1-Prénom</th>
                <th>1-eMail</th>
                <th>1-eMail validé ?</th>

                <th>2-Id</th>
                <th>2-Nom</th>
                <th>2-Prenom</th>
                <th>2-eMail</th>
                <th>2-eMail validé ?</th>

                <th>State pre-enregistrement</th>
                <th>status compte</th>
                <th>Flags</th>
                <th>Score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php   }  ?>



        <?php function table_element_static($id, $val, $label)
        { ?>
            <td id="<?php echo $label . "_" . htmlspecialchars($id); ?>"><?php echo htmlspecialchars($val); ?></td>
        <?php      } ?>

        <?php function table_element_editable($id, $val, $label)
        { ?>
            <td><input type="text" id="<?php echo $label . "_" . htmlspecialchars($id); ?>" value="<?php echo htmlspecialchars($val); ?>"></td>
        <?php } ?>


        <?php function table_row($count, $row)
        {

        ?>
            <tr>
                <?php
                table_element_static($row['uid'], $count, 'count');
                table_element_static($row['uid'], $row['id'], 'id');
                table_element_static($row['uid'], $row['uid'], 'uid');

                table_element_editable($row['uid'], $row['etablissement'], 'etablissement');
                table_element_editable($row['uid'], $row['lycee'], 'lycee');

                table_element_editable($row['uid'], $row['teamname'], 'teamname');
                table_element_editable($row['uid'], $row['login'], 'login');
                table_element_editable($row['uid'], $row['pseudo'], 'pseudo');
                table_element_editable($row['uid'], $row['mail'], 'mail');

                table_element_editable($row['uid'], $row['uid1'], 'uid1');
                table_element_editable($row['uid'], $row['nom1'], 'nom1');
                table_element_editable($row['uid'], $row['prenom1'], 'prenom1');
                table_element_editable($row['uid'], $row['email1'], 'email1');
                table_element_editable($row['uid'], $row['ismail1confirmed'], 'ismail1confirmed');

                table_element_editable($row['uid'], $row['uid2'], 'uid2');
                table_element_editable($row['uid'], $row['nom2'], 'nom2');
                table_element_editable($row['uid'], $row['prenom2'], 'prenom2');
                table_element_editable($row['uid'], $row['email2'], 'email2');
                table_element_editable($row['uid'], $row['ismail2confirmed'], 'ismail2confirmed');

                table_element_editable($row['uid'], $row['state'], 'state');
                table_element_editable($row['uid'], $row['status'], 'status');
                table_element_editable($row['uid'], $row['flag'], 'flag');
                table_element_static($row['uid'], $row['max_score'], 'score');
                ?>
                <td><button type="submit" class="btn btn-primary" onclick="return onrowSave('<?php echo htmlspecialchars($row['uid']); ?>')">Save</button></td>
                <td><button type="submit" class="btn btn-primary" onclick="return onrowResetPassword('<?php echo htmlspecialchars($row['uid']); ?>')">ResetPassword</button></td>
                <td><button type="submit" class="btn btn-primary" onclick="return onrowDelete('<?php echo htmlspecialchars($row['uid']); ?>')">Delete</button></td>

            </tr>

        <?php   }  ?>

        <?php function table_end()
        { ?>
        </tbody>
    </table></div>
    <div><hr><br /><br /></div>
<?php   }  ?>

<script>
    function onrowSave(uid) {
        var postdata = {
            'cmd': "saveEntry",
            'uid': uid,
            'etablissement': document.getElementById("etablissement_" + uid).value,
            'lycee': document.getElementById("lycee_" + uid).value,
            'teamname': document.getElementById("teamname_" + uid).value,
            'login': document.getElementById("login_" + uid).value,
            'pseudo': document.getElementById("pseudo_" + uid).value,
            'mail': document.getElementById("mail_" + uid).value,

            'uid1': document.getElementById("uid1_" + uid).value,
            'nom1': document.getElementById("nom1_" + uid).value,
            'prenom1': document.getElementById("prenom1_" + uid).value,
            'email1': document.getElementById("email1_" + uid).value,
            'ismail1confirmed': document.getElementById("ismail1confirmed_" + uid).value,

            'uid2': document.getElementById("uid2_" + uid).value,
            'nom2': document.getElementById("nom2_" + uid).value,
            'prenom2': document.getElementById("prenom2_" + uid).value,
            'email2': document.getElementById("email2_" + uid).value,
            'ismail2confirmed': document.getElementById("ismail2confirmed_" + uid).value,

            'state': document.getElementById("state_" + uid).value,
            'status': document.getElementById("status_" + uid).value,

        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
        });
    }

    function onrowResetPassword(uid) {
        var passwd = prompt("Enter new password", "123456");
        if (passwd == null || passwd == "") {
            return;
        }
        var postdata = {
            'cmd': "resetPassword",
            'uid': uid,
            'password': passwd,
        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
            });
    }
    function onrowDelete(uid) {
        if (! confirm("Delete entry ?")) {
            return;
        }
        var postdata = {
            'cmd': "deleteUID",
            'uid': uid,
        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
            });
    }
</script>

<?php function dump_table()
{
    include "ctf_sql.php";
    $user_query = "SELECT * FROM participants p
            LEFT JOIN users u
            on p.uid = u.uid
            GROUP BY p.id
            ORDER BY etablissement, lycee;";
    if ($result = $mysqli->query($user_query)) {
        while ($row = $result->fetch_assoc()) {
            table_row("-", $row);
        }
        $result->close();
    } else {
        echo "pb";
    }
    $mysqli->close();
}


function dump_table_by_iut()
{
    include "ctf_sql.php";
    $user_query = "SELECT *, max(score) as max_score FROM participants p
            LEFT JOIN users u  on p.uid = u.uid
            LEFT JOIN flags f  on p.uid = f.uid
            GROUP BY p.id
            ORDER BY etablissement, lycee;";
    if ($result = $mysqli->query($user_query)) {
        $current_iut = "";
        $current_lycee = "";
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            if ($row['etablissement'] != $current_iut) {
                if ($current_iut != "") {
                    table_end();
                }
                echo '<h2 class="title">' . $row['etablissement'] . '</h2></br>';
                $current_iut = $row['etablissement'];
                table_begin();
                $count = 0;
            }
            $count = $count + 1;
            table_row($count, $row);
        }
        table_end();

        $result->close();
    } else {
        echo "pb";
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

    function DBImportParticipantsFromFile($file){
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                DBImportParticipantsFromFileLine($line);
            }

            fclose($handle);
        } else {
            // error opening the file.
        } 
    }


function DBCheckImport() {

    if (isset($_FILES['importfile'])) {
        
        $extension = end(explode(".",$_FILES['importfile']['name']));

        echo "Upload file      : ".$_FILES['importfile']['name']."<br/>";
        echo "-File type       : ".$_FILES['importfile']['type']."<br/>";
        echo "-File extension  : ".$extension."<br/>";
        echo "-File size       : ".$_FILES['importfile']['size']."<br/>";
        echo "-File tmp        : ".$_FILES['importfile']['tmp_name']."<br/>";
        echo "-Status          : ".$_FILES['importfile']['error']."<br/>";
        echo "<br />";
             
        if (!$_FILES['importfile']['error']) {
            if (!isset($uploaddir)) { $uploaddir="upload/"; }
            move_uploaded_file($_FILES['importfile']['tmp_name'], $uploaddir.'/'.$_FILES['importfile']['name']);
            echo "\n\nDéplacement du fichier dans $uploaddir<br/>\n";
            DBImportParticipantsFromFile($uploaddir."/".$_FILES['importfile']['name']);
            echo "<a href='$uploaddir/".$_FILES['importfile']['name']."'> $uploaddir/".$_FILES['importfile']['name']."</a><br/>\n";

        } else {
            echo "<font color='red'>Pb lors de l'upload</font> <br/>";
        }

        
    } 
}

?>



<div class="col text-center">
    <div class="col text-left">
        <h2>Admin Day CTF</h2><br><br>
    </div>

<script>
    function onDBupload()
    {

    }

    function onDBReset()
    {
        if (!confirm("Reset Database ?")) { return; }
        var postdata = {
            'cmd': "dbReset",
        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert(data);
            });
    }
</script>



    <div class="col text-center">
        <!---- DB --->
        <div class="row chall-titre bg-secondary text-white">
            <div class="col-sm text-left">Database Import/Export</div>
        </div>
        <div class="col text-left">
              <button type="submit" class="btn btn-primary" onclick="onDBReset();">Reset DB</button>
        </div>
        <div class="col text-left">       
            <form enctype="multipart/form-data" action="" method="post">
                <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                <input id="importfile" type="file" name="importfile"/>
                <button type="submit" class="btn btn-primary" value="Import">Import Equipes IUT</button>
            </form>
        </div>
        <div class="col text-left">    
            <form action="p_adminiut_export.php" method="post">
                <button type="submit" class="btn btn-primary" value="Export">Export CSV File</button>
            </form> 
        </div>

        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Equipes</div>
            </div>
            <div class="form-group text-left row">
                
                    <?php
                    if (isset($_SESSION['login'])) {
                        // $admin from ctf_env.php
                        if (($_SESSION['login'] === $admin)) {
                            DBCheckImport();

                            /* Full table dump
                            table_begin();
                            dump_table();
                            table_end();
                                */
                            dump_table_by_iut();
                        }
                    }
                    ?>

                
            </div>
        </div>



        <div class="form-group text-left  row ">
            <hr>
        </div>
    </div>
</div>