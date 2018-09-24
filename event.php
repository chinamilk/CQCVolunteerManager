<?php
include_once("conn.php");

$tel = trim($_POST['postman']);
$require = $_POST['EventRequire'];
$require_p = $_POST['EventRequirePerson'];
$time = $_POST['EventTime'];
$place = $_POST['EventPlace'];
$process = $_POST['EventProcess'];
$process_p = $_POST['EventProcessPerson'];
$content = $_POST['EventContent'];
$type = $_POST['type'];

if($type=="postnew")
{
	preg_match("/^\d+/",$require_p,$match);
	$require_p = $match[0];
	preg_match("/^\d+/",$process_p,$match);
	$process_p = $match[0];
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	$result = mysql_fetch_array($query);
	$id = $result['user_id'];
	$query = mysql_query("INSERT INTO `event_info` (`event_id`, `event_require`, `event_require_person`, `event_time`, `event_place`, `event_content`, `event_process`, `event_process_person`, `event_step`, `event_post_man`, `event_solution`, `event_post_time`) VALUES (NULL, '".$require."', '".$require_p."', '".$time."', '".$place."', '".$content."', '".$process."', '".$process_p."', '1', '".$id."', '', '".time()."');");
	if($query) echo "1";
}

if($type=="addsolution")
{
	$eventid = $_POST['eventid'];
	$solution = $_POST['solution'];
	$tel = trim($_POST['tel']);
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$query = mysql_query("UPDATE `event_info` SET `event_solution` = '".$solution."', `event_step` = '2',`event_post_man` = CONCAT(`event_post_man`,' ".$result['user_id']."'),`event_post_time` = '".time()."' WHERE `event_id` = '".$eventid."';");
	if($query) echo "1";
}

if($type=="finish")
{
	$eventid = $_POST['eventid'];
	$tel = trim($_POST['tel']);
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$query = mysql_query("UPDATE `event_info` SET `event_step` = '3',`event_post_man` = CONCAT(`event_post_man`,' ".$result['user_id']."'),`event_post_time` = '".time()."' WHERE `event_id` = '".$eventid."';");
	if($query) echo "1";
}
?>