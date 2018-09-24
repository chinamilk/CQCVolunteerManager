<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."'");
if($query) $result = mysql_fetch_array($query);

$group = substr($result['user_group'],0,1);
$id = $result['user_id'];

$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."') AND (`event_step` = '1' OR `event_step` = '2');");
if($query) $eventNum = mysql_num_rows($query);

$confirmNum = 0;

$query = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
$result = mysql_fetch_array($query);
$sign = $result['sign_title'];

$query = mysql_query("SELECT * FROM `user_info` WHERE FIND_IN_SET('".$id."',`user_parent`) ;");
if(mysql_num_rows($query))
{
	while($row = mysql_fetch_array($query))
	{
		$query_sign = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`user_id` = '".$row['user_id']."' AND s.`".$sign."` = '1';");
		if(mysql_num_rows($query_sign)) $confirmNum++;
	}
}

$query = mysql_query("SELECT COUNT(*) AS cnt FROM `msg_info` WHERE FIND_IN_SET('".$id."',`msg_unread`);");
if($query) $result=mysql_fetch_array($query);
$msgNum = intval($result['cnt']);

echo json_encode(array('eventNum'=>$eventNum,'confirmNum'=>$confirmNum,'msgNum'=>$msgNum),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
?>