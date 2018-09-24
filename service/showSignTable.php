<?php
include_once("../conn.php");
error_reporting(0);


$tel = trim($_POST['tel']);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];

$query = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$id."';");
if(mysql_num_rows($query))
{
	echo '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
	$query = mysql_query("SHOW FIELDS FROM user_sign;");
	$cnt = 0;
	while($row=mysql_fetch_array($query))
	{
		$cnt++;
		if($cnt==1) continue;
		echo '<th>'.$row['Field'].'</th>';
	}
	echo '</tr></thead><tbody><tr>';
	
	$query = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$id."';");
	$row = mysql_fetch_row($query);
	$cnt = 0;
	foreach ($row as $data)
	{
		$cnt++;
		if($cnt==1) continue;
		if($data=="2") echo '<td><span class="glyphicon glyphicon-ok-sign" style="color:#0C3"></span></td>';
		else if($data=="1") echo '<td><span class="glyphicon glyphicon-question-sign" style="color:#EFCF61"></span></td>';
		else if($data=="0") echo '<td><span class="glyphicon glyphicon-remove-sign" style="color:#EA4044"></span></td>';
	}
	echo '</tr></tbody><tfoot><tr><td colspan="'.($cnt-1).'"></td></tr></tfoot></table></div>';
}
else echo '<div class="alert alert-danger text-left"><strong>错误！</strong> 您之前没有签到记录。</div>';
?>
