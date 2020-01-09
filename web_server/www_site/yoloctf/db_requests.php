<?php
function dumpUserFlagDataSet($uid){
    require_once "ctf_sql_pdo.php";
    
	$count=0;
    $stmt = $mysqli_pdo->prepare('SELECT UID,CHALLID, fdate, isvalid, flag, score FROM flags WHERE UID=:uid');
    if ($stmt->execute(['uid' => $uid ])) {
        echo '[';
        $firstrow=true;
		while ($frow = $stmt->fetch()) {
			if ($frow['isvalid']) { 
                $count =  $frow['score'];
                $dd = $frow['fdate'];
                $format = '%Y-%m-%d %H:%M:%S'; 
                $d = date_parse($dd);
                $jsdate = "$d[month]/$d[day]/$d[year] $d[hour]:$d[minute]";

                if ($firstrow) {
                    $firstrow=false;
                } else {
                    echo ",";
                }
                echo ' { "x": "'.$jsdate.'", "y": '.$count.'}';
                
            }
        }
        echo ']';

	}
}

function dumpTop20($limit=0, $iut="", $lycee=""){
    include "ctf_sql.php";
    
    
	$count=0;
	$query = "SELECT f.UID, max(score) as max_score, login, etablissement, lycee 
	FROM flags f 
		left join users u	on f.UID = u.UID 
		left join participants p	on f.UID = p.UID 
	";
	if ($iut!="") {
		$query = $query." WHERE etablissement='$iut' ";
	}
	if ($lycee!="") {
		if ($iut!="") {
			$query = $query." AND lycee='$lycee' ";
		} else {
			$query = $query." WHERE lycee='$lycee' ";
		}
	}
	$query = $query." GROUP BY UID ORDER BY max(score) DESC ";
	if ($limit>0) {
		$query = $query." LIMIT $limit ";
	}
	$query = $query." ;";
	if ($fresult = $mysqli->query($query)) {
		/* fetch object array */
		echo '[';
		$firstrow=true;
		while ($frow = $fresult->fetch_assoc()) {
				if ($firstrow) {
					$firstrow=false;
				} else {
					echo ",";
				}
				//echo ' { "UID": "'.$frow['UID'].'", "score": '.$frow['max_score'].'", "login": '.$frow['login'].'}'; 
               
                $object = (object) [
                    "etablissement" => $frow['etablissement'],
                    "lycee" => $frow['lycee'],
                    "login" => $frow['login'],
                    "UID"   => $frow['UID'],
                    "score" => $frow['max_score']
                  ];
                echo json_encode($object);
			}
		
		echo ']';
		$fresult->close();
	} else {

	}
}



function getNbUsers(){
	include "ctf_sql.php";
	
	$user_query = "SELECT count(*) as nbusers FROM users;";
	if ($user_result = $mysqli->query($user_query)) {
		$row = $user_result->fetch_assoc();
		//echo "Error: " . $mysqli->error . "<br>";
		//echo $row['nbusers'];
		return $row['nbusers'];
	}
	return 0;
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
                echo '"login":"'.htmlspecialchars($row['login']).'", ';
                echo '"passwd":"'.htmlspecialchars($row['password']).'", ';
                echo '"mail":"'.htmlspecialchars($row['mail']).'", ';
                echo '"pseudo":"'.htmlspecialchars($row['pseudo']).'", '; 
                echo '"UID":"'.htmlspecialchars($row['UID']).'", ';
                echo '"status":"'.htmlspecialchars($row['status']).'" ';
                echo "}\n";
            }
            echo "]\n";
            $result->close();
        }
        $mysqli->close();
	}
	

    // {"a":1,"b":2,"c":3,"d":4,"e":5}
    // login, passwd, mail, pseudo, UID, status
    function dumpIUTListJSON(){
        include "ctf_sql.php";
        $user_query = "SELECT etablissement FROM participants GROUP BY etablissement ;";
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
                echo '"etablissement":"'.htmlspecialchars($row['etablissement']).'" ';
                echo "}\n";
            }
            echo "]\n";
            $result->close();
        }
        $mysqli->close();
    }

?>
