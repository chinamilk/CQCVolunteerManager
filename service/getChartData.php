<?php
include_once("../conn.php");
error_reporting(0);
$type = $_POST['type'];
$tel = trim($_POST['tel']);

if($type=="person")
{
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$group = $result['user_group'];
	$query = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
	if($query) $result = mysql_fetch_array($query);
	$sign = $result['sign_title'];
	if(substr($group,0,1)=="1")
	{
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '0' OR s.`".$sign."` IS NULL");
		if(mysql_num_rows($query)) $person['nosign'] = mysql_num_rows($query);
		else $person['nosign'] = 0;
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '1'");
		if(mysql_num_rows($query)) $person['sign'] = mysql_num_rows($query);
		else $person['sign'] = 0;
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '2'");
		if(mysql_num_rows($query)) $person['checked'] = mysql_num_rows($query);
		else $person['checked'] = 0;
	}
	else
	{
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE (s.`".$sign."` = '0' OR s.`".$sign."` IS NULL) AND LEFT(`user_group`,1) = '".$group."'");
		if(mysql_num_rows($query)) $person['nosign'] = mysql_num_rows($query);
		else $person['nosign'] = 0;
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '1' AND LEFT(`user_group`,1) = '".$group."'");
		if(mysql_num_rows($query)) $person['sign'] = mysql_num_rows($query);
		else $person['sign'] = 0;
		$query = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_sign` s USING(`user_id`) WHERE s.`".$sign."` = '2' AND LEFT(`user_group`,1) = '".$group."'");
		if(mysql_num_rows($query)) $person['checked'] = mysql_num_rows($query);
		else $person['checked'] = 0;
	}
	$html = '<table class="table table-striped"><thead><tr><th colspan="2" class="text-center">当前时段全体签到情况</th></tr></thead><tbody><tr><td><span style="color:#46BFBD;"><strong>已确认</strong></span></td><td>'.$person['checked'].'人</td></tr><tr><td><span style="color:#DAB85F;"><strong>待确认</strong></span></td><td>'.$person['sign'].'人</td></tr><tr><td><span style="color:#F7464A;"><strong>未签到</strong></span></td><td>'.$person['nosign'].'人</td></tr></tbody><tfoot><tr><td colspan="2"></td></tr></tfoot></table><div class="form-group"><button class="btn btn-primary center-block screen" id="detailPersonChart" data-toggle="modal" data-target="#detailPersonModal">查看详情</button></div>';
	echo json_encode(array('data'=>$person,'html'=>$html),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if($type=="event")
{
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$group = $result['user_group'];
	if(substr($group,0,1)=="1")
	{
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '1';");
		if(mysql_num_rows($query)) $event['post'] = mysql_num_rows($query);
		else $event['post'] = 0;
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '2';");
		if(mysql_num_rows($query)) $event['process'] = mysql_num_rows($query);
		else $event['process'] = 0;
		$query = mysql_query("SELECT * FROM `event_info` WHERE `event_step` = '3';");
		if(mysql_num_rows($query)) $event['finish'] = mysql_num_rows($query);
		else $event['finish'] = 0;
	}
	else
	{
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."') AND `event_step` = '1'");
		if(mysql_num_rows($query)) $event['post'] = mysql_num_rows($query);
		else $event['post'] = 0;
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."') AND `event_step` = '2'");
		if(mysql_num_rows($query)) $event['process'] = mysql_num_rows($query);
		else $event['process'] = 0;
		$query = mysql_query("SELECT * FROM `event_info` WHERE (LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."') AND `event_step` = '3'");
		if(mysql_num_rows($query)) $event['finish'] = mysql_num_rows($query);
		else $event['finish'] = 0;
	}
	$html = '<table class="table table-striped"><thead><tr><th colspan="2" class="text-center">当前全部事件流转情况</th></tr></thead><tbody><tr><td><span style="color:#F4A81B;"><strong>已登记</strong></span></td><td>'.$event['post'].'件</td></tr><tr><td><span style="color:#F7464A;"><strong>处理中</strong></span></td><td>'.$event['process'].'件</td></tr><tr><td><span style="color:#6AB82C;"><strong>已完结</strong></span></td><td>'.$event['finish'].'件</td></tr></tbody><tfoot><tr><td colspan="2"></td></tr></tfoot></table><div class="form-group"><button class="btn btn-primary center-block screen" id="detailEventChart" data-toggle="modal" data-target="#detailEventModal">查看详情</button></div>';
	echo json_encode(array('data'=>$event,'html'=>$html),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}
?>