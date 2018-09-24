<?php
header("Content-Type: text/html; charset=UTF-8");
include_once("../conn.php");
error_reporting(0);

$id = $_GET["id"];
$smsid = $_GET["smsid"];
$id = urldecode(base64_decode($id));
$smsid = urldecode(base64_decode($smsid));
$query = mysql_query("SELECT * FROM `sms_reply` s LEFT JOIN `sms_info` i USING(`sms_id`) WHERE `sms_id` = '".$smsid."';");
if(!mysql_num_rows($query)) echo "未查询到回复记录。";
else
{
	$i = 0;
    $reply_tel_list = array();
	while($row = mysql_fetch_array($query))
	{
        if(in_array($row['reply_tel'],$reply_tel_list)) $isreplied = 1;
        else
        {
            array_push($reply_tel_list,$row['reply_tel']);
            $isreplied=0;
        }
		$i++;
		if($i==1)
		{
			echo '<table><tr><td>发送流水号</td><td>'.$smsid.'</td><td>内容</td><td>'.$row['sms_content'].'</td><td>发送时间</td><td>'.$row['sms_time'].'</td></tr>';
			$reply_clip = '<tr><td>姓名</td><td>组别</td><td colspan="2">回复内容</td><td>联系电话</td><td>回复时间</td></tr>';
		}
		$query_name = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_group` USING(`user_group`) WHERE `user_tel` = '".$row['reply_tel']."';");
		if(mysql_num_rows($query_name))
		{
			$name_ret = mysql_fetch_array($query_name);
			$name = $name_ret['user_name'];
			$group = $name_ret['user_group_name'];
		}
		else
		{
			$query_name = mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_tel` = '".$row['reply_tel']."';");
			if(mysql_num_rows($query_name))
			{
				$name_ret = mysql_fetch_array($query_name);
				$name = $name_ret['guest_name'];
				$group = $name_ret['guest_type_name'];
			}
		}
		if(!$isreplied) $reply_clip.='<tr><td>'.$name.'</td><td>'.$group.'</td><td colspan="2">'.$row['reply_content'].'</td><td>'.$row['reply_tel'].'</td><td>'.$row['reply_time'].'</td></tr>';
        else $reply_clip.='<tr bgcolor="#FF0000"><td>'.$name.'</td><td>'.$group.'</td><td colspan="2">'.$row['reply_content'].'</td><td>'.$row['reply_tel'].'</td><td>'.$row['reply_time'].'</td></tr>';
	}
    $reply_clip.='<tr><td colspan="3">共'.count($reply_tel_list).'人回复</td><td colspan="3">共'.$i.'条回复记录，重复记录已标红。</td></tr>';
	echo $reply_clip.'<tr><td colspan="6">请使用Ctrl+A全选页面内容并复制，粘贴到Excel中。</td></tr></table>';
}
?>