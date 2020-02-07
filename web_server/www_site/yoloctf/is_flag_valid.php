<?php

    function is_flag_validated($uid, $cid)
    {
        require "ctf_sql_pdo.php";
        $ret=0;
        $query = "select UID from flags where (UID=:uid and CHALLID=:cid and isvalid=TRUE);";
        //echo $query;
        $stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute(['uid' => $uid, 'cid' => $cid ])) {
            $ret  = $stmt->rowCount();
		}
        return $ret;
    }

    function getUserScore($uid)
    {
        require "ctf_sql_pdo.php";
        $ret=0;
        $query = "select max(score) as maxscore from flags where (UID=:uid);";
        //echo $query;
		$stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute(['uid' => $uid ])) {
            if ($row = $stmt->fetch()) {
                $ret= $row['maxscore'];
            }
		}
        return $ret;
    }


    function save_flag_submission($uid, $cid, $flag, $isvalid)
    {
        $count = is_flag_validated($uid, $cid);
        if (($isvalid)&&($count>0)) {
            return;
        }
            //echo "Valid='$valid'";
            //insert into flags (UID,CHALLID, fdate, isvalid, flag) values ('user1','chall1', NOW(), TRUE, 'flag1');
            $flag = $flag;
            $val=0;
            $score=0;
            if ($isvalid) {
                $val = getChallValue($cid);
                $score=getUserScore($uid)+$val;
            }
            //echo "- $cid, $val, $score";
            require "ctf_sql_pdo.php";
            $query = "insert into flags (UID,CHALLID, fdate, isvalid, flag, value, score) 
                      values (:uid, :cid, NOW(), :isvalid, :flag, :val, :score);";
            //echo $query;
            $stmt = $mysqli_pdo->prepare($query);
            if ($stmt->execute([
                'uid' => $uid,
                'cid' => $cid,
                'isvalid' => $isvalid,
                'flag' => $flag,
                'val' => $val,
                'score' => $score,                
                 ])) {
                // ok
            } else {
                // ko
                echo "Error: " . $sql . "<br>" . $mysqli->error;
            }

    }



    //
    // Handle request
    //
    session_start ();
    include ("ctf_challenges.php");

    // if, flag
    $cid =  $_GET['id'];
    $flag = trim($_GET['flag']);
    if (isset($flag)) {
        $flag = urldecode($flag);
    }

    if (isset($_SESSION['uid'] )) {
        $uid = $_SESSION['uid'];

        // Status != enabled
        if ($_SESSION['status'] !== 'enabled') {
            if (isFlagValid($cid,$flag)){
                print "ok_not_enabled";
            } else {
                echo "ko_not_enabled";
            }
            return;
        }
        require_once'ctf_enable.php';
        if (isset($_GET['flag'])) {
            if (isFlagValid($cid,$flag)){
                print "ok";
                if (isFlagValidationAllowed()) { save_flag_submission($_SESSION['uid'], $cid, $flag, true); }
            } else {
                print "ko";
                if (isFlagValidationAllowed()) { save_flag_submission($_SESSION['uid'], $cid, $flag, false); }
            }   
        } else {
            $count = is_flag_validated($uid, $cid);
            //echo $count;
            if (($count>0)) {
                echo 'ok';
            } else {
                echo 'ko';
            }
        }
    } else {
        // User not logged
        if (isFlagValid($cid,$flag)){
            echo "ok_not_logged";
        } else {
            echo "ko_not_logged";
        }
        
    }
  
?>