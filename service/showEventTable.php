<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);
$page = $_POST['page'];

$query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);

$group_r = $result['user_group'];
$type_r = $result['user_type'];

if(!$page) $page=1;
$start = 10*($page-1);
if($type_r=="9" ||($type_r=="3" && $group_r=="111"))
{
	$query = mysql_query("SELECT * FROM `event_info` ORDER BY `event_step` ,`event_post_time` DESC;");
	$sum = mysql_num_rows($query);
	$query = mysql_query("SELECT * FROM `event_info` ORDER BY `event_step` ,`event_post_time` DESC LIMIT ".$start.",10;");
}
else
{
	$group = substr($group_r,0,1);
	$query = mysql_query("SELECT * FROM `event_info` WHERE LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."' ORDER BY `event_step` ,`event_post_time` DESC;");
	$sum = mysql_num_rows($query);
	$query = mysql_query("SELECT * FROM `event_info` WHERE LEFT(`event_require`,1) = '".$group."' OR LEFT(`event_process`,1) = '".$group."' ORDER BY `event_step` ,`event_post_time` DESC LIMIT ".$start.",10;");
}	
$sum%10==0 ? $total = (int)($sum/10) : $total = (int)($sum/10)+1;
if($sum)
{
	$html = '<div class="table-responsive">
			   <table class="table table-striped" id="showEvent">
				 <thead>
				   <tr>
					 <th>#</th>
					 <th>需求方</th>
					 <th>时间</th>
					 <th>地点</th>
					 <th>进度</th>
					 <th></th>
				   </tr>
				 </thead>
				 <tbody>';
	while($row = mysql_fetch_array($query))
	{
		$query_group = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".$row['event_require']."';");
		if($query_group) $result = mysql_fetch_array($query_group); 
		if($row['event_step']=="1") $step = "已录入";
		else if ($row['event_step']=="2") $step = "处理中";
		else $step = "已完结";
		if($row['event_step']=="1" && intval($row['event_post_time'])+15*60 < time()) $alert = '<button class="btn btn-xs btn-danger detail">未处理报警</button>';
		else if($row['event_step']=="2" && intval($row['event_post_time'])+20*60 < time()) $alert = '<button class="btn btn-xs btn-warning detail">未完结报警</button>';
		else $alert = '<button class="btn btn-xs btn-primary detail">查看详情</button>';
		$html.='<tr>
				  <td>'.$row['event_id'].'</td>
				  <td>'.$result['user_group_name'].'</td>
				  <td>'.$row['event_time'].'</td>
				  <td>'.$row['event_place'].'</td>
				  <td>'.$step.'</td>
				  <td>'.$alert.'</td>
				</tr>';
	}
	$html.='</tbody>
			  <tfoot>
				<tr>
				  <td colspan="6"><input type="hidden" id="pageNum" value="'.$page.'">';
	if($total!=1) $html.='<p>转到第<input type="text" class="form-control input-sm" id="pageEventInput">页<button class="btn btn-xs btn-primary" id="pageEventBtn">GO</button></p>';
	if($page<$total) $html.='<p><a onclick="showEventTable('.($page+1).')">下一页</a></p>';
	if($page>1) $html.='<p><a onclick="showEventTable('.($page-1).')">上一页</a></p>';
	$html.='<p>第'.$page.'/'.$total.'页</p>';
	if($start+10<=$sum) $html.='<p>第'.($start+1).'-'.($start+10).'/'.$sum.'条记录</p>';
	else $html.='<p>第'.($start+1).'-'.$sum.'/'.$sum.'条记录</p>';
	$html.='</td></tr></tfoot></table></div>';
	echo $html;
}
else
{
	$html = '<div class="alert alert-danger text-left"><strong>错误！</strong> 没有找到事件记录。</div></div>';
	echo $html;
}
?>