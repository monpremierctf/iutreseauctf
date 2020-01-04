<?php
function dumpUserFlagDataSet($uid){
    include "ctf_sql.php";
    
    
	$count=0;
	$query = "SELECT UID,CHALLID, fdate, isvalid, flag, score FROM flags WHERE UID='$uid';";
	if ($fresult = $mysqli->query($query)) {
        /* fetch object array */
        echo '[';
        $firstrow=true;
		while ($frow = $fresult->fetch_assoc()) {
			//UID,CHALLID, fdate, isvalid, flag
			//var_dump($row);
			//printf ("%s (%s) (%s) (%s)</br>", $frow['UID'], $frow['flag'], $frow['isvalid'], $frow['fdate']);
			if ($frow['isvalid']) { 
                /*
				$chall = getChallengeById($frow['CHALLID']);
				if ($chall!=null){
					$count+=$chall['value'];
				} else {
					$count++;
				}
                */
                $count =  $frow['score'];
                $dd = $frow['fdate'];
                $format = '%Y-%m-%d %H:%M:%S'; // 
                //$dd = '2019-05-18 15:32:15';
                //$d = strptime($dd , $format);
                $d = date_parse($dd);
                //$jsdate = "$d[tm_mon]/$d[tm_mday]/$d[tm_year] $d[tm_hour]:$d[tm_min]";
                $jsdate = "$d[month]/$d[day]/$d[year] $d[hour]:$d[minute]";
                //print_r($d);
                if ($firstrow) {
                    $firstrow=false;
                } else {
                    echo ",";
                }
                echo ' { "x": "'.$jsdate.'", "y": '.$count.'}';
                
            }
        }
        echo ']';
		$fresult->close();
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
				echo ' { "etablissement": "'.$frow['etablissement'].'", "lycee": "'.$frow['lycee'].'", "login": "'.$frow['login'].'", "UID": "'.$frow['UID'].'", "score": '.$frow['max_score'].'}';                  
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
