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
	$query = mysql_query("SELECT * FROM `user_info` ORDER BY LEFT(`user_group`,1), LENGTH(`user_group`),`user_type` DESC;");
	$sum = mysql_num_rows($query);
	$query = mysql_query("SELECT * FROM `user_info` ORDER BY LEFT(`user_group`,1), LENGTH(`user_group`),`user_type` DESC LIMIT ".$start.",10;");
}
else
{
	$group = substr($group_r,0,1);
	$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(`user_group`,1) = '".$group."' ORDER BY LENGTH(`user_group`),`user_type` DESC;");
	$sum = mysql_num_rows($query);
	$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(`user_group`,1) = '".$group."' ORDER BY LENGTH(`user_group`),`user_type` DESC LIMIT ".$start.",10;");
}
$sum%10==0 ? $total = (int)($sum/10) : $total = (int)($sum/10)+1;
if($sum)
{
	$html='<table class="table table-striped">
		    <thead>
			  <tr><th>#</th><th>姓名</th><th>签到信息</th><th>组别</th><th>职务</th><th>联系方式</th><th>来宾信息</th></tr>
		    </thead>
		    <tbody>';
	while($row = mysql_fetch_array($query))
	{
		$group_name = "";
		for($i=0;$i<strlen(str_replace(" ","",$row['user_group']));$i++)
		{
			$j = $i;
			$query_group = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".substr(str_replace(" ","",$row['user_group']),0,$j+1)."';");
			$result = mysql_fetch_array($query_group);
			$group_name .= $result['user_group_name'].' ';
		}
		$group_name = substr($group_name,0,-1);
		$query_sign = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
		$sign_result = mysql_fetch_array($query_sign);
		$query_sign = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$row['user_id']."';");
		if(!mysql_num_rows($query_sign)) $issign = '<span class="text-danger"><strong>未签到</strong></span>';
		else 
		{
			$result = mysql_fetch_array($query_sign);
			switch($result[$sign_result['sign_title']])
			{
				case '0': $issign = '<span class="text-danger"><strong>未签到</strong></span>';break;
				case '1': $issign = '<span class="text-warning"><strong>待确认</strong></span>';break;
				case '2': $issign = '<span class="text-success"><strong>已确认</strong></span>';break;
				default:break;
			}
		}
		$query_type = mysql_query("SELECT * FROM `user_type` WHERE `user_type` = '".$row['user_type']."';");
		$result = mysql_fetch_array($query_type);
		$type_name = $result['user_type_name'];
		$query_guest= mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_user` = '".$row['user_id']."';");
		if(mysql_num_rows($query_guest)) $guest_html = '<button class="btn btn-xs btn-primary" type="button" data-toggle="modal" data-target="#showGuestModal">查看</button></td>';
		else $guest_html = '';
		$html.='<tr><td>'.$row['user_id'].'</td><td>'.$row['user_name'].'</td><td>'.$issign.'</td><td>'.$group_name.'</td><td>'.$type_name.'</td><td>'.$row['user_tel'].'</td><td>'.$guest_html.'</td></tr>';
	}
	$html.='</tbody>
		    <tfoot>
			  <tr><td colspan="7"><input type="hidden" id="pagePersonNum" value="'.$page.'">';
	if($total!=1) $html.='<p>转到第<input type="text" class="form-control input-sm" id="pagePersonInput">页<button class="btn btn-xs btn-primary" id="pagePersonBtn">GO</button></p>';
	if($page<$total) $html.='<p><a onclick="showPersonTable('.($page+1).')">下一页</a></p>';
	if($page>1) $html.='<p><a onclick="showPersonTable('.($page-1).')">上一页</a></p>';
	$html.='<p>第'.$page.'/'.$total.'页</p>';
	if($start+10<=$sum) $html.='<p>第'.($start+1).'-'.($start+10).'/'.$sum.'条记录</p>';
	else $html.='<p>第'.($start+1).'-'.$sum.'/'.$sum.'条记录</p>';
	$html.='</td></tr></tfoot></table>';
	echo $html;
}
else
{
	$html = '<div class="alert alert-warning text-left"><strong>错误！</strong> 没有找到人员记录。</div></div>';
	echo $html;
}

?>