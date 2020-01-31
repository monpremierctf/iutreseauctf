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
                ?>
                <td><button type="submit" class="btn btn-primary" onclick="return onrowSave('<?php echo htmlspecialchars($row['uid']); ?>')">Save</button></td>
                <td><button type="submit" class="btn btn-primary" onclick="return onrowResetPassword('<?php echo htmlspecialchars($row['uid']); ?>')">ResetPassword</button></td>

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
    $user_query = "SELECT * FROM participants p
            LEFT JOIN users u
            on p.uid = u.uid
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


?>



<div class="col text-center">
    <div class="col text-left">
        <h2>Admin Day CTF</h2><br><br>
    </div>
    <div class="col text-center">

        <!---- UID, login, mail  --->

        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Equipes</div>
            </div>
            <div class="form-group text-left row">
                
                    <?php
                    if (isset($_SESSION['login'])) {
                        // $admin from ctf_env.php
                        if (($_SESSION['login'] === $admin)) {
                            /*
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