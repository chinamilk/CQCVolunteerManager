<?php
include_once("../conn.php");
error_reporting(0);

$content = $_GET['query'];
if($content)
{
	$query = mysql_query("SELECT * FROM (((`event_info` AS i LEFT JOIN `user_group` g1 ON LEFT(i.`event_require`,1) = g1.`user_group`) LEFT JOIN `user_group` g2 ON LEFT(i.`event_require`,2) = g2.`user_group` )LEFT JOIN `user_group` g3 ON LEFT(i.`event_require`,3) = g3.`user_group`) LEFT JOIN `user_group` g4 ON LEFT(i.`event_require`,4) = g4.`user_group` WHERE  CONCAT(g1.`user_group_name`,g2.`user_group_name`,g3.`user_group_name`) LIKE '%".$content."%' OR i.`event_id` LIKE '%".$content."%' ORDER BY LEFT(i.`event_require`,1),LENGTH(i.`event_require`),i.`event_require`,i.`event_id`;");
	$suggestions = array();
	if(mysql_num_rows($query))
	{
		$k = 0;
		while($row = mysql_fetch_array($query))
	    {
			if($row['event_step']=="1") $step = "已录入";
			else if ($row['event_step']=="2") $step = "处理中";
			else $step = "已完结";
			if($row['event_step']=="1" && intval($row['event_post_time'])+15*60 < time()) $alert = '<button class="btn btn-xs btn-danger detail">未处理报警</button>';
			else if($row['event_step']=="2" && intval($row['event_post_time'])+20*60 < time()) $alert = '<button class="btn btn-xs btn-warning detail">未完结报警</button>';
			else $alert = '<button class="btn btn-xs btn-primary detail">查看详情</button>';
			$html = '<tr><td>'.$row['event_id'].'</td><td>'.$row['user_group_name'].'</td><td>'.$row['event_time'].'</td><td>'.$row['event_place'].'</td><td>'.$step.'</td><td>'.$alert.'</td></tr></tbody><tfoot><tr><td colspan="6"></td></tr></tfoot></table>';
			$suggestions[$k] = array('value'=>$row['event_id']." - 需求方：".$row['user_group_name'],'data'=>array('require'=>$row['user_group_name'],'id'=>$row['event_id'],'html'=>$html));
			$k++;
		}
		$return = array('query'=>$content,'suggestions'=>$suggestions);
		echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
	}
	else json_encode(array('query'=>$content,'suggestions'=>$suggestions),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}
else
{
	$content = $_POST['searchEvent'];
	$query = mysql_query("SELECT * FROM `event_info` WHERE `event_id` = '".$content."';");
	if(!mysql_num_rows($query))
	{
		echo json_encode(array('valid' => false),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
	}
	else echo json_encode(array('valid' => true),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}
?>