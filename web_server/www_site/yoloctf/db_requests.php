<?php


function db_login_exists($login){
    require "ctf_sql_pdo.php";
    
    $query = 'SELECT UID FROM users WHERE login=:login';
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['login' => $login ])) {
        return ( $stmt->rowCount()>0);
    }
    return false;
}


function dumpUserFlagDataSet($uid){
    require "ctf_sql_pdo.php";
    
    $count=0;
    $query = 'SELECT UID,CHALLID, fdate, isvalid, flag, score FROM flags WHERE UID=:uid';
    $stmt = $mysqli_pdo->prepare($query);
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
    require "ctf_sql_pdo.php";
    
    $count=0;
    $params=[];
	$query = "SELECT f.UID, max(score) as max_score, login, etablissement, lycee 
	FROM flags f 
		left join users u	on f.UID = u.UID 
		left join participants p	on f.UID = p.UID 
	";
	if ($iut!="") {
        $query = $query." WHERE etablissement=:iut ";
        $params['iut']=$iut;
	}
	if ($lycee!="") {
		if ($iut!="") {
			$query = $query." AND lycee=:lycee ";
		} else {
			$query = $query." WHERE lycee=:lycee ";
        }
        $params['lycee']=$lycee;
	}
	$query = $query." GROUP BY UID ORDER BY max(score) DESC ";
	if ($limit>0) {
        $query = $query." LIMIT :limit ";
        $params['limit']=$limit;
	}
    $query = $query." ;";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute($params)) {
		echo '[';
		$firstrow=true;
		while ($frow = $stmt->fetch()) {
				if ($firstrow) {
					$firstrow=false;
				} else {
					echo ",";
				}
                
                $object = (object) [
                    "etablissement" => $frow['etablissement'],
                    "lycee" => ($frow['lycee']), //htmlspecialchars
                    "login" => $frow['login'],
                    "UID"   => $frow['UID'],
                    "score" => $frow['max_score']
                  ];
                  //var_dump($frow);
                echo json_encode($object);
                //var_dump($frow);
			}
		
		echo ']';
	} else {
        echo "nop";
	}
}



function getNbUsers(){
	require "ctf_sql_pdo.php";
	
    $query = "SELECT count(*) as nbusers FROM users;";
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute()) {
        if ($frow = $stmt->fetch()) {
            return $frow['nbusers'];
        }
    }
	return 0;
}



// 
function dumpUserListJSON(){
    require "ctf_sql_pdo.php";
    $query = "SELECT * FROM users;";
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo '[ ';
        $isfirstrow=true;
        while ($row = $stmt->fetch()) {
            if (!$isfirstrow) {
                echo ",";
            } else {
                $isfirstrow=false;
            }
            /*
            echo '{ ';
            echo '"login":"'.htmlspecialchars($row['login']).'", ';
            echo '"passwd":"'.htmlspecialchars($row['password']).'", ';
            echo '"mail":"'.htmlspecialchars($row['mail']).'", ';
            echo '"pseudo":"'.htmlspecialchars($row['pseudo']).'", '; 
            echo '"UID":"'.htmlspecialchars($row['UID']).'", ';
            echo '"status":"'.htmlspecialchars($row['status']).'" ';
            echo "}\n";
                */
            $object = (object) [
                "login" => $row['login'],
                "passwd" => $row['password'],
                "mail" => $row['mail'],
                "pseudo" => $row['pseudo'],
                "UID" => $row['UID'],
                "status" => $row['status']
              ];
              //var_dump($frow);
            echo json_encode($object);

        }
        echo "]\n";
    }
}


// {"a":1,"b":2,"c":3,"d":4,"e":5}
// login, passwd, mail, pseudo, UID, status
function dumpIUTListJSON(){
    require "ctf_sql_pdo.php";
    $query = "SELECT etablissement FROM participants GROUP BY etablissement ;";
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo '[ ';
        $isfirstrow=true;
        while ($row = $stmt->fetch()) {
            if (!$isfirstrow) {
                echo ",";
            } else {
                $isfirstrow=false;
            }
            echo json_encode($row);
            echo "\n";
        }
        echo "]\n";

    }
}

?>
