<?php
error_reporting(0);

include_once("../conn.php");

$f = new SaeFetchurl();
$f->setMethod("post");
$f->setPostData(
  array(
	  "action"=> "query" ,
	  "userid"=> UID,
	  "account" => UNAME,
	  "password" => PSW,
  )
);
$ret = $f->fetch("http://115.238.169.181:7788/callApi.aspx");
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