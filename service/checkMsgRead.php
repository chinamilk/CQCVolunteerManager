<?php
error_reporting(0);

include_once("../conn.php");
$uid = $_POST['uid'];
$mid = $_POST['mid'];

if($uid&&$mid)
{
	$query = mysql_query("SELECT * FROM `msg_info` WHERE `msg_id` = '".$mid."';");
	if($query) $result = mysql_fetch_array($query);
	$unread = $result['msg_unread'];
	if($unread!="")
	{
		$unread_id = explode(",",$unread);
		unset($unread_id[array_search($uid,$unread_id)]);
		$unread = implode(",",$unread_id);
	}
	$read = $result['msg_read'];
	if($read!="")
	{
		$read_id = explode(",",$read);
		array_push($read_id,$uid);
		$read = implode(",",$read_id);
	}
	else $read = $uid;
	$query = mysql_query("UPDATE `msg_info` SET `msg_read` = '".$read."',`msg_unread` = '".$unread."' WHERE `msg_id` = '".$mid."';");
	if($query) echo "1";
}
?>
