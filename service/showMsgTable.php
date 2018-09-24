<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);
$page = $_POST['page'];

$query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);

if(!$page) $page=1;
$start = 10*($page-1);

$query = mysql_query("SELECT * FROM `msg_info` m LEFT JOIN `user_info` i ON m.`msg_postman` = i.`user_id` ORDER BY `msg_id` DESC");
$sum = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM `msg_info` m LEFT JOIN `user_info` i ON m.`msg_postman` = i.`user_id` ORDER BY `msg_id` DESC LIMIT ".$start.",10;");

$sum%10==0 ? $total = (int)($sum/10) : $total = (int)($sum/10)+1;
if($sum)
{
	$html = '<div class="table-responsive">
			   <table class="table table-striped small table-condensed" id="showMsgTable">
				 <thead>
				   <tr>
					 <th>#</th>
					 <th>发送人</th>
					 <th width="250">内容</th>
 					 <th>接收者</th>
					 <th>已读</th>
					 <th>未读</th>
					 <th>未读人名单</th>
					 <th>发送时间</th>
				   </tr>
				 </thead>
				 <tbody>';
	while($row = mysql_fetch_array($query))
	{
		$receiver = preg_split("/,/",$row['msg_receiver_raw']);
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
			else if ($type=="C") $receiver_raw="全体";
		}
		$unread_name = "";
		$row['msg_read']==""? $readnum="0": $readnum=count(explode(",",$row['msg_read']));
		$row['msg_unread']==""? $unreadnum="0": $unreadnum=count(explode(",",$row['msg_unread']));
		foreach(explode(",",$row['msg_unread']) as $unread)
		{
			$query_name = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$unread."';");
			$result = mysql_fetch_array($query_name);
			$unread_name.=$result['user_name']." ";
		}
		$html.='<tr>
				  <td>'.$row['msg_id'].'</td>
				  <td>'.$row['user_name'].'</td>
				  <td class="text-left">'.$row['msg_content'].'</td>
				  <td><button type="button" class="btn btn-xs btn-primary popoverMsg" data-toggle="popover" title="查看接收者名单" data-content="'.$receiver_raw.'" data-trigger="focus" data-placement="left">查看</button></td>
				  <td>'.$readnum.'</td>
				  <td>'.$unreadnum.'</td>
				  <td><button type="button" class="btn btn-xs btn-primary popoverMsg" data-toggle="popover" title="查看未读人名单" data-content="'.$unread_name.'" data-trigger="focus" data-placement="left">查看</button></td>
				  <td>'.$row['msg_time'].'</td>
				</tr>';
	}
	$html.='</tbody>
			  <tfoot>
				<tr>
				  <td colspan="8"><input type="hidden" id="pageNumMsgTable" value="'.$page.'">';
	if($page<$total) $html.='<p><a onclick="showMsgTable('.($page+1).')">下一页</a></p>';
	if($page>1) $html.='<p><a onclick="showMsgTable('.($page-1).')">上一页</a></p>';
	$html.='<p>第'.$page.'/'.$total.'页</p>';
	if($start+10<=$sum) $html.='<p>第'.($start+1).'-'.($start+10).'/'.$sum.'条记录</p>';
	else $html.='<p>第'.($start+1).'-'.$sum.'/'.$sum.'条记录</p>';
	$html.='</td></tr></tfoot></table></div>';
	echo $html;
}
else
{
	$html = '<div class="alert alert-danger text-left"><strong>错误！</strong> 没有找到消息记录。</div></div>';
	echo $html;
}
?>