<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);
$page = $_POST['page'];

$query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];

if(!$page) $page=1;
$start = 10*($page-1);

$query = mysql_query("SELECT * FROM `sms_info` m LEFT JOIN `user_info` i ON m.`sms_postman` = i.`user_id` ORDER BY `sms_id` DESC");
$sum = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM `sms_info` m LEFT JOIN `user_info` i ON m.`sms_postman` = i.`user_id` ORDER BY `sms_id` DESC LIMIT ".$start.",10;");

$sum%10==0 ? $total = (int)($sum/10) : $total = (int)($sum/10)+1;
if($sum)
{
	$html = '<div class="table-responsive">
			   <table class="table table-striped small table-condensed" id="showSmsTable">
				 <thead>
				   <tr>
					 <th>#</th>
					 <th>发送人</th>
					 <th width="220">内容</th>
 					 <th>接收者</th>
					 <th>未回复</th>
					 <th>回复内容</th>
					 <th>发送时间</th>
				   </tr>
				 </thead>
				 <tbody>';
	while($row = mysql_fetch_array($query))
	{
		$receiver = preg_split("/,/",$row['sms_receiver_raw']);
		$receiver_raw="";
		foreach ($receiver as $value)
		{
			$type = substr($value,0,1);
			if($type=="A")
			{
				$query_name = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".substr($value,1)."';");
				$result = mysql_fetch_array($query_name);
				$receiver_raw.=$result['user_name']." ";
			}
			else if($type=="B")
			{
				$query_group = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".substr($value,1)."';");
				$result = mysql_fetch_array($query_group);
				$receiver_raw.=$result['user_group_name']." ";
			}
			else if ($type=="C") $receiver_raw="全体志愿者";
			else if ($type=="D")
			{
				$query_name = mysql_query("SELECT * FROM `guest_info` WHERE `guest_id` = '".substr($value,1)."';");
				$result = mysql_fetch_array($query_name);
				$receiver_raw.=$result['guest_name']." ";
			}
			else if($type=="E")
			{
				$query_group = mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_type` = '".substr($value,1)."';");
				$result = mysql_fetch_array($query_group);
				$receiver_raw.=$result['guest_type_name']." ";
			}
			else if ($type=="F") $receiver_raw="全体来宾";
		}
		$unreply_list = "";
		$unreply_clip = "姓名\t组别\t联系电话\n";
		
		foreach(explode(",",$row['sms_unreply']) as $unreply)
		{
			$query_name = mysql_query("SELECT * FROM `user_info` LEFT JOIN `user_group` USING(`user_group`) WHERE `user_id` = '".$unreply."';");
			$result = mysql_fetch_array($query_name);
			$unreply_list.=$result['user_name']." ";
			$unreply_clip.=$result['user_name']."\t".$result['user_group_name']."\t".$result['user_tel']."\n";
		}
		$row['sms_receiver']==""? $receivernum="0": $receivernum=count(explode(",",$row['sms_receiver']));
		$row['sms_unreply']==""? $unreplynum="0": $unreplynum=count(explode(",",$row['sms_unreply']));	
		$html.='<tr>
		  <td>'.$row['sms_id'].'</td>
		  <td>'.$row['user_name'].'</td>
		  <td class="text-left">'.$row['sms_content'].'</td>
		  <td class="text-left">共'.$receivernum.'人 <button type="button" class="btn btn-xs btn-primary popoverSms pull-right" data-toggle="popover" title="查看接收者" data-content="'.$receiver_raw.'" data-trigger="focus" data-placement="left">查看</button></td>
		  <td class="text-left">共'.$unreplynum.'人 <button type="button" class="btn btn-xs btn-primary pull-right" onclick="window.open(\'../service/showUnreplyList.php?id='.urlencode(base64_encode($id)).'&smsid='.urlencode(base64_encode($row['sms_id'])).'\')">查看</button></td>
		  <td class="text-left"><button type="button" class="btn btn-xs btn-primary center-block" onclick="window.open(\'../service/showReplyList.php?id='.urlencode(base64_encode($id)).'&smsid='.urlencode(base64_encode($row['sms_id'])).'\')">查看</button></td>
		  <td>'.$row['sms_time'].'</td>
		</tr>';
	}
	$html.='</tbody>
			  <tfoot>
				<tr>
				  <td colspan="7"><input type="hidden" id="pageNumSmsTable" value="'.$page.'">';
	if($page<$total) $html.='<p><a onclick="showSmsTable('.($page+1).')">下一页</a></p>';
	if($page>1) $html.='<p><a onclick="showSmsTable('.($page-1).')">上一页</a></p>';
	$html.='<p>第'.$page.'/'.$total.'页</p>';
	if($start+10<=$sum) $html.='<p>第'.($start+1).'-'.($start+10).'/'.$sum.'条记录</p>';
	else $html.='<p>第'.($start+1).'-'.$sum.'/'.$sum.'条记录</p>';
	$html.='</td></tr></tfoot></table></div>';
	echo $html;
}
else
{
	$html = '<div class="alert alert-danger text-left"><strong>错误！</strong> 没有找到短信记录。</div></div>';
	echo $html;
}
?>