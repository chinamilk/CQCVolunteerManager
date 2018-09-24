<?php
include("conn.php");
$tel = trim($_POST['tel']);
$content = $_POST['msgContent'];
$receive = $_POST['msgReceive'];
$receive_raw = $receive;
$receiver = preg_split("/,/",$receive);
$rec_list = array();
foreach ($receiver as $value)
{
	$type = substr($value,0,1);
	if($type=="A")
	{
		array_push($rec_list,substr($value,1));
	}
	else if($type=="B")
	{
		$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(`user_group`,".strlen(substr($value,1)).") = '".substr($value,1)."';");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list,$row['user_id']);
			}
		}
	}
	else if($type=="C")
	{
		$query = mysql_query("SELECT * FROM `user_info`;");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list,$row['user_id']);
			}
		}
	}
}
$rec_list = array_flip(array_flip($rec_list));
$receive = implode(",",$rec_list);
$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$postman = $result['user_id'];

$query = mysql_query("INSERT INTO `msg_info` (`msg_id`,`msg_content`,`msg_receiver`,`msg_receiver_raw`,`msg_read`,`msg_unread`,`msg_postman`,`msg_time`) VALUES (NULL,'".$content."','".$receive."','".$receive_raw."','','".$receive."','".$postman."','".date("Y/m/d H:i:s")."');");
if($query) echo "1";

?>
