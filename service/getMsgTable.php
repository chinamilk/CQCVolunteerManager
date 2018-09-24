<?php
include_once("../conn.php");
error_reporting(0);

$tel = trim($_POST['tel']);

$page = $_POST['page'];

if(!$page) $page=1;
$start = 5*($page-1);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];

$query = mysql_query("SELECT * FROM `msg_info` WHERE FIND_IN_SET('".$id."',`msg_receiver`) ORDER BY `msg_id` DESC;");
$sum = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM `msg_info` WHERE FIND_IN_SET('".$id."',`msg_receiver`) ORDER BY `msg_id` DESC LIMIT ".$start.",5;");
$sum%5==0 ? $total = (int)($sum/5) : $total = (int)($sum/5)+1;
if($sum)
{
	$html = '<div class="table-responsive">
			   <table class="table table-striped small table-condensed text-center">
				 <thead>
				   <tr>
					 <th>#</th>
 					 <th>状态</th>
					 <th>发送人</th>
					 <th width="250">内容</th>
					 <th>发送时间</th>
					 <th>标记</th>
				   </tr>
				 </thead>
				 <tbody>';
	while($row = mysql_fetch_array($query))
	{
		$query_state = mysql_query("SELECT * FROM `msg_info` WHERE FIND_IN_SET('".$id."',`msg_unread`) AND `msg_id` = '".$row['msg_id']."';");
		if(mysql_num_rows($query_state))
		{
			$state = '<span class="glyphicon glyphicon-folder-close" style="color:#F4A81B"></span>';
			$disable = 'btn-success';
		}
		else
		{
			$state = '<span class="glyphicon glyphicon-folder-open" style="color:#888888"></span>';
			$disable = 'btn-default disabled';
		}
		$query_name = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$row['msg_postman']."';");
		if($query_name) $result = mysql_fetch_array($query_name);
		$name = $result['user_name'];
		$html.='<tr><td>'.$row['msg_id'].'</td><td>'.$state.'</td><td>'.$name.'</td><td class="text-left">'.$row['msg_content'].'</td><td>'.$row['msg_time'].'</td><td><button class="btn btn-xs '.$disable.'" onClick="alreadyRead('.$id.','.$row['msg_id'].')">已读</button></td></tr>';
	}
	$html.='</tbody>
			  <tfoot>
				<tr>
				  <td colspan="6"><input type="hidden" id="msgPageNum" value="'.$page.'">';
	if($page<$total) $html.='<p><a onclick="getMsgTable('.($page+1).')">下一页</a></p>';
	if($page>1) $html.='<p><a onclick="getMsgTable('.($page-1).')">上一页</a></p>';
	$html.='<p>第'.$page.'/'.$total.'页</p>';
	if($start+5<=$sum) $html.='<p>第'.($start+1).'-'.($start+5).'/'.$sum.'条记录</p>';
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