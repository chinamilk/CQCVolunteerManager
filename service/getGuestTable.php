<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];
$query = mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_user` = '".$id."'");
if(mysql_num_rows($query))
{
	$result = mysql_fetch_array($query);
	$html = '<div class="table-responsive table-condensed"><table class="table table-striped small">
        <thead>
          <tr><th colspan="2" class="text-left">来宾信息详情</th></tr>
        </thead>
        <tbody class="text-left">
		  <tr><td width="200"><strong>姓名</strong></td><td>'.$result['guest_name'].'</td></tr>
		  <tr><td width="200"><strong>联系方式</strong></td><td>'.$result['guest_tel'].'</td></tr>
		  <tr><td><strong>身份</strong></td><td>'.$result['guest_type_name'].'</td></tr>
		  <tr><td><strong>单位</strong></td><td>'.$result['guest_from'].'</td></tr>';
	if(intval($result['guest_type'])<9)
	{
		$result['guest_arrival_time'] = ""?$arrival_time = "未填写" : $arrival_time = $result['guest_arrival_time'];
		$result['guest_arrival_trans'] = ""?$arrival_trans = "未填写" : $arrival_trans = $result['guest_arrival_trans'];
		$result['guest_departure_time'] = ""?$departure_time = "未填写" : $departure_time = $result['guest_departure_time'];
		$result['guest_departure_time'] = ""?$departure_trans = "未填写" : $departure_trans = $result['guest_departure_time'];
		$result['guest_food'] = ""?$food = "未填写" : $food = $result['guest_food'];
		$html.='<tr><td><strong>抵达武汉时间</strong></td><td>'.$arrival_time.'</td></tr>
		        <tr><td><strong>抵达武汉交通信息</strong></td><td>'.$arrival_trans.'</td></tr>
				<tr><td><strong>离开武汉时间</strong></td><td>'.$departure_time.'</td></tr>
		        <tr><td><strong>离开武汉交通信息</strong></td><td>'.$departure_trans.'</td></tr>
				<tr><td><strong>特殊饮食习惯</strong></td><td>'.$food.'</td></tr>';
	}
	$result['guest_other'] = ""?$other = "未填写" : $other = $result['guest_other'];
	$html.='<tr><td><strong>其他信息</strong></td><td>'.$other.'</td></tr>
	        </tbody>
			<tfoot><tr><td colspan="2" class="text-center"><button type="submit" class="btn btn-lg btn-primary center-block" id="guestContent">补充信息</button></td></tr></tfoot></table>';
	echo $html;
}
else
{
	$html = '<div class="alert alert-danger text-left"><strong>错误！</strong> 未找到对应来宾信息。</div>';
	echo $html;
}
?>