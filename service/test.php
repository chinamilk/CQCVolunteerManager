<?php
include_once("../conn.php");
$ret = '<?xml version="1.0" encoding="utf-8" ?> 
<returnsms>
<callbox>
<mobile>15827462343</mobile>
<taskid>4431</taskid>
<content>【测试需要，不是真的回复。——王镜毓】</content>
<receivetime>2011-12-02 22:12:11</receivetime>
</callbox>
</returnsms>
';
$xml = simplexml_load_string($ret);
foreach($xml->callbox as $v)
{
	if((string)$v->mobile && (string)$v->taskid && (string)$v->content)
	{
		$query = mysql_query("SELECT * FROM `sms_info` WHERE FIND_IN_SET('".(string)$v->taskid."',`sms_taskid`);");
		if(mysql_num_rows($query)) $result = mysql_fetch_array($query);
		$id = $result['sms_id'];
		$unreply = $result['sms_unreply'];
		$unreply_list = explode(",",$unreply);
		
		$query_add = mysql_query("INSERT INTO `sms_reply` (`reply_id`,`sms_id`,`reply_tel`,`reply_content`,`reply_time`) VALUES (NULL, '".$id."', '".(string)$v->mobile."','".(string)$v->content."','".date("Y/m/d H:i:s")."');");
		
		$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".(string)$v->mobile."';");
		if(mysql_num_rows($query))
		{
			$result = mysql_fetch_array($query);
			$user_id = $result['user_id'];
			if(array_search("A".$user_id,$unreply_list)!==false) unset($unreply_list[array_search("A".$user_id,$unreply_list)]);
		}
		$query = mysql_query("SELECT * FROM `guest_info` WHERE `guest_tel` = '".(string)$v->mobile."';");
		if(mysql_num_rows($query))
		{
			$result = mysql_fetch_array($query);
			$user_id = $result['guest_id'];
			if(array_search("B".$user_id,$unreply_list)!==false) unset($unreply_list[array_search("B".$user_id,$unreply_list)]);
		}
		array_flip(array_flip($unreply_list));
		$unreply = implode(",",$unreply_list);
		$query = mysql_query("UPDATE `sms_info` SET `sms_unreply` = '".$unreply."' WHERE `sms_id` = '".$id."';");
	}
}


?>