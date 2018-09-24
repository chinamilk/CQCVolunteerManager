<?php
include_once("../conn.php");
error_reporting(0);


$type = $_POST['type'];
$group = $_POST['group'];

if($type=="person")
{
	$query = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
	if($query) $result = mysql_fetch_array($query);
	$sign = $result['sign_title'];
	if($group!="0")
	{
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE (s.`".$sign."` = '0' OR s.`".$sign."` IS NULL ) AND LEFT(`user_group`,".strlen($group).") = '".$group."'");
	  if($query) $person['nosign'] = mysql_num_rows($query);
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '1' AND LEFT(`user_group`,".strlen($group).") = '".$group."'");
	  if($query) $person['sign'] = mysql_num_rows($query);
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '2' AND LEFT(`user_group`,".strlen($group).") = '".$group."'");
	  if($query) $person['checked'] = mysql_num_rows($query);
	}
	else
	{
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '0' OR s.`".$sign."` IS NULL");
	  if($query) $person['nosign'] = mysql_num_rows($query);
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '1'");
	  if($query) $person['sign'] = mysql_num_rows($query);
	  $query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '2'");
	  if($query) $person['checked'] = mysql_num_rows($query);
	}
	echo json_encode(array('data'=>$person),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if($type=="event")
{
	if($group!="0")
	{
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,".strlen($group).") = '".$group."' OR LEFT(`event_process`,".strlen($group).") = '".$group."') AND `event_step` = '1'");
		if($query) $event['post'] = mysql_num_rows($query);
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,".strlen($group).") = '".$group."' OR LEFT(`event_process`,".strlen($group).") = '".$group."') AND `event_step` = '2'");
		if($query) $event['process'] = mysql_num_rows($query);
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,".strlen($group).") = '".$group."' OR LEFT(`event_process`,".strlen($group).") = '".$group."') AND `event_step` = '3'");
		if($query) $event['finish'] = mysql_num_rows($query);
	}
	else
	{
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '1';");
		if($query) $event['post'] = mysql_num_rows($query);
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '2';");
		if($query) $event['process'] = mysql_num_rows($query);
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '3';");
		if($query) $event['finish'] = mysql_num_rows($query);
	}
	echo json_encode(array('data'=>$event),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}
?>