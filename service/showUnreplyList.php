<?php
header("Content-Type: text/html; charset=UTF-8");
include_once("../conn.php");
error_reporting(0);

$id = $_GET["id"];
$smsid = $_GET["smsid"];
$id = urldecode(base64_decode($id));
$smsid = urldecode(base64_decode($smsid));
$query = mysql_query("SELECT * FROM `sms_info` s LEFT JOIN `user_info` i ON s.`sms_postman` = i.`user_id` WHERE `sms_id` = '".$smsid."';");
if(mysql_num_rows($query))
{
	$row = mysql_fetch_array($query);
	if($row["sms_unreply"]=="") echo "未查询到未回复人员记录。";
	else
	{
        $volunteer = 0;
        $guest = 0;
		echo '<table><tr><td>发送流水号</td><td>'.$smsid.'</td><td>内容</td><td>'.$row['sms_content'].'</td><td>发送时间</td><td>'.$row['sms_time'].'</td></tr>';
		$unreply_clip = '<tr><td colspan="2">姓名</td><td colspan="2">组别</td><td colspan="2">联系电话</td></tr>';
		foreach(explode(",",$row['sms_unreply']) as $unreply)
		{
			if(substr($unreply,0,1)=="A")
			{
                $volunteer++;
				$query_name = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_group` USING(`user_group`) WHERE `user_id` = '".substr($unreply,1)."';");
				$result = mysql_fetch_array($query_name);
				$unreply_clip.='<tr><td colspan="2">'.$result['user_name'].'</td><td colspan="2">'.$result['user_group_name'].'</td><td colspan="2">'.$result['user_tel'].'</td></tr>';
			}
			else if(substr($unreply,0,1)=="B")
			{
                $guest++;
				$query_name = mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_id` = '".substr($unreply,1)."';");
				$result = mysql_fetch_array($query_name);
				$unreply_clip.='<tr><td colspan="2">'.$result['guest_name'].'</td><td colspan="2">'.$result['guest_type_name'].'</td><td colspan="2">'.$result['guest_tel'].'</td></tr>';
			}
 
		}
        $unreply_clip.='<tr><td colspan="2">合计</td><td>来宾未回复数</td><td>'.$guest.'</td><td>志愿者未回复数</td><td>'.$volunteer.'</td></tr>';
		echo $unreply_clip.'<tr><td colspan="6">请使用Ctrl+A全选页面内容并复制，粘贴到Excel中。</td></tr></table>';
	}
}
?>