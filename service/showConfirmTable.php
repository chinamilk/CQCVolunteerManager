<?php
error_reporting(0);
include_once("../conn.php");

$tel = $_POST['tel'];
$page = $_POST['page'];
if(!$page) $page=1;
$start = 10*($page-1);

$query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);

$id = $result['user_id'];

$query = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
$result = mysql_fetch_array($query);
$sign = $result['sign_title'];

$query = mysql_query("SELECT * FROM `user_info` AS i LEFT JOIN `user_sign` s USING(`user_id`) WHERE FIND_IN_SET('".$id."',i.`user_parent`) AND s.`".$sign."` != '0' ORDER BY s.`".$sign."`, LEFT(i.`user_group`,1),LENGTH(i.`user_group`),i.`user_id`;");
$sum = mysql_num_rows($query);
$query = mysql_query("SELECT * FROM `user_info` AS i LEFT JOIN `user_sign` s USING(`user_id`) WHERE FIND_IN_SET('".$id."',i.`user_parent`) AND s.`".$sign."` != '0' ORDER BY s.`".$sign."`, LEFT(i.`user_group`,1),LENGTH(i.`user_group`),i.`user_id` LIMIT ".$start.",10;");
$sum%10==0 ? $total = (int)($sum/10) : $total = (int)($sum/10)+1;
if($sum)
{
	$html = '<div class="table-responsive"><table class="table table-striped">
        <thead>
          <tr><th><input type="checkbox" id="selectAll"></th><th>#</th><th>姓名</th><th>签到状态</th><th>组别</th><th>职务</th><th>联系方式</th><th>评论</th></tr>
        </thead>
        <tbody>';
	$btnDisabled = 1;
	while($row = mysql_fetch_array($query))
	{
		$query_type = mysql_query("SELECT * FROM `user_type` WHERE `user_type` = '".$row['user_type']."';");
		$result = mysql_fetch_array($query_type);
		$type_name = $result['user_type_name'];
			
		$group_name = "";
		for($i=0;$i<strlen(str_replace(" ","",$row['user_group']));$i++)
		{
			$j = $i;
			$query_group = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".substr(str_replace(" ","",$row['user_group']),0,$j+1)."';");
			$result = mysql_fetch_array($query_group);
			$group_name .= $result['user_group_name'].' ';
		}
		$group_name = substr($group_name,0,-1);

		if($row[$sign]=="1")
		{
			$disabled = "";
			$state = '<span class="text-warning"><strong>待确认</strong></span>';
			$btnDisabled = 0;
		}
		else 
		{
			$disabled = " disabled";
			$state = '<span class="text-success"><strong>已确认</strong></span>';
		}
			$html .= '<tr><td><input type="checkbox"'.$disabled.'></td><td>'.$row['user_id'].'</td><td>'.$row['user_name'].'</td><td>'.$state.'</td><td>'.$group_name.'</td><td>'.$type_name.'</td><td>'.$row['user_tel'].'</td><td><input type="text" class="form-control input-sm" placeholder="请输入评论" id="iptAddConfirmComment" name="iptAddConfirmComment"><button class="btn btn-xs btn-primary" id="btnAddConfirmComment">提交</button></td></tr>';
	}
	$btnDisabled?$btnDisabled=" disabled":$btnDisabled="";
	$html.='</tbody>
		    <tfoot>
			  <tr><td colspan="8"><input type="hidden" id="pageConfirmNum" value="'.$page.'">';
		if($total!=1) $html.='<p>转到第<input type="text" class="form-control input-sm" id="pageConfirmInput">页<button class="btn btn-xs btn-primary" id="pageConfirmBtn">GO</button></p>';
		if($page<$total) $html.='<p><a onclick="showConfirmTable('.($page+1).')">下一页</a></p>';
		if($page>1) $html.='<p><a onclick="showConfirmTable('.($page-1).')">上一页</a></p>';
		$html.='<p>第'.$page.'/'.$total.'页</p>';
		if($start+10<=$sum) $html.='<p>第'.($start+1).'-'.($start+10).'/'.$sum.'条记录</p>';
		else $html.='<p>第'.($start+1).'-'.$sum.'/'.$sum.'条记录</p>';
		$html.='</td></tr></tfoot></table></div>
           <div class="form-group">
             <button class="btn btn-lg btn-primary confirm'.$btnDisabled.'">确认签到</button>
		   <div>';
		echo $html;
}
else echo '<div class="alert alert-warning text-left"><strong>请注意！</strong> 暂不存在需确认的签到记录。</div>';
?>