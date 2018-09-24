<?php
include_once("../conn.php");
error_reporting(0);


$event_id = $_POST['event_id'];
$tel = trim($_POST['tel']);

if($event_id&&$tel)
{
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	$this_group = $result['user_group'];
	
	$query = mysql_query("SELECT * FROM `event_info` WHERE `event_id` = '".$event_id."';");
	if(mysql_num_rows($query))
	{
		$result = mysql_fetch_array($query);
		$require = $result['event_require'];
		$process = $result['event_process'];
		substr($this_group,0,1)==substr($process,0,1)?$isprocess=1:$isprocess=0;
		substr($this_group,0,1)==substr($require,0,1)?$isrequire=1:$isrequire=0;
		$require_p = $result['event_require_person'];
		$process_p = $result['event_process_person'];
		$step = $result['event_step'];
		$postman = $result['event_post_man'];
		$time = $result['event_time'];
		$place = $result['event_place'];
		$content = $result['event_content'];
		$solution = $result['event_solution'];
		
		$query = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".$require."';");
		$row = mysql_fetch_array($query);
		$require = $row['user_group_name'];
		
		$query = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".$process."';");
		$row = mysql_fetch_array($query);
		$process = $row['user_group_name'];
		
		$query = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$require_p."';");
		$row = mysql_fetch_array($query);
		$require_p = $row['user_name'];
		$require_p_tel = $row['user_tel'];
		
		$query = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$process_p."';");
		$row = mysql_fetch_array($query);
		$process_p = $row['user_name'];
		$process_p_tel = $row['user_tel'];

		if($step=="1")
		{
			$step = "已录入";
			$query = mysql_query("SELECT * FROM `user_info` WHERE `user_id` = '".$postman."';");
			$row = mysql_fetch_array($query);
			$postman = $row['user_name'];
			if($solution=="")
			{
				$isprocess?$solution='<button class="btn btn-xs btn-primary" onClick="showEventModal('.$event_id.')">进入处理流程</button>':$solution='<button class="btn btn-xs btn-primary disabled" onClick="showEventModal('.$event_id.') disabled">进入处理流程</button>';
			}
			echo '<table class="table table-striped">
                    <thead>
                      <tr><th colspan="2">详细信息</th></tr>
                    </thead>
                    <tbody class="small">
                      <tr><td width="100">#</td><td>'.$event_id.'</td></tr>
                      <tr><td>需求方</td><td>'.$require.'</td></tr>
                      <tr><td>需求方责任人</td><td>'.$require_p.' - '.$require_p_tel.'</td></tr>
                      <tr><td>时间</td><td>'.$time.'</td></tr>
                      <tr><td>地点</td><td>'.$place.'</td></tr>
                      <tr><td>具体需求</td><td>'.$content.'</td></tr>
                      <tr><td>处理方</td><td>'.$process.'</td></tr>
                      <tr><td>处理方负责人</td><td>'.$process_p.' - '.$process_p_tel.'</td></tr>
                      <tr><td>进度</td><td>'.$step.' - '.$postman.'</td></tr>
                      <tr><td>解决方案</td><td>'.$solution.'</td></tr>
					</tbody>
                    <tfoot>
                      <tr><td colspan="2"></td></tr>
					</tfoot>
                  </table>';
		}
		else if($step=="2")
		{
			$postman_d = explode(" ",$postman,2);
			$query = mysql_query("SELECT `user_name` FROM `user_info` WHERE `user_id` = '".$postman_d[0]."';");
			$row = mysql_fetch_array($query);
			$postman_str = "已录入 - ".$row['user_name']."<br>";
			$query = mysql_query("SELECT `user_name` FROM `user_info` WHERE `user_id` = '".$postman_d[1]."';");
			$row = mysql_fetch_array($query);
			$postman_str .= "处理中 - ".$row['user_name'];
			echo '<table class="table table-striped">
                    <thead>
                      <tr><th colspan="2">详细信息</th></tr>
					</thead>
					<tbody class="small">
					  <tr><td width="100">#</td><td>'.$event_id.'</td></tr>
					  <tr><td>需求方</td><td>'.$require.'</td></tr>
					  <tr><td>需求方责任人</td><td>'.$require_p.' - '.$require_p_tel.'</td></tr>
					  <tr><td>时间</td><td>'.$time.'</td></tr>
					  <tr><td>地点</td><td>'.$place.'</td></tr>
					  <tr><td>具体需求</td><td>'.$content.'</td></tr>
					  <tr><td>处理方</td><td>'.$process.'</td></tr>
                      <tr><td>处理方负责人</td><td>'.$process_p.' - '.$process_p_tel.'</td></tr>
					  <tr><td>进度</td><td>'.$postman_str.'</td></tr>
					  <tr><td>解决方案</td><td>'.$solution.'</td></tr>
					</tbody>
					<tfoot>
					  <tr><td colspan="2"><button class="btn btn-sm btn-success';
					  echo $isprocess?' ':' disabled';
					  echo '" id="finishEvent"';
					  echo $isprocess?' ':' disabled';
					  echo '>完结事件</button></td></tr>
					</tfoot>
				  </table>';
		}
		else if($step=="3")
		{
			$postman_d = explode(" ",$postman,3);
			$query = mysql_query("SELECT `user_name` FROM `user_info` WHERE `user_id` = '".$postman_d[0]."';");
			$row = mysql_fetch_array($query);
			$postman_str = "已录入 - ".$row['user_name']."<br>";
			$query = mysql_query("SELECT `user_name` FROM `user_info` WHERE `user_id` = '".$postman_d[1]."';");
			$row = mysql_fetch_array($query);
			$postman_str .= "处理中 - ".$row['user_name']."<br>";
			$query = mysql_query("SELECT `user_name` FROM `user_info` WHERE `user_id` = '".$postman_d[1]."';");
			$row = mysql_fetch_array($query);
			$postman_str .= "已完结 - ".$row['user_name'];
			echo '<table class="table table-striped">
                    <thead>
                      <tr><th colspan="2">详细信息</th></tr>
					</thead>
					<tbody class="small">
					  <tr><td width="100">#</td><td>'.$event_id.'</td></tr>
					  <tr><td>需求方</td><td>'.$require.'</td></tr>
					  <tr><td>需求方责任人</td><td>'.$require_p.' - '.$require_p_tel.'</td></tr>
					  <tr><td>时间</td><td>'.$time.'</td></tr>
					  <tr><td>地点</td><td>'.$place.'</td></tr>
					  <tr><td>具体需求</td><td>'.$content.'</td></tr>
					  <tr><td>处理方</td><td>'.$process.'</td></tr>
                      <tr><td>处理方负责人</td><td>'.$process_p.' - '.$process_p_tel.'</td></tr>
					  <tr><td>进度</td><td>'.$postman_str.'</td></tr>
					  <tr><td>解决方案</td><td>'.$solution.'</td></tr>
					</tbody>
					<tfoot>
					  <tr><td colspan="2"></td></tr>
					</tfoot>
				  </table>';
		}

	}
	
}

?>