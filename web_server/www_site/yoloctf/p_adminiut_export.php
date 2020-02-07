<?php

function dump_header() { 
    $tab = "; ";
    $ret = "";
    $ret = $ret."Count".$tab;
    $ret = $ret."id".$tab; 
    $ret = $ret."etablissement".$tab; 
    $ret = $ret."lycee".$tab; 
    $ret = $ret."uid".$tab; 
    $ret = $ret."teamname".$tab; 
    $ret = $ret."uid1".$tab;
    $ret = $ret."nom1".$tab; 
    $ret = $ret."prenom1".$tab; 
    $ret = $ret."email1".$tab; 
    $ret = $ret."ismail1confirmed".$tab;
    $ret = $ret."uid2".$tab; 
    $ret = $ret."nom2".$tab; 
    $ret = $ret."prenom2".$tab; 
    $ret = $ret."email2".$tab; 
    $ret = $ret."ismail2confirmed".$tab; 
    $ret = $ret."state".$tab;
    $ret = $ret."flag"; 
    $ret = $ret."\n";
    return $ret;
} 

function dump_row($count, $row) { 
    $tab = "; ";
    $removechar = " ,;\t\n\r\0\x0B";
    $ret = "";
    $ret = $ret.htmlspecialchars($count).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['id'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['etablissement'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['lycee'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['uid'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['teamname'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['uid1'], $removechar)).$tab;
    $ret = $ret.htmlspecialchars(trim($row['nom1'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['prenom1'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['email1'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['ismail1confirmed'], $removechar)).$tab;
    $ret = $ret.htmlspecialchars(trim($row['uid2'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['nom2'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['prenom2'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['email2'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['ismail2confirmed'], $removechar)).$tab; 
    $ret = $ret.htmlspecialchars(trim($row['state'], $removechar)).$tab;
    $ret = $ret.htmlspecialchars(trim($row['flag'], $removechar)); 
    $ret = $ret."\n";
    return $ret;
} 


    $data = "aa, bbb, ccc\nzzz, eee, rrr";
    $data="";

    include "ctf_sql_pdo.php";

    $iut="";
    if (isset($_GET['iut'])) {
        $iut = $_GET['iut'];
    
    } 
    $statement = $mysqli_pdo->prepare("
            SELECT 
                p.*, count(f.flag) AS flag 
                
            FROM participants p 
            LEFT JOIN flags f
            on p.uid = f.uid
            GROUP BY p.id
            ORDER BY etablissement, lycee
        ;");
    $statement->execute();
    
    $current_iut="";
    $current_lycee="";
    $count=0;
    $data = $data.dump_header();
    while ($row = $statement->fetch()) {
        if ($row['etablissement'] != $current_iut) {
            if ($current_iut!=""){
                
            }
            $current_iut = $row['etablissement'];
            $count=0;
        }
        $count = $count+1;
        if ($iut=="") {
            $data = $data.dump_row($count, $row);
        } elseif ($current_iut == $iut) {
            $data = $data.dump_row($count, $row);
        }
                                
    } 
    
    

    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: application/text");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".strlen($data));
    header("Content-Disposition: attachment; filename=data.csv");
    echo $data;
    die();        

?>