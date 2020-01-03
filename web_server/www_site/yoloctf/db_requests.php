<?php
function dumpUserFlagDataSet($uid){
    include "ctf_sql.php";
    
	$count=0;
	$query = "SELECT UID,CHALLID, fdate, isvalid, flag FROM flags WHERE UID='$uid';";
	if ($fresult = $mysqli->query($query)) {
		/* fetch object array */
		while ($frow = $fresult->fetch_assoc()) {
			//UID,CHALLID, fdate, isvalid, flag
			//var_dump($row);
			//printf ("%s (%s) (%s) (%s)</br>", $frow['UID'], $frow['flag'], $frow['isvalid'], $frow['fdate']);
			if ($frow['isvalid']) { 
				$chall = getChallengeById($frow['CHALLID']);
				if ($chall!=null){
					$count+=$chall['value'];
				} else {
					$count++;
				}
			}
			$dd = $frow['fdate'];
			$format = '%Y-%m-%d %H:%M:%S'; // 
			//$dd = '2019-05-18 15:32:15';
			//$d = strptime($dd , $format);
			$d = date_parse($dd);
			//$jsdate = "$d[tm_mon]/$d[tm_mday]/$d[tm_year] $d[tm_hour]:$d[tm_min]";
			$jsdate = "$d[month]/$d[day]/$d[year] $d[hour]:$d[minute]";
			//print_r($d);
			echo " { x: '$jsdate', y: $count},";
		}
		$fresult->close();
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

?>
