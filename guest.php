<?php
include_once("conn.php");

$tel = trim($_POST['tel']);
$arrival_time = trim($_POST['ArrivalTime']);
$arrival_trans = trim($_POST['ArrivalTrans']);
$departure_time = trim($_POST['DepartureTime']);
$departure_trans = trim($_POST['DepartureTrans']);
$food = trim($_POST['Food']);
$other = trim($_POST['Other']);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];
$query = mysql_query("UPDATE `guest_info` SET `guest_arrival_time` = '".$arrival_time."', `guest_arrival_trans` = '".$arrival_trans."', `guest_departure_time` = '".$departure_time."', `guest_departure_trans` = '".$departure_trans."', `guest_food` = '".$food."',`guest_other` = '".$other."' WHERE `guest_user` = '".$id."';");
if($query) echo "1";
else echo "0";
?>