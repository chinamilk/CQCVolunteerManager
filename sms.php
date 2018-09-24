<?php
include_once("conn.php");

$tel = trim($_POST['tel']);
$content = $_POST['smsContent'];
$receive = $_POST['smsReceive'];
$receive_raw = $receive;
$receiver = preg_split("/,/",$receive);
$rec_list_volunteer = array(); //待发送志愿者电话号码表
$rec_list_guest = array(); //待发送评委电话号码表
$tel_list = array();

foreach ($receiver as $value)
{
	$type = substr($value,0,1);
	if($type=="A") //志愿者个人
	{
		array_push($rec_list_volunteer,substr($value,1));
	}
	else if($type=="B") //志愿者整组
	{
		$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(`user_group`,".strlen(substr($value,1)).") = '".substr($value,1)."';");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list_volunteer,$row['user_id']);
			}
		}
	}
	else if($type=="C") //志愿者全体
	{
		$query = mysql_query("SELECT * FROM `user_info`;");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list_volunteer,$row['user_id']);
			}
		}
	}
	else if($type=="D") //评委个人
	{
		array_push($rec_list_guest,substr($value,1));
	}
	
	else if($type=="E") //评委整组
	{
		$query = mysql_query("SELECT * FROM `guest_info` WHERE LEFT(`guest_type`,".strlen(substr($value,1)).") = '".substr($value,1)."';");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list_guest,$row['guest_id']);
			}
		}
	}
	
	else if ($type=="F") //评委全体
	{
		$query = mysql_query("SELECT * FROM `guest_info`;");
		if(mysql_num_rows($query))
		{
			while($row = mysql_fetch_array($query))
			{
				array_push($rec_list_guest,$row['guest_id']);
			}
		}
	}
}
if(count($rec_list_volunteer)!=0) $rec_list_volunteer = array_flip(array_flip($rec_list_volunteer));
if(count($rec_list_guest)!=0) $rec_list_guest = array_flip(array_flip($rec_list_guest)); //查重
$total = count($rec_list_volunteer) + count($rec_list_guest);//总待发送条目数
// 形成接收短信电话号码表

if(strpos($content,"%")!==false)
{
	$i = 0;
	$success = 0;
	$receive = "";
	$time = date("Y/m/d H:i:s"); //获得发布时间
	
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$postman = $result['user_id']; //获得发布人
	
	if(count($rec_list_volunteer)!=0)
	{
		foreach ($rec_list_volunteer as $rec)
		{
			$content_every = $content;
			$query_tel = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$rec."';");
			if($query_tel) $tel_ret = mysql_fetch_array($query_tel);
			$receive_tel = $tel_ret['user_tel']; //获得接收手机号
			
			if(strpos($content_every,"%姓名%")!==false) $content_every = str_replace("%姓名%",$tel_ret['user_name'],$content_every);
			if(strpos($content_every,"%类型%")!==false) $content_every = str_replace("%类型%","同学",$content_every);
            if(strpos($content_every,"%来宾%")!==false)
            {
				$query_guest = mysql_query("SELECT * FROM `guest_info` WHERE `guest_user` = '".$rec."';");
                if(mysql_num_rows($query_guest))
				{
					$guest_ret = mysql_fetch_array($query_guest);
                    $content_every = str_replace("%来宾%",$guest_ret['guest_name'],$content_every);
				}
				else
				{
					unset($rec_list_volunteer[array_search($rec,$rec_list_volunteer)]);
					$total -= 1;
					continue;
				}                
            }
			if(strpos($content_every,"%电话%")!==false)
            {
				$query_guest = mysql_query("SELECT * FROM `guest_info` WHERE `guest_user` = '".$rec."';");
                if(mysql_num_rows($query_guest))
				{
					$guest_ret = mysql_fetch_array($query_guest);
                    $content_every = str_replace("%电话%",$guest_ret['guest_tel'],$content_every);
				}
				else
				{
					unset($rec_list_volunteer[array_search($rec,$rec_list_volunteer)]);
					$total -= 1;
					continue;
				}
            }
			if(strpos($content_every,"%团队信息%")!==false)
			{
				$query_guest = mysql_query("SELECT * FROM `guest_info` WHERE `guest_user` = '".$rec."' AND `guest_type` = 2;");
				if(mysql_num_rows($query_guest))
				{
					$guest_ret = mysql_fetch_array($query_guest);
					$query_team = mysql_query("SELECT * FROM `team_plan` LEFT JOIN `guest_info` ON `team_tel` = `guest_tel` WHERE `guest_from` LIKE '".$guest_ret['guest_from']."';");
					if(mysql_num_rows($query_team))
					{
						$team_info = "\n";
						$t = 0;
						while($team_ret = mysql_fetch_array($query_team))
						{
							$t++;
							$team_time = $team_ret['team_time'];
							$team_place = $team_ret['team_place'];
							$team_content = $team_ret['team_content'];
							$team_info.=$t." ".$team_ret['team_name']." ".$team_time." ".$team_place."\n";
						}
						$content_every = str_replace("%团队信息%",$team_info,$content_every);
					}
                    			else
                    {
                    unset($rec_list_volunteer[array_search($rec,$rec_list_volunteer)]);
                    	$total -= 1;
                    	continue;
                    }
				}
                else
				{
					unset($rec_list_volunteer[array_search($rec,$rec_list_volunteer)]);
					$total -= 1;
					continue;
				}
			}
			if(strpos($content_every,"%地点%")!==false || strpos($content_every,"%时间%")!==false || strpos($content_every,"%志愿者%")!==false || strpos($content_every,"%上车时间%")!==false || strpos($content_every,"%上车地点%")!==false || strpos($content_every,"%就餐时间%")!==false || strpos($content_every,"%就餐地点%")!==false)
			{
				unset($rec_list_volunteer[array_search($rec,$rec_list_volunteer)]);
				$total -= 1;
				continue;
			} //更改个性化内容
            
			$i++;
			
			$receive = "A".$rec.","; //形成接收者列表
			
			if($i==1) $query = mysql_query("INSERT INTO `sms_info` (`sms_id`,`sms_taskid`,`sms_content`,`sms_receiver`,`sms_receiver_raw`,`sms_unreply`,`sms_postman`,`sms_time`) VALUES (NULL,NULL,'".$content."','".$receive."','".$receive_raw."','".$receive."','".$postman."','".$time."');");
			else $query = mysql_query("UPDATE `sms_info` SET `sms_receiver` = CONCAT(`sms_receiver`,'".$receive."'),`sms_unreply` = CONCAT(`sms_unreply`,'".$receive."') WHERE `sms_time` = '".$time."';");
			if($i==$total) mysql_query("UPDATE `sms_info` SET `sms_receiver` = LEFT(`sms_receiver`,LENGTH(`sms_receiver`)-1), `sms_unreply` = LEFT(`sms_unreply`,LENGTH(`sms_unreply`)-1) WHERE `sms_time` = '".$time."';");
			
			if($query)
			{
				$f = new SaeFetchurl();
				$f->setMethod("post");
				$f->setPostData(array("action"=>"send","userid"=>UID,"account"=>UNAME,"password"=>PSW,"mobile"=>$receive_tel,"content"=>$content_every."【华中科技大学】","sendTime"=>"","checkcontent"=>"0"));
				$ret = $f->fetch("http://115.238.169.181:7788/sms.aspx");
				$xml = simplexml_load_string($ret);
				if((string)$xml->returnstatus=="Success" || (string)$xml->returnstatus=="success")
				{
					$query_update = mysql_query("UPDATE `sms_info` SET `sms_taskid` = CONCAT(IFNULL(`sms_taskid`,''),'".(string)$xml->taskID.","."') WHERE `sms_time` = '".$time."';");
					if($i==$total) mysql_query("UPDATE `sms_info` SET `sms_taskid` = LEFT(`sms_taskid`,LENGTH(`sms_taskid`)-1) WHERE `sms_time` = '".$time."';");
					if($query_update)
					{
						$state = "1";
						$success+=intval((string)$xml->successCounts);
					}
					else $state = "0";
				}
				else $state = "0";
			}
			else $state = "0";
		}
	}
	
	if(count($rec_list_guest)!=0)
	{
		foreach ($rec_list_guest as $rec)
		{
			$content_every = $content;
			$query_tel = mysql_query("SELECT * FROM `guest_info` WHERE `guest_id` = '".$rec."';");
			if($query_tel) $tel_ret = mysql_fetch_array($query_tel);
			$receive_tel = $tel_ret['guest_tel']; //获得接收手机号
			$receive_from = $tel_ret['guest_from'];
			$guest_type = $tel_ret['guest_type'];
			if($guest_type=="3") $content_type="同学";
			else $content_type = "老师";
            
			if(strpos($content_every,"%团队信息%")!==false)
			{
				if($guest_type=="2")
				{
					$query_team = mysql_query("SELECT * FROM `team_plan`;");
					if(mysql_num_rows($query_team))
					{
						$team_info = "\n";
						$t = 0;
						while($team_ret = mysql_fetch_array($query_team))
						{
							$query_from = mysql_query("SELECT * FROM `guest_info` WHERE `guest_tel` = '".$team_ret['team_tel']."';");
							if(mysql_num_rows($query_from))
							{
								$from_ret = mysql_fetch_array($query_from);
								if($from_ret['guest_from'] == $receive_from)
								{
									$t++;
									$team_name = $team_ret['team_name'];
									$team_time = $team_ret['team_time'];
									$team_place = $team_ret['team_place'];
									$team_info.= $t." ".$team_name." ".$team_time." ".$team_place."\n";
								}
							}
						}
						$content_every = str_replace("%团队信息%",$team_info,$content_every);
					}
				}
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
			}
			if(strpos($content_every,"%姓名%")!==false) $content_every = str_replace("%姓名%",$tel_ret['guest_name'],$content_every);
			if(strpos($content_every,"%类型%")!==false) $content_every = str_replace("%类型%",$content_type,$content_every);
			if(strpos($content_every,"%来宾%")!==false)
			{
				unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
				$total -= 1;
				continue;
			}
			if(strpos($content_every,"%地点%")!==false)
			{
				$query_room = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_room)) $room_ret = mysql_fetch_array($query_room);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
				$content_every = str_replace("%地点%",$room_ret['guest_room'],$content_every);
			}
			if(strpos($content_every,"%时间%")!==false)
			{
				$query_time = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_time)) $time_ret = mysql_fetch_array($query_time);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
				$content_every = str_replace("%时间%",$time_ret['guest_time'],$content_every);
			}
			if(strpos($content_every,"%志愿者%")!==false)
			{
				$query_volunteer = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$tel_ret['guest_user']."';");
				if(mysql_num_rows($query_volunteer)) $vol_ret = mysql_fetch_array($query_volunteer);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}					
				$content_every = str_replace("%志愿者%",$vol_ret['user_name'],$content_every);
			}
			if(strpos($content_every,"%电话%")!==false)
			{
				$query_volunteer = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$tel_ret['guest_user']."';");
				if(mysql_num_rows($query_volunteer)) $vol_ret = mysql_fetch_array($query_volunteer);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}					
				$content_every = str_replace("%电话%",$vol_ret['user_tel'],$content_every);
			}
			if(strpos($content_every,"%上车地点%")!==false)
			{
				$query_room = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_room)) $room_ret = mysql_fetch_array($query_room);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
				$content_every = str_replace("%上车地点%",$room_ret['guest_onboard_place'],$content_every);
			}
			if(strpos($content_every,"%上车时间%")!==false)
			{
				$query_time = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_time)) $time_ret = mysql_fetch_array($query_time);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
				$content_every = str_replace("%上车时间%",$time_ret['guest_onboard_time'],$content_every);
			}
			if(strpos($content_every,"%就餐地点%")!==false)
			{
				$query_room = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_room)) $room_ret = mysql_fetch_array($query_room);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
				$content_every = str_replace("%就餐地点%",$room_ret['guest_dinner_place'],$content_every);
			}
			if(strpos($content_every,"%就餐时间%")!==false)
			{
				$query_time = mysql_query("SELECT * FROM `guest_plan` WHERE `guest_tel` = '".$receive_tel."';");
				if(mysql_num_rows($query_time)) $time_ret = mysql_fetch_array($query_time);
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}					
				$content_every = str_replace("%就餐时间%",$time_ret['guest_dinner_time'],$content_every);
			}//更改个性化内容
			
			if(strpos($content_every,"%抄送%")!==false)
			{
				$query_volunteer = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$tel_ret['guest_user']."';");
				if(mysql_num_rows($query_volunteer))
				{
					$vol_ret = mysql_fetch_array($query_volunteer);
					$receive="B".$rec.",A".$vol_ret['user_id'].",";
					$receive_tel.= ",".$vol_ret['user_tel'];
					$receive_raw.=",A".$vol_ret['user_id'];
					$content_every = str_replace("%抄送%","",$content_every);
				}
				else
				{
					unset($rec_list_guest[array_search($rec,$rec_list_guest)]);
					$total -= 1;
					continue;
				}
			}
			else $receive="B".$rec.","; //形成接收者列表
			$i++;	
			if($i==1) $query = mysql_query("INSERT INTO `sms_info` (`sms_id`,`sms_taskid`,`sms_content`,`sms_receiver`,`sms_receiver_raw`,`sms_unreply`,`sms_postman`,`sms_time`) VALUES (NULL,NULL,'".$content."','".$receive."','".$receive_raw."','".$receive."','".$postman."','".$time."');");
			else $query = mysql_query("UPDATE `sms_info` SET `sms_receiver` = CONCAT(`sms_receiver`,'".$receive."'),`sms_unreply` = CONCAT(`sms_unreply`,'".$receive."') WHERE `sms_time` = '".$time."';");
			if($i==$total) mysql_query("UPDATE `sms_info` SET `sms_receiver` = LEFT(`sms_receiver`,LENGTH(`sms_receiver`)-1), `sms_unreply` = LEFT(`sms_unreply`,LENGTH(`sms_unreply`)-1) WHERE `sms_time` = '".$time."';");
			if($query)
			{
				$f = new SaeFetchurl();
				$f->setMethod("post");
				$f->setPostData(array("action"=>"send","userid"=>UID,"account"=>UNAME,"password"=>PSW,"mobile"=>$receive_tel,"content"=>$content_every."【华中科技大学】","sendTime"=>"","checkcontent"=>"0"));
				$ret = $f->fetch("http://115.238.169.181:7788/sms.aspx");
				$xml = simplexml_load_string($ret);
				if((string)$xml->returnstatus=="Success" || (string)$xml->returnstatus=="success")
				{
					$query_update = mysql_query("UPDATE `sms_info` SET `sms_taskid` = CONCAT(IFNULL(`sms_taskid`,''),'".(string)$xml->taskID.","."') WHERE `sms_time` = '".$time."';");
					if($i==$total) mysql_query("UPDATE `sms_info` SET `sms_taskid` = LEFT(`sms_taskid`,LENGTH(`sms_taskid`)-1) WHERE `sms_time` = '".$time."';");
					if($query_update)
					{
						$state = "1";
						$success+=intval((string)$xml->successCounts);
					}
					else $state = "0";
				}
				else $state = "0";
			}
			else $state = "0";
		}
	}
	if($state != "1")
	{
		$query = mysql_query("DELETE FROM `sms_info` WHERE `sms_time` = '".$time."';");
		$msg = "对不起！发送失败。错误码：".$state;
	}
	else if ($state == "1") $msg="您已成功提交".$success."条短信发送请求。";
	echo json_encode(array("state"=>$state,"msg"=>$msg));
}
else
{
	$receive_tel = "";
	if(count($rec_list_volunteer)!=0)
	{
		foreach ($rec_list_volunteer as $rec)
		{
			$query_tel = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$rec."';");
			if($query_tel) $tel_ret = mysql_fetch_array($query_tel);
			array_push($tel_list,$tel_ret['user_tel']);
		}
	}
    if(count($rec_list_guest)!=0)
	{
		foreach ($rec_list_guest as $rec)
		{
			$query_tel = mysql_query("SELECT * FROM `guest_info` WHERE `guest_id` = '".$rec."';");
			if($query_tel) $tel_ret = mysql_fetch_array($query_tel);
			array_push($tel_list,$tel_ret['guest_tel']);
		}
	}
    $tel_list = array_flip(array_flip($tel_list));
	$receive_tel.= implode(",",$tel_list);
	
    $receive = "";
	if(count($rec_list_volunteer)!=0)
	{
		foreach ($rec_list_volunteer as $rec)
		{
			$receive.="A".$rec.",";
		}
	}
	if(count($rec_list_guest)!=0)
	{
		foreach ($rec_list_guest as $rec)
		{
			$receive.="B".$rec.",";
		}
	}
	$receive = substr($receive,0,-1);
    
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$postman = $result['user_id'];
	$time = date("Y/m/d H:i:s");
	$query = mysql_query("INSERT INTO `sms_info` (`sms_id`,`sms_taskid`,`sms_content`,`sms_receiver`,`sms_receiver_raw`,`sms_unreply`,`sms_postman`,`sms_time`) VALUES (NULL,NULL,'".$content."','".$receive."','".$receive_raw."','".$receive."','".$postman."','".$time."');");
	if($query)
	{	$f = new SaeFetchurl();
		$f->setMethod("post");
		$f->setPostData(
		  array(
			  "action"=> "send" ,
			  "userid"=> UID,
			  "account" => UNAME,
			  "password" => "zd920812",
			  "mobile"=>$receive_tel,
			  "content"=>$content."【华中科技大学】",
			  "sendTime"=>"",
			  "checkcontent"=>"0"
		  )
		); 
		$ret = $f->fetch("http://115.238.169.181:7788/sms.aspx");
		$xml = simplexml_load_string($ret);
		if((string)$xml->returnstatus=="Success")
		{
			$query_update = mysql_query("UPDATE `sms_info` SET `sms_taskid` = '".(string)$xml->taskID."' WHERE `sms_time` = '".$time."';");
			if($query_update)
			{
				$state = "1";
				$msg = "您已成功提交".(string)$xml->successCounts."条短信发送请求。";
			}
			else
			{
				$state = "0";
				$msg = "数据库写入错误。";
			}
		}
		else 
		{
			$state = "0";
			$msg = "调用发送接口错误。".(string)$xml->returnstatus.(string)$xml->message;
		}
		if($state == "0") $query = mysql_query("DELETE FROM `sms_info` WHERE `sms_time` = '".$time."'");
		echo json_encode(array("state"=>$state,"msg"=>$msg));
	}
}
?>